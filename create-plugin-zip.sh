#!/usr/bin/env bash
#
# WordPress "Installation failed. No valid plugins were found" usually means: missing bootstrap
# PHP in the zip root folder, or the archive has no single top-level directory, or a stale zip was
# re-used (zip -r merges). This script rm -f's the output zip first; verify_zip_output checks structure.
#
# LandTech Extras for Elementor — bootstrap file: landtech-extras.php (not committed copies under scripts/).
#
# Optional LMFWC bake (private/premium distribution zips only): if you maintain a separate premium build,
# set LANDTECH_EXTRAS_PREMIUM_BUILD_CONSUMER_KEY / _SECRET or use .lmfwc-credentials.local and
# scripts/inject-lmfwc-credentials-for-zip.php. WordPress.org releases must not ship custom licensing
# or update clients in this tree.
#
# Usage:
#   ./create-plugin-zip.sh                # writes DIST/landtech-extras-<version>.zip (inner folder = plugin slug, no version)
#   ./create-plugin-zip.sh -o ~/out.zip
#
# Optional:
#   LANDTECH_EXTRAS_ZIP_FILE_BASENAME  Override zip filename stem (default: landtech-extras → landtech-extras-2.x.x.zip)
#   LANDTECH_EXTRAS_ZIP_INNER_FOLDER   Override folder name inside the zip (default: repo directory basename; never versioned)
#   LANDTECH_EXTRAS_ZIP_OUT_DIR          Override output directory (default: ./DIST)
#
# CSS note: WordPress loads assets/css/frontend.min.css (and frontend-rtl.min.css) unless
# SCRIPT_DEBUG is true. After editing frontend.css / frontend-rtl.css, regenerate the .min
# files before zipping, e.g.:
#   npx --yes clean-css-cli -o assets/css/frontend.min.css assets/css/frontend.css
#   npx --yes clean-css-cli -o assets/css/frontend-rtl.min.css assets/css/frontend-rtl.css
# The script warns if a source .css is newer than its .min.css peer.
#
# Optional: LANDTECH_EXTRAS_ZIP_FAIL_ON_STALE_MIN=1 aborts the zip when minified CSS or frontend.min.js is stale
# (override with LANDTECH_EXTRAS_ZIP_ALLOW_STALE_MIN=1).
#

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
_REPO_BASENAME="$(basename "$SCRIPT_DIR")"
PLUGIN_SLUG="${LANDTECH_EXTRAS_ZIP_INNER_FOLDER:-$_REPO_BASENAME}"
ZIP_FILE_BASE="${LANDTECH_EXTRAS_ZIP_FILE_BASENAME:-landtech-extras}"
DIST_DIR="${LANDTECH_EXTRAS_ZIP_OUT_DIR:-${SCRIPT_DIR}/DIST}"
TEMP_DIR="${SCRIPT_DIR}/.zip-pack-$$"
TEMP_PLUGIN="${TEMP_DIR}/${PLUGIN_SLUG}"
INJECT_SCRIPT="${SCRIPT_DIR}/scripts/inject-lmfwc-credentials-for-zip.php"

read_version() {
	local main_file="${SCRIPT_DIR}/landtech-extras.php"
	if [[ ! -f "${main_file}" ]]; then
		echo "unknown"
		return
	fi
	grep -m1 -E '^\s*\*\s*Version:' "${main_file}" | sed -E 's/.*Version:[[:space:]]+//' | tr -d '\r\n' || echo "unknown"
}

