# Installation Guide

This document provides detailed installation steps for different environments.

## Prerequisites

- Node.js 18.12.0 or higher
- Yarn 4.0.0 or higher
- Python (for native module builds)
- C++ build tools

## Linux

```bash
# Install build dependencies
sudo apt-get update
sudo apt-get install -y build-essential python3

# Install Node.js 18.x
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Install Yarn
corepack enable
corepack prepare yarn@4.6.0 --activate

# Clone the repository
git clone https://github.com/yourname/imagine.git
cd imagine

# Install dependencies
yarn install
```

## macOS

```bash
# Install Homebrew if not already installed
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install Node.js 18.x
brew install node@18

# Install Yarn
corepack enable
corepack prepare yarn@4.6.0 --activate

# Install Xcode Command Line Tools
xcode-select --install

# Clone the repository
git clone https://github.com/yourname/imagine.git
cd imagine

# Install dependencies
yarn install
```

## Windows

```powershell
# Install Node.js 18.x
# Download and run the installer from https://nodejs.org/

# Install Yarn
corepack enable
corepack prepare yarn@4.6.0 --activate

# Install build tools
npm install --global --production windows-build-tools

# Clone the repository
git clone https://github.com/yourname/imagine.git
cd imagine

# Install dependencies
yarn install
```

## Troubleshooting

### Native Module Build Issues

If you encounter issues with native modules like `@parcel/watcher` or `esbuild`, try:

```bash
# Rebuild specific packages
yarn rebuild @parcel/watcher esbuild

# Or clean and reinstall everything
yarn clean
yarn install
```

### Clearing Caches

```bash
# Clear Yarn cache
yarn cache clean

# Clear node_modules
rm -rf node_modules
yarn install
```
