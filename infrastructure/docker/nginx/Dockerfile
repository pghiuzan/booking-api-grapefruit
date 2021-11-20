FROM nginx:1.20-alpine

ADD ./public /var/www/html/public
ADD ./infrastructure/docker/nginx/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/html