# Verify the temp tree contains files required at runtime and catch stale minified CSS.
verify_pack_artifacts() {
	local base="${TEMP_PLUGIN}"
	local 	req=(
		"landtech-extras.php"
		"includes/landtech-extras-bootstrap-guard.php"
		"readme.txt"
		"assets/blueprints/blueprint.json"
		"modules/posts/widgets/posts.php"
		"includes/plugin.php"
		"assets/css/frontend.css"
		"assets/css/frontend.min.css"
		"assets/css/frontend-rtl.css"
		"assets/css/frontend-rtl.min.css"
		"modules/posts/skins/presets/skin-posts-preset-base.php"
	)
	local f
	for f in "${req[@]}"; do
		if [[ ! -f "${base}/${f}" ]]; then
			echo "Error: required file missing from pack: ${f}" >&2
			exit 1
		fi
	done
	local pc
	pc="$(find "${base}/modules/posts/skins/presets" -maxdepth 1 -type f -name 'skin-posts-*.php' 2>/dev/null | wc -l | tr -d ' ')"
	if [[ "${pc}" -lt 11 ]]; then
		echo "Error: expected 11 preset/skin PHP files in modules/posts/skins/presets/, found ${pc}" >&2
		exit 1
	fi
	local pair src dst
	for pair in \
		"assets/css/frontend.css:assets/css/frontend.min.css" \
		"assets/css/frontend-rtl.css:assets/css/frontend-rtl.min.css"; do
		src="${base}/${pair%%:*}"
		dst="${base}/${pair##*:}"
		if [[ -f "${src}" && -f "${dst}" && "${src}" -nt "${dst}" ]]; then
			echo "WARNING: ${pair##*:} is older than ${pair%%:*}. Rebuild minified CSS or the zip ships stale styles when SCRIPT_DEBUG is off (Elementor loads .min)." >&2
			if [[ -n "${LANDTECH_EXTRAS_ZIP_FAIL_ON_STALE_MIN:-}" && "${LANDTECH_EXTRAS_ZIP_ALLOW_STALE_MIN:-}" != "1" ]]; then
				echo "Abort: set LANDTECH_EXTRAS_ZIP_ALLOW_STALE_MIN=1 to override, or regenerate .min files." >&2
				exit 1
			fi
		fi
	done
	local jspair jssrc jsdst
	for jspair in \
		"assets/js/frontend.js:assets/js/frontend.min.js"; do
		jssrc="${base}/${jspair%%:*}"
		jsdst="${base}/${jspair##*:}"
		if [[ -f "${jssrc}" && -f "${jsdst}" && "${jssrc}" -nt "${jsdst}" ]]; then
			echo "WARNING: ${jspair##*:} is older than ${jspair%%:*}. Rebuild minified JS or SCRIPT_DEBUG=false sites load stale bundles." >&2
			if [[ -n "${LANDTECH_EXTRAS_ZIP_FAIL_ON_STALE_MIN:-}" && "${LANDTECH_EXTRAS_ZIP_ALLOW_STALE_MIN:-}" != "1" ]]; then
				echo "Abort: set LANDTECH_EXTRAS_ZIP_ALLOW_STALE_MIN=1 to override, or regenerate frontend.min.js." >&2
				exit 1
			fi
		fi
	done
}

# Confirm the archive is WordPress-installable: one root folder, bootstrap file, Plugin Name header.
verify_zip_output() {
	local z="${OUT_ZIP}"
	local main_relpath="${ZIP_PREFIX}/landtech-extras.php"
	if [[ ! -f "${z}" ]]; then
		echo "Error: zip was not created: ${z}" >&2
		exit 1
	fi
	if ! unzip -tq "${z}" >/dev/null; then
		echo "Error: zip integrity test failed: ${z}" >&2
		exit 1
	fi
	# Use grep -c (not -q) so the zip listing pipe is drained under bash pipefail;
	# otherwise unzip can get SIGPIPE when grep exits early after the first match.
	found_count="$(unzip -Z1 "${z}" 2>/dev/null | grep -Fxc "${main_relpath}" || true)"
	if [[ "${found_count}" -ne 1 ]]; then
		echo "Error: ${main_relpath} missing from zip (WordPress reports no valid plugin when bootstrap file is absent)." >&2
		exit 1
	fi
	local uniq_root_count
	uniq_root_count="$(unzip -Z1 "${z}" 2>/dev/null | awk -F/ 'NF >= 1 && $1 != "" { print $1 }' | sort -u | wc -l | tr -d ' ')"
	if [[ "${uniq_root_count}" != "1" ]]; then
		echo "Error: zip must contain exactly one top-level directory (found ${uniq_root_count}). Check excludes or temp tree." >&2
		exit 1
	fi
	local r1
	r1="$(unzip -Z1 "${z}" 2>/dev/null | awk -F/ 'NF >= 1 && $1 != "" { print $1; exit }')"
	if [[ "${r1}" != "${ZIP_PREFIX}" ]]; then
		echo "Error: zip root folder is \"${r1}\", expected \"${ZIP_PREFIX}\" (set LANDTECH_EXTRAS_ZIP_INNER_FOLDER to match repo layout)." >&2
		exit 1
	fi
	local hdr
	hdr="$(unzip -p "${z}" "${main_relpath}" 2>/dev/null | head -n 45 || true)"
	if ! grep -q 'Plugin Name:' <<< "${hdr}"; then
		echo "Error: ${main_relpath} has no Plugin Name header in zip (WordPress ignores such packages)." >&2
		exit 1
	fi
}

VERSION="$(read_version)"
DEFAULT_OUT="${DIST_DIR}/${ZIP_FILE_BASE}-${VERSION}.zip"
OUT_ZIP="${DEFAULT_OUT}"

