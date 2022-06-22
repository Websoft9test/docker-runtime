ARG PHP_VERSION=${PHP_VERSION}

FROM php:${PHP_VERSION}-fpm

LABEL maintainer="help@websoft9.com"
LABEL version="${PHP_VERSION}"
LABEL description="PHP runtime for ${PHP_VERSION}"

# install os common package or other image, such as drupal, wordpress,owncloud(https://github.com/docker-library)
RUN apt-get update && apt-get install -y --no-install-recommends \
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
		unixodbc-dev \
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
		libsnmp-dev \
		libtidy-dev \
		libmcrypt-dev \
		libgmp-dev \
		libmagickwand-dev \
		libmagickcore-dev \
		libc-client-dev \
		libkrb5-dev \
		libc-client-dev \
		libbz2-dev \
		libxml2-dev || true

# install php module for other image, such as drupal, wordpress,owncloud(https://github.com/docker-library) and role_php
RUN docker-php-ext-configure gd \
		--with-freetype \
		--with-jpeg=/usr \
		--with-webp \
	|| true; \
	\
	docker-php-ext-install -j "$(nproc)" \
		bcmath \
		exif \
		intl \
		gd \
		pcntl \
		opcache \
		pdo_mysql \
		pdo_pgsql \
                pgsql \
                mysqli \
                soap \
                snmp \
                tidy \
                gmp \
                mysqli \
                bz2 \
		zip \
		ldap || true

# config and install odbc and imap
RUN { \
      echo '# https://github.com/docker-library/php/issues/103#issuecomment-271413933'; \
      echo 'AC_DEFUN([PHP_ALWAYS_SHARED],[])dnl'; \
      echo; \
      cat /usr/src/php/ext/odbc/config.m4; \
    } > temp.m4; \
    mv temp.m4 /usr/src/php/ext/odbc/config.m4; \
    docker-php-ext-configure odbc --with-unixODBC=shared,/usr; \
    docker-php-ext-install odbc; \
    docker-php-ext-configure imap --with-kerberos --with-imap-ssl; \
    docker-php-ext-install imap

# install pecl packge
RUN pecl install mongodb; \
    pecl install xmlrpc-1.0.0RC3; \
    echo "no" | pecl install apcu; \
    echo "no" | pecl install redis; \
    echo "no" | pecl install memcached; \
    echo "mcrypt" | pecl install mcrypt; \
    echo "imagick" | pecl install imagick




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
COPY --from=composer/composer /usr/bin/composer /usr/bin/composer

# install Laravel,ThinkPHP,Symfony,Yii
RUN composer create-project laravel/laravel mylaravel; \
    composer create-project topthink/think mythinkphp; \
    composer create-project symfony/skeleton mysymfony; \
    cd /var/www/html/mysymfony; \
    composer require symfony/skeleton; \
    composer create-project --prefer-dist yiisoft/yii2-app-basic myyii

# create softlink of workdir
RUN mkdir -p /data/apps; \
    ln -sf /var/www/html/* /data/apps

# install supervisord
RUN apt install -y supervisor
