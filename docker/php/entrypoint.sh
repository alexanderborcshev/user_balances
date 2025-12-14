#!/bin/sh
set -e

# Ensure we are in the app directory
cd /var/www/html || exit 1

# If frontend present, install deps and build assets
if [ -f package.json ]; then
  echo "[entrypoint] Frontend detected (package.json found). Installing deps and building assets..."

  # Prefer npm ci when lockfile exists
  if [ -f package-lock.json ]; then
    npm ci
  else
    npm install
  fi

  npm run build
else
  echo "[entrypoint] No package.json found. Skipping frontend build."
fi