while [[ "${1:-}" == -* ]]; do
	case "${1:-}" in
		-o | --output)
			OUT_ZIP="${2:?--output requires a path}"
			shift 2
			;;
		-h | --help)
			echo "Usage: $0 [-o|--output PATH]"
			echo "Default output: DIST/<basename>-<version>.zip (version from plugin header; inner folder is plugin slug only)."
			echo "Env: LANDTECH_EXTRAS_ZIP_FILE_BASENAME, LANDTECH_EXTRAS_ZIP_INNER_FOLDER, LANDTECH_EXTRAS_ZIP_OUT_DIR,"
			echo "    LANDTECH_EXTRAS_ZIP_FAIL_ON_STALE_MIN=1 (optional), LANDTECH_EXTRAS_ZIP_ALLOW_STALE_MIN=1"
			exit 0
			;;
		*)
			echo "Unknown option: $1" >&2
			exit 1
			;;
	esac
done

OUT_DIR="$(dirname "${OUT_ZIP}")"
mkdir -p "${OUT_DIR}"

cleanup() {
	if [[ -d "${TEMP_DIR}" ]]; then
		rm -rf "${TEMP_DIR}"
	fi
}
trap cleanup EXIT

if ! command -v rsync >/dev/null 2>&1; then
	echo "Error: rsync is required." >&2
	exit 1
fi
if ! command -v zip >/dev/null 2>&1; then
	echo "Error: zip not found." >&2
	exit 1
fi
if ! command -v unzip >/dev/null 2>&1; then
	echo "Error: unzip is required (post-build validation)." >&2
	exit 1
fi

mkdir -p "${TEMP_PLUGIN}"

rsync -a \
	--exclude='.zip-pack-*' \
	--exclude='DIST/' \
	--exclude='.git/' \
	--exclude='.github/' \
	--exclude='.gitattributes' \
	--exclude='.cursorrules' \
	--exclude='.cursor/' \
	--exclude='.idea/' \
	--exclude='.vscode/' \
	--exclude='node_modules/' \
	--exclude='vendor/' \
	--exclude='assets/src/' \
	--exclude='tests/' \
	--exclude='*.zip' \
	--exclude='.lmfwc-credentials.local' \
	--exclude='.distignore' \
	--exclude='.release' \
	--exclude='scripts/' \
	--exclude='.plugin-check/' \
	"${SCRIPT_DIR}/" "${TEMP_PLUGIN}/"

verify_pack_artifacts

LMFWC_KEY_INJECT=""
LMFWC_SECRET_INJECT=""
if [[ -n "${LANDTECH_EXTRAS_PREMIUM_BUILD_CONSUMER_KEY:-}" && -n "${LANDTECH_EXTRAS_PREMIUM_BUILD_CONSUMER_SECRET:-}" ]]; then
	LMFWC_KEY_INJECT="${LANDTECH_EXTRAS_PREMIUM_BUILD_CONSUMER_KEY}"
	LMFWC_SECRET_INJECT="${LANDTECH_EXTRAS_PREMIUM_BUILD_CONSUMER_SECRET}"
