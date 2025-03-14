FROM nginx:alpine
MAINTAINER Open Source Digital Signage Initiative <info@open-signage.org>

COPY ./output /usr/share/nginx/html
COPY ./nginx.conf /etc/nginx/conf.d/default.conf
ADD https://raw.githubusercontent.com/xibosignage/xibo-cms/master/web/swagger.json /usr/share/nginx/html/swagger.json
RUN chmod 544 /usr/share/nginx/html/swagger.json
