ARG PHP_VERSION=${PHP_VERSION}

FROM php:${PHP_VERSION}-fpm

LABEL maintainer="help@websoft9.com"
LABEL version="${PHP_VERSION}"
LABEL description="PHP runtime for ${PHP_VERSION}"

# install os common package
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
                chrony

# install php module(compare role_php and show list by `php -m`) TODO

# install php module for other image, such as drupal, wordpress,owncloud(https://github.com/docker-library)
RUN	apt-get install -y --no-install-recommends \
		libfreetype6-dev \
		libjpeg-dev \
		libpng-dev \
		libpq-dev \
		libwebp-dev \
		libzip-dev \
		gnupg dirmngr \
		libcurl4-openssl-dev \
		libicu-dev \
		libldap2-dev \
		libmemcached-dev \
		libxml2-dev \
		unzip \
# Ghostscript is required for rendering PDF previews
		ghostscript \
	; \
	\
	docker-php-ext-configure gd \
		--with-freetype \
		--with-jpeg=/usr \
		--with-webp \
	; \
	\
	docker-php-ext-install -j "$(nproc)" \
		bcmath \
		exif \
		intl \
		gd \
		ldap \
		pcntl \
		opcache \
		pdo_mysql \
		pdo_pgsql \
                pgsql \
                mysqli \
		zip

# install composer
RUN     php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
        php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"; \
        php composer-setup.php; \
        php -r "unlink('composer-setup.php')"; \
        php composer.phar; \
        mv composer.phar /usr/local/bin/composer

# install Laravel,ThinkPHP,Symfony,Yii
RUN     composer create-project laravel/laravel laravel-app; \
        composer create-project topthink/think tp-app; \
        composer create-project symfony/skeleton:"6.1.*" symfony_app; \
        composer create-project --prefer-dist yiisoft/yii2-app-basic yii-app

# create workdir
RUN mkdir -p /data/apps

# install supervisord
RUN apt install -y supervisor
