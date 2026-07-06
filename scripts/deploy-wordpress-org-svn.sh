#!/usr/bin/env bash
#
# Deploy LandTech Extras for Elementor (WordPress.org free build) to plugins.svn.wordpress.org.
#
# Populates trunk/, creates tags/<version>/ from trunk, and refreshes plugin-directory assets/
# (icon/banner PNGs generated from .plugin-check/.wordpress-org/*.svg).
#
# File set mirrors create-plugin-zip.sh (same rsync excludes; no LMFWC inject; scripts/ excluded).
#
# Usage:
#   ./scripts/deploy-wordpress-org-svn.sh              # sync + stage; print svn status
#   ./scripts/deploy-wordpress-org-svn.sh --commit       # sync, stage, and svn commit
#   ./scripts/deploy-wordpress-org-svn.sh --checkout     # create SVN working copy if missing
#   ./scripts/deploy-wordpress-org-svn.sh --dry-run      # preview rsync only
#
# Options:
#   --checkout       svn checkout into LANDTECH_EXTRAS_SVN_DIR when the directory is missing
#   --commit         Run svn commit after staging
#   --message TEXT   Commit message (default includes version)
#   --skip-assets    Do not regenerate assets/icon-*.png and assets/banner-*.png
#   --skip-tag       Update trunk only (no tags/<version>/)
#   --dry-run        rsync --dry-run (no SVN writes)
#   -h, --help       Show help
#
# Environment:
#   LANDTECH_EXTRAS_SVN_DIR   SVN working copy (default: ../landtech-extras-for-elementor-svn)
#
# Requires: svn, rsync, sips (macOS) for SVG→PNG assets. WordPress.org SVN credentials for --commit.
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SVN_URL="https://plugins.svn.wordpress.org/landtech-extras-for-elementor"
SVN_DIR="${LANDTECH_EXTRAS_SVN_DIR:-${SCRIPT_DIR}/../landtech-extras-for-elementor-svn}"
ORG_ASSET_SRC="${SCRIPT_DIR}/.plugin-check/.wordpress-org"

DO_CHECKOUT=0
DO_COMMIT=0
DO_DRY_RUN=0
SKIP_ASSETS=0
SKIP_TAG=0
COMMIT_MESSAGE=""

usage() {
	sed -n '2,28p' "$0" | sed 's/^# \{0,1\}//'
}

while [[ $# -gt 0 ]]; do
	case "$1" in
		--checkout) DO_CHECKOUT=1 ;;
		--commit) DO_COMMIT=1 ;;
		--dry-run) DO_DRY_RUN=1 ;;
		--skip-assets) SKIP_ASSETS=1 ;;
		--skip-tag) SKIP_TAG=1 ;;
		--message)
			shift
			COMMIT_MESSAGE="${1:-}"
			;;
		-h | --help)
			usage
			exit 0
			;;
		*)
			echo "Unknown option: $1" >&2
			usage >&2
			exit 1
			;;
	esac
	shift
done

read_plugin_version() {
	local main_file="${SCRIPT_DIR}/landtech-extras.php"
	grep -m1 -E '^\s*\*\s*Version:' "${main_file}" | sed -E 's/.*Version:[[:space:]]+//' | tr -d '\r\n'
}

read_readme_stable_tag() {
	local readme="${SCRIPT_DIR}/readme.txt"
	grep -m1 -E '^Stable tag:' "${readme}" | sed -E 's/^Stable tag:[[:space:]]+//' | tr -d '\r\n'
}

require_command() {
	if ! command -v "$1" >/dev/null 2>&1; then
		echo "Error: required command not found: $1" >&2
		exit 1
	fi
}

verify_org_build() {
	if [[ -f "${SCRIPT_DIR}/includes/updater.php" ]]; then
		echo "Error: includes/updater.php found — use the Premium repo for self-hosted builds, not WordPress.org SVN." >&2
		exit 1
	fi
}

