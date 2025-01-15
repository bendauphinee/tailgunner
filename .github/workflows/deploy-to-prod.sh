#!/bin/bash

# Set deployment directories
DEPLOY_PATH=/var/www/html
RELEASE_PATH=/var/www/releases/$(date +%Y%m%d_%H%M%S)
SHARED_PATH=/var/www/shared

# Create required directories
mkdir -p $RELEASE_PATH
mkdir -p $SHARED_PATH/storage

# Copy deployment package
cd $RELEASE_PATH

# Extract deployment package
tar xzf ~/deploy.tar.gz
rm ~/deploy.tar.gz

# Link shared directories
rm -rf $RELEASE_PATH/storage
ln -s $SHARED_PATH/storage $RELEASE_PATH/storage

# Update permissions
chmod -R 755 $RELEASE_PATH
chown -R www-data:www-data $RELEASE_PATH

# Activate new release
ln -sfn $RELEASE_PATH $DEPLOY_PATH

# Clean up old releases (keep last 5)
cd /var/www/releases
ls -1dt */ | tail -n +6 | xargs -d '\n' rm -rf --