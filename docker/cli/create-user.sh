#!/bin/bash

# Get user ID, group ID, LOCAL_UNAME, and user group
LOCAL_UID=$(id -u)
LOCAL_GID=$(id -g)

# Check if ../../.env file exists, if not create it
if [ ! -f ../../.env ]; then
    touch ../../.env
fi

# Check if the variables already exist in the file
if grep -q "LOCAL_UID" ../../.env; then
    # If they do, update them
    sed -i "/# Local User/,/# End: Local User/c\\# Local User\nLOCAL_UID=$LOCAL_UID\nLOCAL_GID=$LOCAL_GID\n# End: Local User" ../../.env
else
    # If they don't, append them
    echo "" >> ../../.env
    echo "# Local User" >> ../../.env
    echo "LOCAL_UID=$LOCAL_UID" >> ../../.env
    echo "LOCAL_GID=$LOCAL_GID" >> ../../.env
    echo "# End: Local User" >> ../../.env
fi