#!/bin/bash

# Change owner and group of the database directory to root  
sudo chown -R root:root ../../data

# This is to avoid permission issues when running the container.
# If not set to root, the database connection will fail.
