FROM php:7.4-apache

RUN sed -i "s#deb.debian.org#mirrors.tuna.tsinghua.edu.cn#" /etc/apt/sources.list && \
    sed -i "s#security.debian.org#mirrors.tuna.tsinghua.edu.cn#" /etc/apt/sources.list

# Configure LDAP.
RUN apt-get update \
 && apt-get install libldap2-dev -y \
 && rm -rf /var/lib/apt/lists/* \
 && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
 && docker-php-ext-install ldap

RUN docker-php-ext-install mysqli && \
    docker-php-ext-install pdo_mysql

COPY ./ /var/www/html/
