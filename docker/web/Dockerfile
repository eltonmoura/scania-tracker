FROM php:7.2-apache
MAINTAINER elton.ms@gmail.com

RUN apt-get update && apt-get install -y vim unzip cron libzip-dev zip

# Configurando Timezone do Serv
ENV TZ=America/Sao_Paulo
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Variaveis de ambiente PHP
COPY ./php.ini /usr/local/etc/php/conf.d/php.ini

# Update the default apache site with the config we created.
COPY ./apache.conf /etc/apache2/sites-enabled/000-default.conf

# Configurando crontab
# COPY ./crontab /etc/cron.d/crontab
# CMD chmod 0644 /etc/cron.d/crontab

# ENTRYPOINT "crond"

# PHP extensions
RUN docker-php-ext-configure zip --with-libzip
RUN docker-php-ext-install pdo pdo_mysql zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

RUN a2enmod rewrite
# RUN crontab /etc/cron.d/crontab