verify_pack_tree() {
	local base="$1"
	local req=(
		"landtech-extras.php"
		"includes/landtech-extras-bootstrap-guard.php"
		"readme.txt"
		"assets/css/frontend.min.css"
	)
	local f
	for f in "${req[@]}"; do
		if [[ ! -f "${base}/${f}" ]]; then
			echo "Error: required file missing from deploy tree: ${f}" >&2
			exit 1
		fi
	done
}

rsync_excludes=(
	--exclude='.zip-pack-*'
	--exclude='DIST/'
	--exclude='.git/'
	--exclude='.github/'
	--exclude='.gitattributes'
	--exclude='.cursorrules'
	--exclude='.cursor/'
	--exclude='.idea/'
	--exclude='.vscode/'
	--exclude='node_modules/'
	--exclude='vendor/'
	--exclude='assets/src/'
	--exclude='tests/'
	--exclude='*.zip'
	--exclude='.lmfwc-credentials.local'
	--exclude='.distignore'
	--exclude='.release'
	--exclude='scripts/'
	--exclude='.plugin-check/'
)

sync_trunk() {
	local dest="${SVN_DIR}/trunk"
	mkdir -p "${dest}"
	local rsync_flags=( -a --delete "${rsync_excludes[@]}" "${SCRIPT_DIR}/" "${dest}/" )
	if [[ "${DO_DRY_RUN}" -eq 1 ]]; then
		rsync_flags=( -a --delete --dry-run "${rsync_excludes[@]}" "${SCRIPT_DIR}/" "${dest}/" )
	fi
	rsync "${rsync_flags[@]}"
}

svn_delete_missing() {
	local target="$1"
	local path
	while IFS= read -r path; do
		[[ -z "${path}" ]] && continue
		svn delete --force "${path}"
	done < <(svn status "${target}" | awk '/^!/ {print $2}')
}

stage_trunk() {
	svn add --force "${SVN_DIR}/trunk"
	svn_delete_missing "${SVN_DIR}/trunk"
}

build_org_assets() {
	local assets_dir="${SVN_DIR}/assets"
	local icon_svg="${ORG_ASSET_SRC}/icon.svg"
	local banner_svg="${ORG_ASSET_SRC}/banner.svg"
	local tmp=""

	if [[ ! -f "${icon_svg}" || ! -f "${banner_svg}" ]]; then
		echo "Error: missing ${ORG_ASSET_SRC}/{icon,banner}.svg — add WordPress.org asset sources first." >&2
		exit 1
	fi
	if ! command -v sips >/dev/null 2>&1; then
		echo "Error: sips not found (macOS). Install PNGs manually in SVN assets/ or run on macOS." >&2
		exit 1
	fi

	mkdir -p "${assets_dir}"
	local tmp
	tmp="$(mktemp -d)"
	trap 'rm -rf "${tmp}"' RETURN

	sips -s format png "${icon_svg}" --out "${tmp}/icon-256x256.png" >/dev/null
	sips -z 128 128 "${tmp}/icon-256x256.png" --out "${tmp}/icon-128x128.png" >/dev/null
	sips -s format png "${banner_svg}" --out "${tmp}/banner-full.png" >/dev/null
	sips -z 250 772 "${tmp}/banner-full.png" --out "${tmp}/banner-772x250.png" >/dev/null
	cp "${tmp}/banner-full.png" "${tmp}/banner-1544x500.png"

	cp "${tmp}/icon-256x256.png" "${tmp}/icon-128x128.png" \
		"${tmp}/banner-772x250.png" "${tmp}/banner-1544x500.png" \
		"${assets_dir}/"

	local screenshot
	for screenshot in "${ORG_ASSET_SRC}"/screenshot-*.png; do
		if [[ -f "${screenshot}" ]]; then
			cp "${screenshot}" "${assets_dir}/"
		fi
	done

	local blueprint_src="${SCRIPT_DIR}/assets/blueprints/blueprint.json"
	if [[ -f "${blueprint_src}" ]]; then
		mkdir -p "${assets_dir}/blueprints"
		cp "${blueprint_src}" "${assets_dir}/blueprints/blueprint.json"
	fi

	svn add --force "${assets_dir}"
	echo "Refreshed plugin-directory assets in ${assets_dir}"
}

