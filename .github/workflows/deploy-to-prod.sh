#!/bin/bash

# Take username as argument
USERNAME=$1

# Set deployment directories
DEPLOY_PATH=/home/$USERNAME/public_html
RELEASE_PATH=/home/$USERNAME/releases/$(date +%Y%m%d_%H%M%S)
SHARED_PATH=/home/$USERNAME/shared

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

# Refresh Laravel caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

# Activate new release
ln -sfn $RELEASE_PATH $DEPLOY_PATH

# Clean up old releases (keep last 5)
cd /home/$USERNAME/releases
ls -1dt */ | tail -n +6 | xargs -d '\n' rm -rf --