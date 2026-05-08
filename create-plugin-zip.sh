#!/usr/bin/env bash
#
# Build a WordPress-installable ZIP: one top-level folder (this plugin directory)
# with dev/editor artifacts excluded.
#
# Usage:
#   ./create-plugin-zip.sh              # writes ../<folder-name>-<version>.zip
#   ./create-plugin-zip.sh -o ~/out.zip # explicit output path (directory must exist)
#

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_SLUG="$(basename "$SCRIPT_DIR")"
PARENT_DIR="$(dirname "$SCRIPT_DIR")"

read_version() {
	local main_file="${SCRIPT_DIR}/elementor-extras.php"
	if [[ ! -f "${main_file}" ]]; then
		echo "unknown"
		return
	fi
	# WP plugin header: * Version: x.y.z
	grep -m1 -E '^\s*\*\s*Version:' "${main_file}" | sed -E 's/.*Version:[[:space:]]+//' | tr -d '\r\n' || echo "unknown"
}

VERSION="$(read_version)"
DEFAULT_OUT="${PARENT_DIR}/${PLUGIN_SLUG}-${VERSION}.zip"
OUT_ZIP="${DEFAULT_OUT}"

while [[ "${1:-}" == -* ]]; do
	case "${1:-}" in
		-o | --output)
			OUT_ZIP="${2:?--output requires a path}"
			shift 2
			;;
		-h | --help)
			echo "Usage: $0 [-o|--output PATH]"
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

# Paths inside the archive look like PLUGIN_SLUG/... (zip ran from PARENT_DIR).
ZIP_PREFIX="${PLUGIN_SLUG}"

echo "Creating: ${OUT_ZIP}"
echo " Root folder in archive: ${ZIP_PREFIX}/"
echo " Version from header: ${VERSION}"

(cd "${PARENT_DIR}" && zip -r "${OUT_ZIP}" "${ZIP_PREFIX}" \
	-x "${ZIP_PREFIX}/.cursorrules" \
	-x "${ZIP_PREFIX}/.cursor/*" \
	-x "${ZIP_PREFIX}/.git/*" \
	-x "${ZIP_PREFIX}/.git" \
	-x "${ZIP_PREFIX}/.github/*" \
	-x "${ZIP_PREFIX}/.gitignore" \
	-x "${ZIP_PREFIX}/.gitattributes" \
	-x "${ZIP_PREFIX}/node_modules/*" \
	-x "${ZIP_PREFIX}/.DS_Store" \
	-x "${ZIP_PREFIX}/vendor/*" \
	-x "${ZIP_PREFIX}/assets/src/*" \
	-x "${ZIP_PREFIX}/tests/*" \
	-x "${ZIP_PREFIX}/phpunit.xml" \
	-x "${ZIP_PREFIX}/phpunit.xml.dist" \
	-x "${ZIP_PREFIX}/phpcs.xml" \
	-x "${ZIP_PREFIX}/phpcs.xml.dist" \
	-x "${ZIP_PREFIX}/phpcs-security.xml" \
	-x "${ZIP_PREFIX}/.deploy-trigger" \
	-x "${ZIP_PREFIX}/.deploy-test" \
	-x "${ZIP_PREFIX}/*.zip" \
	-x "${ZIP_PREFIX}/*.sh" \
	-x "${ZIP_PREFIX}/*.md" \
	-x "${ZIP_PREFIX}/create-plugin-zip.sh" \
	-x "${ZIP_PREFIX}/.idea/*" \
	-x "${ZIP_PREFIX}/.vscode/*" \
	-x "${ZIP_PREFIX}/*.swp" \
	-x "${ZIP_PREFIX}/*.swo" \
	-x "${ZIP_PREFIX}/composer.json" \
	-x "${ZIP_PREFIX}/composer.lock" \
	-x "${ZIP_PREFIX}/package.json" \
	-x "${ZIP_PREFIX}/package-lock.json" \
	-x "${ZIP_PREFIX}/yarn.lock" \
	-x "${ZIP_PREFIX}/pnpm-lock.yaml" \
)

echo "Done."
