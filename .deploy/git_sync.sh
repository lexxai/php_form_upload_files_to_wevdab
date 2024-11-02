#!/bin/sh

git stash 
git pull

# Get the latest Git commit hash
GIT_HASH=$(git rev-parse --short HEAD)
GIT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
GIT_VERSION=$(git describe --tags --always)

FILTERED_VERSION=$(echo "$GIT_VERSION" | grep -v '-')

VERSION=""

if [ "$GIT_BRANCH" != "main" ]; then 
    VERSION="[$GIT_BRANCH] "; 
fi
 
if [ -z "$FILTERED_VERSION" ]; then
    VERSION="${VERSION}${GIT_HASH}"
else
    VERSION="${VERSION}v${FILTERED_VERSION}"
fi

# Define the config file path
CONFIG_FILE="../config/.config.php"

if [ -f "$CONFIG_FILE" ]; then
    sed -i '' "s/const APP_VERSION = '.*';/const APP_VERSION = '$VERSION';/" "$CONFIG_FILE"
    echo "Updated version to '$VERSION' in $CONFIG_FILE"
fi

