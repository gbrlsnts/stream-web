FROM dvdgiessen/nginx-rtmp-docker

EXPOSE 80

ENV HLS_URL=http://localhost:8181/hls/ \
    HLS_FRAGMENT=2s \
    HLS_PLAYLIST_LENGTH=10s \
    CORS_URL=*

RUN apk --no-cache add gettext

COPY nginx.conf /etc/nginx/nginx.conf.template
COPY entrypoint.sh /entrypoint.sh

RUN chmod 644 /etc/nginx/nginx.conf.template && mkdir -p /tmp/nginx/client-body

ENTRYPOINT [ "/entrypoint.sh" ]