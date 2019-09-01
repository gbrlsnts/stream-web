FROM php:7.3-fpm-alpine

VOLUME [ "/usr/src/app/data" ]

ENV ENV=DEV \
    APP_NAME='Stream Demo' \
    DEFAULT_STREAM=ezstream \
    STREAM_FLASH_URL=rtmp://localhost/stream/%stream% \
    STREAM_HLS_URL=http://localhost:8181/hls/%stream%.m3u8

WORKDIR /usr/src/app

COPY . .

ENTRYPOINT [ "./entrypoint.sh" ]