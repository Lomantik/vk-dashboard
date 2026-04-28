# syntax=docker/dockerfile:1

FROM node:25.9.0 AS build
WORKDIR /usr/src/app
COPY frontend/package*.json ./
RUN npm install
COPY ./frontend .
RUN npm run build

FROM nginx:1.30-alpine3.23 AS prod

COPY --from=build /usr/src/app/dist /usr/share/nginx/html

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
