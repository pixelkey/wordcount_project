#!/bin/bash
set -e

# This script is used to adjust the permissions in the data/db folder to make it accessible to the local user.
# Without this change, the local user will not have access to the data/db folder and files.

# However, if you are willing to sacrifice performance, you can remove the following 'delegated' option from the docker-compose.yml file:
#          - ./data/db:/var/lib/mysql:delegated

#After removing this option, you will be able to access the data/db folder and files without running this script.

sudo chmod 770 -R ../../data/db
exec "$@"