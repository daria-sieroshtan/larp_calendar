FROM ubuntu:18.04

RUN apt update
RUN DEBIAN_FRONTEND=noninteractive apt install -yq --no-install-recommends php php-json php-curl php-ctype php-iconv php-pdo php-mysql php-dom php-zip composer apache2 libapache2-mod-php
COPY . /var/www/rolendar

WORKDIR /var/www/rolendar

RUN cp /var/www/rolendar/larp.conf /etc/apache2/sites-available && \
    a2ensite larp && \
    a2enmod rewrite && \
    composer install && \
    chown -R www-data:www-data /var/www/rolendar

CMD /bin/bash
