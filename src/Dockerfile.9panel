FROM nginx:stable-alpine

LABEL maintainer="help@websoft9.com"
LABEL version="1.0.1"
LABEL description="9panel"

ENV RUNTIME_LANG="LAMP"

COPY  ./9panel /usr/share/nginx/html
COPY ./entrypoint-9panel.sh /docker-entrypoint.d/

RUN set -ex; \
    ls /usr/share/nginx/html; \
    chmod +x /docker-entrypoint.d/entrypoint-9panel.sh