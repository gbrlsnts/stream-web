#!/bin/sh
set -e

envsubst '${HLS_URL} ${HLS_FRAGMENT} ${HLS_PLAYLIST_LENGTH} ${CORS_URL} ${SECURE_LINK_SECRET}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

nginx -g 'daemon off;'