create_release_tag() {
	local version="$1"
	local tag_path="${SVN_DIR}/tags/${version}"

	if [[ -e "${tag_path}" ]]; then
		echo "Error: SVN tag already exists: tags/${version}" >&2
		echo "Bump Version in landtech-extras.php and Stable tag in readme.txt before deploying." >&2
		exit 1
	fi

	svn copy "${SVN_DIR}/trunk" "${tag_path}"
	echo "Created tag tags/${version} from trunk"
}

ensure_svn_working_copy() {
	if [[ -d "${SVN_DIR}/.svn" ]]; then
		return 0
	fi
	if [[ "${DO_CHECKOUT}" -ne 1 && "${DO_COMMIT}" -ne 1 ]]; then
		echo "Error: SVN working copy not found at ${SVN_DIR}" >&2
		echo "Run with --checkout to create it, or set LANDTECH_EXTRAS_SVN_DIR." >&2
		exit 1
	fi
	echo "Checking out ${SVN_URL} → ${SVN_DIR}"
	mkdir -p "$(dirname "${SVN_DIR}")"
	svn checkout --depth immediates "${SVN_URL}" "${SVN_DIR}"
	svn update --set-depth infinity "${SVN_DIR}/trunk" "${SVN_DIR}/assets" "${SVN_DIR}/tags"
}

main() {
	require_command svn
	require_command rsync
	verify_org_build

	local version stable
	version="$(read_plugin_version)"
	stable="$(read_readme_stable_tag)"

	if [[ -z "${version}" || -z "${stable}" ]]; then
		echo "Error: could not read Version or Stable tag." >&2
		exit 1
	fi
	if [[ "${version}" != "${stable}" ]]; then
		echo "Error: version mismatch — landtech-extras.php Version (${version}) != readme.txt Stable tag (${stable})." >&2
		exit 1
	fi
	if [[ "${stable}" == "trunk" ]]; then
		echo "Error: readme.txt Stable tag is 'trunk'; set an explicit semver before SVN deploy." >&2
		exit 1
	fi

	echo "Deploy version: ${version}"
	echo "SVN working copy: ${SVN_DIR}"

	if [[ "${DO_DRY_RUN}" -eq 1 ]]; then
		echo "Dry run: rsync to trunk only"
		sync_trunk
		exit 0
	fi

	ensure_svn_working_copy
	cd "${SVN_DIR}"
	svn update trunk assets tags

	sync_trunk
	verify_pack_tree "${SVN_DIR}/trunk"
	stage_trunk

	if [[ "${SKIP_ASSETS}" -eq 0 ]]; then
		build_org_assets
	fi

	if [[ "${SKIP_TAG}" -eq 0 ]]; then
		create_release_tag "${version}"
	fi

	echo ""
	echo "SVN status (summary):"
	svn status | head -40
	local count
	count="$(svn status | wc -l | tr -d ' ')"
	if [[ "${count}" -gt 40 ]]; then
		echo "... (${count} lines total; run 'svn status' in ${SVN_DIR})"
	fi

	if [[ "${DO_COMMIT}" -eq 0 ]]; then
		echo ""
		echo "Staged locally. Review with: cd ${SVN_DIR} && svn status"
		echo "Commit with: ./scripts/deploy-wordpress-org-svn.sh --commit"
		exit 0
	fi

	if [[ -z "${COMMIT_MESSAGE}" ]]; then
		COMMIT_MESSAGE="Release ${version} to WordPress.org (trunk + tags/${version})."
	fi

	echo ""
	echo "Committing: ${COMMIT_MESSAGE}"
	svn commit -m "${COMMIT_MESSAGE}"
	echo "Done. Plugin directory sync may take 15–30 minutes."
	echo "https://wordpress.org/plugins/landtech-extras-for-elementor/"
}

main "$@"
