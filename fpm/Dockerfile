FROM php:7.3-fpm-alpine

VOLUME [ "/usr/src/app/data" ]

ENV ENV=DEV \
    APP_NAME='Stream Demo' \
    APP_URL='http://localhost:8080' \
    DEFAULT_STREAM=ezstream \
    DEFAULT_PASSWORD=ezstream \
    PASSWORD_ALGO= \
    STREAM_FLASH_URL=rtmp://localhost/stream/%stream% \
    STREAM_HLS_URL=http://localhost:8181/hls/%stream%.m3u8?expires=%expire%&token=%token% \
    SECURE_LINK_SECRET=secret \
    SECURE_LINK_TTL=1800 \
    TOKEN_SIZE=5

WORKDIR /usr/src/app

COPY . .

ENTRYPOINT [ "./entrypoint.sh" ]