#!/usr/bin/env bash
# Build a clean, installable theme zip: dist/aeon.zip (contains a single
# top-level "aeon/" folder), ready for WP Admin -> Appearance -> Themes ->
# Add New -> Upload Theme.
#
#   bash tools/package-theme.sh
#
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
THEMES="$ROOT/app/wp-content/themes"
DIST="$ROOT/dist"

mkdir -p "$DIST"
rm -f "$DIST/aeon.zip"

# Zip from the themes parent so archive entries are "aeon/...". Exclude any
# stray dev artifacts that must never ship inside the theme.
( cd "$THEMES" && zip -rq "$DIST/aeon.zip" aeon \
    -x 'aeon/.git/*' -x 'aeon/node_modules/*' -x 'aeon/*.log' )

echo "Built $DIST/aeon.zip"
echo "Upload it via WP Admin -> Appearance -> Themes -> Add New -> Upload Theme."
