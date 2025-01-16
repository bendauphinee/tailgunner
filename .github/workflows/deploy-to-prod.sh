#!/bin/bash

BASE_DEPLOY_PATH=~/deployments
TARBALL="$(ls -1t ${BASE_DEPLOY_PATH}/deploy-*.tar.gz | head -n1)"
TIMESTAMP="$(basename "$TARBALL" | sed -E 's/.*deploy-(.+)\.tar\.gz/\1/g')"
BASE_RELEASE_PATH=~/deployments/releases
RELEASE_PATH=${BASE_RELEASE_PATH}/${TIMESTAMP}

# Create release directory and extract files
mkdir -p ${RELEASE_PATH}
tar -xzf "$TARBALL" -C ${RELEASE_PATH}

# Remove and link storage directory
rm -Rf ${RELEASE_PATH}/src/storage
ln -s ~/shared/storage ${RELEASE_PATH}/src/storage
ln -s ~/shared/.env ${RELEASE_PATH}/src/.env

# Refresh Laravel caches
cd ${RELEASE_PATH}/src
php artisan config:clear || exit 1
php artisan route:cache || exit 1
php artisan view:cache || exit 1
php artisan storage:link || exit 1

# Run migrations
php artisan migrate --force || exit 1

# Relink the release directory
ln -sfn ${RELEASE_PATH}/src ~/public_html

# Cleanup old releases and tarballs (keep last 5 of each)
cd ${BASE_DEPLOY_PATH}
ls -1t deploy-*.tar.gz | tail -n +6 | xargs -d '\n' rm -f -- 2>/dev/null || true

cd ${BASE_RELEASE_PATH}
ls -1dt */ | tail -n +6 | xargs -d '\n' rm -rf -- 2>/dev/null || true