elif [[ -r "${SCRIPT_DIR}/.lmfwc-credentials.local" ]]; then
	while IFS= read -r raw || [[ -n "${raw}" ]]; do
		line="${raw#"${raw%%[![:space:]]*}"}"
		line="${line%"${line##*[![:space:]]}"}"
		line="${line//$'\r'/}"
		[[ -z "${line}" ]] && continue
		[[ "${line}" == \#* ]] && continue
		if [[ -z "${LMFWC_KEY_INJECT}" ]]; then
			LMFWC_KEY_INJECT="${line}"
		elif [[ -z "${LMFWC_SECRET_INJECT}" ]]; then
			LMFWC_SECRET_INJECT="${line}"
			break
		fi
	done < "${SCRIPT_DIR}/.lmfwc-credentials.local"
fi

if [[ -f "${INJECT_SCRIPT}" ]]; then
	if [[ -n "${LMFWC_KEY_INJECT}" && -n "${LMFWC_SECRET_INJECT}" ]]; then
		echo "LMFWC inject: key length ${#LMFWC_KEY_INJECT}, secret length ${#LMFWC_SECRET_INJECT} (sizes only)."
		export LANDTECH_EXTRAS_PREMIUM_BUILD_CONSUMER_KEY="${LMFWC_KEY_INJECT}"
		export LANDTECH_EXTRAS_PREMIUM_BUILD_CONSUMER_SECRET="${LMFWC_SECRET_INJECT}"
		export LANDTECH_EXTRAS_PREMIUM_BUILD_BLOB_KEY="${LANDTECH_EXTRAS_PREMIUM_BUILD_BLOB_KEY:-}"
		export LANDTECH_EXTRAS_PREMIUM_BUILD_PLAINTEXT_IN_ZIP="${LANDTECH_EXTRAS_PREMIUM_BUILD_PLAINTEXT_IN_ZIP:-}"
		if php "${INJECT_SCRIPT}" "${TEMP_PLUGIN}"; then
			echo "Injected LMFWC_BAKED_INJECT_BLOCK into temp landtech-extras.php"
		else
			echo "LMFWC injection failed." >&2
			exit 1
		fi
	else
		echo "No LANDTECH_EXTRAS_PREMIUM_BUILD_* env or readable .lmfwc-credentials.local — zip uses empty baked defaults (enter REST keys on site or rebuild)."
	fi
else
	echo "Missing ${INJECT_SCRIPT} — zip will not bake LMFWC credentials."
fi

ZIP_PREFIX="${PLUGIN_SLUG}"
declare -a JUNK_EXCLUDES=()
while IFS= read -r -d '' path; do
	rel="${path#"${TEMP_DIR}/"}"
	JUNK_EXCLUDES+=( -x "$rel" )
done < <(find "${TEMP_PLUGIN}" \( \
	-name '.DS_Store' \
	-o -name 'Thumbs.db' \
	-o -name '.localized' \
	-o -path '*/.AppleDouble/*' \
\) -print0 2>/dev/null)

if [[ -d "${TEMP_PLUGIN}/__MACOSX" ]]; then
	JUNK_EXCLUDES+=( -x "${ZIP_PREFIX}/__MACOSX/*" )
fi

junk_count=$(( ${#JUNK_EXCLUDES[@]} / 2 ))
echo "Building plugin zip into: ${DIST_DIR}/"
echo "Creating: ${OUT_ZIP}"
echo " Root folder in archive: ${ZIP_PREFIX}/ (no version suffix in extracted path)"
echo " Version from header: ${VERSION}"
if [[ "${junk_count}" -gt 0 ]]; then
	echo " Excluding ${junk_count} Finder/Explorer junk path(s)."
fi

# zip -r updates existing archives without removing deleted paths; always start fresh.
rm -f "${OUT_ZIP}"

( cd "${TEMP_DIR}" && zip -r "${OUT_ZIP}" "${ZIP_PREFIX}" \
	"${JUNK_EXCLUDES[@]}" \
	-x "${ZIP_PREFIX}/.cursorrules" \
	-x "${ZIP_PREFIX}/.cursor/*" \
	-x "${ZIP_PREFIX}/.git/*" \
	-x "${ZIP_PREFIX}/.git" \
	-x "${ZIP_PREFIX}/.github/*" \
	-x "${ZIP_PREFIX}/node_modules/*" \
	-x "${ZIP_PREFIX}/vendor/*" \
	-x "${ZIP_PREFIX}/assets/src/*" \
	-x "${ZIP_PREFIX}/tests/*" \
	-x "${ZIP_PREFIX}/phpunit.xml" \
	-x "${ZIP_PREFIX}/phpunit.xml.dist" \
	-x "${ZIP_PREFIX}/phpcs.xml" \
	-x "${ZIP_PREFIX}/phpcs.xml.dist" \
	-x "${ZIP_PREFIX}/phpcs.security.xml" \
	-x "${ZIP_PREFIX}/.deploy-trigger" \
	-x "${ZIP_PREFIX}/.deploy-test" \
	-x "${ZIP_PREFIX}/scripts/*" \
	-x "${ZIP_PREFIX}/*.md" \
	-x "${ZIP_PREFIX}/admin/*.md" \
	-x "${ZIP_PREFIX}/includes/*.md" \
	-x "${ZIP_PREFIX}/modules/*.md" \
	-x "${ZIP_PREFIX}/composer.json" \
	-x "${ZIP_PREFIX}/composer.lock" \
	-x "${ZIP_PREFIX}/package.json" \
	-x "${ZIP_PREFIX}/package-lock.json" \
	-x "${ZIP_PREFIX}/yarn.lock" \
	-x "${ZIP_PREFIX}/pnpm-lock.yaml" \
	-x "${ZIP_PREFIX}/lmfwc-credentials.local.example" \
	-x "${ZIP_PREFIX}/.gitignore" \
	-x "${ZIP_PREFIX}/.distignore" \
	-x "${ZIP_PREFIX}/.release" \
	-x "${ZIP_PREFIX}/README.txt" \
	-x "${ZIP_PREFIX}/create-plugin-zip.sh" \
	-x "${ZIP_PREFIX}/scripts/rebrand-to-landtech.py" \
	-x "${ZIP_PREFIX}/*.sh" \
)

verify_zip_output

echo "Done. Upload via Plugins → Add New → Upload Plugin:"
ls -lh "${OUT_ZIP}"
