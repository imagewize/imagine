#!/bin/bash
# This script updates dependencies and the yarn.lock file

echo "Cleaning caches..."
rm -rf .yarn/cache
rm -rf node_modules/.cache

echo "Updating dependencies..."
yarn upgrade

echo "Reinstalling all packages..."
rm -rf node_modules
yarn install

echo "Rebuilding problematic packages..."
yarn rebuild @parcel/watcher esbuild vue-demi

echo "Done! Please commit the updated yarn.lock file"
