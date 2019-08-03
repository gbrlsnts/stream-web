FROM php:7.3-fpm-alpine

ENV ENV=DEV \
    APP_NAME='Stream Demo' \
    DEFAULT_STREAM=ezstream \
    STREAM_FLASH_URL=rtmp://localhost/stream/%stream% \
    STREAM_HLS_URL=http://localhost/hls/%stream%.m3u8

WORKDIR /usr/src/app

COPY . .

ENTRYPOINT [ "./entrypoint.sh" ]