#!/usr/bin/env bash
# Build an installable WordPress theme zip (Appearance → Themes → Add New → Upload Theme).
#
# Usage: ./build-zip.sh [output-dir]      (default: ~/Downloads)
#
# The zip is named NRDS2-theme-<Version>.zip, using the Version from style.css,
# and unpacks into an NRDS2/ folder. It is built from the working files, so
# uncommitted edits are included. Dev-only files are excluded (see EXCLUDE below).

set -euo pipefail

THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
THEME_SLUG="$(basename "$THEME_DIR")"
OUT_DIR="${1:-$HOME/Downloads}"

# Directories and files left out of the zip
EXCLUDE_DIRS=".git .claude nrds-site"
EXCLUDE_FILES=".DS_Store .gitignore build-zip.sh"

cd "$THEME_DIR"

VERSION="$(sed -n 's/^Version:[[:space:]]*//p' style.css | head -n1 | tr -d '[:space:]')"
if [ -z "$VERSION" ]; then
    echo "Error: no Version: line found in style.css" >&2
    exit 1
fi

# ---------- Checks ----------
errors=0

# PHP syntax (every theme PHP file, skipping the static mockup)
if command -v php >/dev/null; then
    while IFS= read -r f; do
        if ! out="$(php -l "$f" 2>&1)"; then
            echo "$out" >&2
            errors=1
        fi
    done < <(find . -name '*.php' -not -path './.git/*' -not -path './nrds-site/*' | sort)
else
    echo "Warning: php not found, skipping syntax check" >&2
fi

# Every stylesheet part listed in nrds_style_parts() (functions.php) must exist
for part in tokens $(python3 -c 'import re; m=re.search(r"function nrds_style_parts\(\).*?array\((.*?)\)", open("functions.php").read(), re.S); print(" ".join(re.findall(r"\x27([\w-]+)\x27", m.group(1))) if m else "")'); do
    if [ ! -f "assets/css/$part.css" ]; then
        echo "Error: functions.php enqueues assets/css/$part.css, which does not exist" >&2
        errors=1
    fi
done

if [ "$errors" -ne 0 ]; then
    echo "Build aborted: fix the errors above first." >&2
    exit 1
fi

# ---------- Build ----------
mkdir -p "$OUT_DIR"
OUT="$OUT_DIR/$THEME_SLUG-theme-$VERSION.zip"

cd "$THEME_DIR/.."
python3 - "$THEME_SLUG" "$OUT" "$EXCLUDE_DIRS" "$EXCLUDE_FILES" <<'EOF'
import os, sys, zipfile

slug, out, skip_dirs, skip_files = sys.argv[1], sys.argv[2], set(sys.argv[3].split()), set(sys.argv[4].split())
tmp = out + ".tmp"
count = 0
with zipfile.ZipFile(tmp, "w", zipfile.ZIP_DEFLATED) as z:
    for root, dirs, files in os.walk(slug):
        dirs[:] = sorted(d for d in dirs if d not in skip_dirs)
        for f in sorted(files):
            if f in skip_files:
                continue
            path = os.path.join(root, f)
            z.write(path, path)
            count += 1
os.replace(tmp, out)
print(f"Built {out} ({count} files, {os.path.getsize(out) / 1024:.0f} KB)")
EOF
