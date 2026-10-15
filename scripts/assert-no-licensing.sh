#!/bin/sh
# Fail if the WordPress.org tree picks up premium licensing code.
# scripts/inject-lmfwc-credentials-for-zip.php is a build helper that skips this tree; it is excluded.
set -eu

root=$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)
cd "$root"

matches=$(grep -RInE \
	--include='*.php' \
	--exclude-dir=scripts \
	--exclude-dir=vendor \
	--exclude-dir=node_modules \
	--exclude-dir=tests \
	--exclude-dir=.git \
	'landtech-license|LMFWC_BAKED|DEFAULT_LANDTECH_SHARED_SECRET|License_Client_SDK' \
	. || true)

if [ -n "$matches" ]; then
	printf '%s\n' "$matches"
	echo "Free plugin must not contain licensing code." >&2
	exit 1
fi

echo "Free plugin licensing gate: no license-server code in the shipping tree."
