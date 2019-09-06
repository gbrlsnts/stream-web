FROM nginx:1.17

RUN mkdir -p /usr/src/app/public

COPY ./nginx/app/nginx.conf /etc/nginx/nginx.conf
COPY ./public /usr/src/app/public