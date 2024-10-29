#!/bin/bash

set -e

source "../../.env"

DOMAIN=$(echo "$SQUASH_DOMAIN")

# install mkcert
if ! [ -x "$(command -v mkcert)" ]; then
  echo 'Error: mkcert is not installed.' >&2
  echo 'Please install it using `apt install mkcert`' >&2
  echo 'Press any key to continue...'
  read -n 1 -s
  exit 1
fi

# Check if libnss3-tools is installed
if ! [ -x "$(command -v certutil)" ]; then
  echo 'Error: libnss3-tools is not installed.' >&2
  echo 'Please install it using `apt install libnss3-tools`' >&2
  echo 'Press any key to continue...'
  read -n 1 -s
  exit 1
fi

mkcert -install "${DOMAIN}"

mkdir -p ../certs

find . -type f -name "*.pem" -exec mv {} ../certs \;