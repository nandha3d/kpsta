#!/bin/bash
#
# Deploy the application to $DEPLOYPATH.
#
# Invoked by .cpanel.yml on a cPanel Git deployment, but it is a plain script
# and can be run by hand or from any other CI.
#
# Three things this has to get right, all of which a plain `cp -r` gets wrong:
#
#   1. NEVER copy .git into the document root. It is ~98MB and its history
#      contains credentials that were committed before they were moved to the
#      environment. Served over HTTP that is a full repository disclosure.
#   2. NEVER copy .env, and never delete the one already on the server. It holds
#      the live credentials and is deliberately not in the repository.
#   3. NEVER delete anything at the destination. uploads/ is user data that only
#      exists on the server, and writable/ holds live sessions. This script only
#      ever adds and overwrites.
#
# CodeIgniter 4 lives in vendor/, which is not committed, so a dependency
# install has to happen on the server. If Composer cannot be found the script
# fails loudly rather than leaving a site with no framework behind it.

set -euo pipefail

if [ -z "${DEPLOYPATH:-}" ]; then
    echo "ERROR: DEPLOYPATH is not set." >&2
    exit 1
fi

SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DEST="${DEPLOYPATH%/}"

echo "==> Deploying $SRC -> $DEST"

mkdir -p "$DEST"

# Everything that must not reach the document root.
EXCLUDES=(
    ".git"
    ".gitignore"
    ".env"              # live credentials; the server keeps its own
    "node_modules"
    "tests"             # Playwright specs
    "playwright.config.js"
    "debug-login.js"
    "_router.php"       # local dev front controller for `php -S`
    "deploy.sh"
    ".cpanel.yml"
    "writable/cache"
    "writable/logs"
    "writable/session"
    "writable/debugbar"
)

# ---------------------------------------------------------------------------
# Copy
# ---------------------------------------------------------------------------
if command -v rsync >/dev/null 2>&1; then
    echo "==> Syncing with rsync (no --delete: uploads/ and .env are preserved)"
    RSYNC_ARGS=(-a --no-perms --no-owner --no-group)
    for e in "${EXCLUDES[@]}"; do
        RSYNC_ARGS+=(--exclude "/$e")
    done
    rsync "${RSYNC_ARGS[@]}" "$SRC/" "$DEST/"
else
    echo "==> rsync unavailable, falling back to tar"
    TAR_ARGS=()
    for e in "${EXCLUDES[@]}"; do
        TAR_ARGS+=(--exclude="./$e")
    done
    # tar preserves the directory tree and dotfiles, which `cp *` does not.
    tar -C "$SRC" -cf - "${TAR_ARGS[@]}" . | tar -C "$DEST" -xf -
fi

# ---------------------------------------------------------------------------
# Dependencies
# ---------------------------------------------------------------------------
COMPOSER=""
if command -v composer >/dev/null 2>&1; then
    COMPOSER="composer"
elif [ -f "$DEST/composer.phar" ]; then
    COMPOSER="php $DEST/composer.phar"
elif command -v composer.phar >/dev/null 2>&1; then
    COMPOSER="composer.phar"
fi

if [ -n "$COMPOSER" ]; then
    echo "==> Installing dependencies"
    ( cd "$DEST" && $COMPOSER install --no-dev --optimize-autoloader --no-interaction )
elif [ -d "$DEST/vendor/codeigniter4/framework" ]; then
    echo "==> WARNING: Composer not found, but vendor/ is already present."
    echo "    Dependencies were NOT refreshed. If composer.lock changed in this"
    echo "    release, upload vendor/ manually or the site will be stale."
else
    echo "ERROR: Composer is not available and $DEST/vendor is missing." >&2
    echo "       CodeIgniter 4 lives in vendor/, so the site will not run." >&2
    echo "       Either install Composer on this host, or build vendor/ locally" >&2
    echo "       with 'composer install --no-dev --optimize-autoloader' and" >&2
    echo "       upload it to $DEST/vendor." >&2
    exit 1
fi

# ---------------------------------------------------------------------------
# Post-deploy checks
# ---------------------------------------------------------------------------
mkdir -p "$DEST"/writable/{cache,logs,session,uploads,debugbar}
chmod -R 775 "$DEST/writable" 2>/dev/null || true

if [ ! -f "$DEST/.env" ]; then
    echo "==> WARNING: $DEST/.env does not exist."
    echo "    Copy env.example to .env there and fill in the database"
    echo "    credentials, or the site cannot connect. Set CI_ENVIRONMENT=production."
fi

# A previous `cp -r` style deploy may already have put these in the web root.
for leaked in .git .env.bak composer.phar; do
    if [ -e "$DEST/$leaked" ] && [ "$leaked" = ".git" ]; then
        echo "==> WARNING: $DEST/.git exists from an earlier deploy."
        echo "    It exposes the full repository, including credentials that were"
        echo "    committed before being moved to the environment. Remove it."
    fi
done

echo "==> Done."
