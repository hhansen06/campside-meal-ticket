FROM debian:11.1-slim

# No interactive frontend during docker build
ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update
RUN apt-get install php php-mysql apache2 -y
RUN apt-get install curl php-cli php-mbstring git unzip -y
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

ADD source/ /var/www/html/

WORKDIR /var/www/html
RUN composer install


ADD run.sh /run.sh
RUN chmod +x /run.sh
ENTRYPOINT ["/run.sh"]