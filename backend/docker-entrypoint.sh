#!/bin/sh
set -e

echo "==> Running backend entrypoint..."

# Install/update vendor if needed
if [ ! -f vendor/autoload.php ] || [ composer.json -nt vendor/autoload.php ]; then
  echo "Installing composer dependencies..."
  composer install
fi

exec "$@"
