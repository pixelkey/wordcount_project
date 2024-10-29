#!/bin/bash

# Source directory containing the template
SRC_DIR=~/projects/tpl/docker-apache-wordpress/

# Destination directory (the current directory where the script is located)
DEST_DIR=$(pwd)

# Execute rsync command with exclusion rules
rsync -av --progress "$SRC_DIR" "$DEST_DIR" \
  --exclude '.gitignore' \
  --exclude 'src' \
  --exclude '.git' \
  --exclude 'docker/assets' \
  --exclude '.env' \
  --exclude 'data'

echo "Template copied successfully, excluding specified files and folders."
echo "Press enter to continue"
read
