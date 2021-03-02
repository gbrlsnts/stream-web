FROM barricas/nginx-rtmp-docker-plus

EXPOSE 80

ENV HLS_URL=http://localhost:8181/hls/ \
    HLS_FRAGMENT=2s \
    HLS_PLAYLIST_LENGTH=10s \
    HLS_REQ_PER_SEC=20 \
    HLS_REQ_BURST=5 \
    SECURE_LINK_SECRET=secret \
    CORS_URL=* \
    TRUSTED_PROXY=172.16.0.0/12 \
    REAL_IP_HEADER=X-Forwarded-For

RUN apk --no-cache add gettext

COPY nginx.conf /etc/nginx/nginx.conf.template
COPY entrypoint.sh /entrypoint.sh

RUN chmod 644 /etc/nginx/nginx.conf.template && mkdir -p /tmp/nginx/client-body

ENTRYPOINT [ "/entrypoint.sh" ]