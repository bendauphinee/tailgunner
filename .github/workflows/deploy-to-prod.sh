#!/bin/bash

BASE_DEPLOY_PATH=~/deployments
BASE_RELEASE_PATH="${BASE_DEPLOY_PATH}/releases"

DEPLOY_PREFIX="deploy-"
TARBALL="$(ls -1t ${BASE_DEPLOY_PATH}/${DEPLOY_PREFIX}*.tar.gz | head -n1)"
TIMESTAMP="$(basename "$TARBALL" | sed -E "s/.*${DEPLOY_PREFIX}(.+)\.tar\.gz/\1/g")"

THIS_RELEASE_PATH=${BASE_RELEASE_PATH}/${TIMESTAMP}
MAX_RELEASES=5

# Create release directory and extract files
mkdir -p ${THIS_RELEASE_PATH}
tar -xzf "$TARBALL" -C ${THIS_RELEASE_PATH}

# Remove and link storage directory
rm -Rf ${THIS_RELEASE_PATH}/src/storage ${THIS_RELEASE_PATH}/src/.env
ln -s ~/shared/storage ${THIS_RELEASE_PATH}/src/storage
ln -s ~/shared/.env ${THIS_RELEASE_PATH}/src/.env

# Refresh Laravel caches
cd ${THIS_RELEASE_PATH}/src
php artisan config:clear || exit 1
php artisan route:cache || exit 1
php artisan view:cache || exit 1
php artisan storage:link || exit 1

# Run migrations
php artisan migrate --force || exit 1

# Relink the release directory
ln -sfn ${THIS_RELEASE_PATH}/src ~/public_html

# Cleanup old releases and tarballs (keep last # of each)
cd ${BASE_DEPLOY_PATH}
ls -1t ${DEPLOY_PREFIX}*.tar.gz | tail -n +$((MAX_RELEASES + 1)) | xargs -d '\n' rm -f -- 2>/dev/null || true

cd ${BASE_RELEASE_PATH}
ls -1dt */ | tail -n +$((MAX_RELEASES + 1)) | xargs -d '\n' rm -rf -- 2>/dev/null || true
