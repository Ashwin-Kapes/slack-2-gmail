
FROM    debian:stretch

ENV DEBIAN_FRONTEND="noninteractive" \
    INITRD="No" \
    PACKAGES="unzip php7.2-fpm php7.2-cli php7.2-common php7.2-curl php7.2-gd php7.2-intl php7.2-pgsql php7.2-json php7.2-redis php7.2-mbstring php7.2-bcmath php-mail-mime"

WORKDIR /app

## Installs the PHP runtime and FPM
## also install nodejs needed to generate addresses
RUN echo "*** Installing $PACKAGES" \
    && apt-get update && apt-get install curl gnupg apt-transport-https ca-certificates lsb-release -y -q \
    && curl -o /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
    && sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list' \
    && apt-get update && apt-get install -y -q $PACKAGES \
    && curl -sL https://deb.nodesource.com/setup_8.x | bash - \
    && apt-get install -y -q nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

## Install composer
RUN echo "*** Installing composer" \
    && curl -k -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ \
    && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

CMD php artisan serve --host 0.0.0.0 --port 8080 -vvv
