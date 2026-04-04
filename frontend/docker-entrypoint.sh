#!/bin/sh
set -e

echo "==> Running frontend entrypoint..."

# Install/update node_modules if needed
if [ ! -f node_modules/.package-lock.json ] || [ package.json -nt node_modules/.package-lock.json ]; then
  echo "Installing npm dependencies..."
  npm ci
fi

exec "$@"
