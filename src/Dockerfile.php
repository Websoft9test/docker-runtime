ARG PHP_VERSION=${PHP_VERSION}

FROM php:${PHP_VERSION}-fpm

LABEL maintainer="help@websoft9.com"
LABEL version="${PHP_VERSION}"
LABEL description="PHP runtime for ${PHP_VERSION}"

# install os common package or other image, such as drupal, wordpress,owncloud(https://github.com/docker-library)
RUN apt-get update && apt-get install -y \
                acl \
                mosh \
                curl \
                gnupg2 \
                ca-certificates \
                lsb-release \
                wget \
                openssl \
                unzip \
                bzip2 \
                expect \
                at \
                tree \
                vim \
                screen \
                pwgen \
                git \
                htop \
                imagemagick \
                goaccess \
                jq \
                net-tools \
                mlocate \
                chrony \
                gnupg dirmngr \
		ghostscript \
		libfreetype6-dev \
		libjpeg-dev \
		libpng-dev \
		libpq-dev \
		libwebp-dev \
		libzip-dev \
		libcurl4-openssl-dev \
		libicu-dev \
		libldap2-dev \
		libmemcached-dev \
		libxml2-dev

# install php module for other image, such as drupal, wordpress,owncloud(https://github.com/docker-library) and role_php
RUN docker-php-ext-configure gd \
		--with-freetype \
		--with-jpeg=/usr \
		--with-webp \
	; \
	\
	docker-php-ext-install -j "$(nproc)" \
                apcu \
		bcmath \
		bz2 \
		exif \
		imagick \
		imap \
		intl \
                geoip \
		gd \
		ldap \
		mcrypt \
		pcntl \
		mcrypt \
		memcache \
		mongodb \
		odbc \
		opcache \
		pdo_mysql \
		pdo_pgsql \
                pgsql \
                mysqli \
                redis \
                snmp \
                soap \
                tidy \
                xmlrpc \
		zip

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=60'; \
		echo 'opcache.fast_shutdown=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# install composer
# COPY --from=composer/composer /usr/bin/composer /usr/bin/composer
COPY --from=composer/composer /usr/bin/composer /usr/bin/composer

# install Laravel,ThinkPHP,Symfony,Yii
RUN composer create-project laravel/laravel mylaravel; \
    composer create-project topthink/think mythinkphp; \
    composer create-project symfony/skeleton mysymfony; \
    composer require webapp; \
    composer create-project --prefer-dist yiisoft/yii2-app-basic myyii

# create softlink of workdir
RUN mkdir -p /data/apps; \
    ln -sf /var/www/html/* /data/apps

# install supervisord
RUN apt install -y supervisor
