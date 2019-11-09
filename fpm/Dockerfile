FROM php:7.3-fpm-alpine

VOLUME [ "/usr/src/app/data" ]

ENV ENV=DEV \
    APP_NAME='Stream Demo' \
    DEFAULT_STREAM=ezstream \
    DEFAULT_PASSWORD=ezstream \
    PASSWORD_ALGO= \
    STREAM_FLASH_URL=rtmp://localhost/stream/%stream% \
    STREAM_HLS_URL=http://localhost:8181/hls/%stream%.m3u8 \
    TOKEN_SIZE=5

WORKDIR /usr/src/app

COPY . .

ENTRYPOINT [ "./entrypoint.sh" ]