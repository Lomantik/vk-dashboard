# syntax=docker/dockerfile:1

FROM nginx:1.30-alpine3.23 AS build
WORKDIR /usr/share/nginx/html

COPY ./docker/backend/nginx/conf.d /etc/nginx/conf.d
COPY ./docker/backend/nginx/nginx.conf /etc/nginx/nginx.conf
