# syntax=docker/dockerfile:1

FROM node:25.9.0 AS dev
WORKDIR /usr/src/app

ARG UID=1000
ARG GID=1000

RUN usermod -u ${UID} node && groupmod -g ${GID} node

RUN chown -R node:node /usr/src/app

USER node

COPY --chown=node:node frontend/package*.json ./

RUN npm install

CMD ["tail", "-f", "/dev/null"]
