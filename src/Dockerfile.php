FROM php:8.1-fpm

LABEL maintainer="help@websoft9.com"
LABEL version="1.0.1"
LABEL description="PHP runtime for 8.1"

ENV RUNTIME_LANG="PHP runtime"

# install os common package

RUN apt-get update && apt-get install -y \
								acl	\
								mosh	\
								curl	\
								gnupg2	\
								ca-certificates	\
								lsb-release	\
								wget	\
								openssl	\
								unzip	\
								bzip2	\
								expect	\
								at	\
								tree	\
								vim	\
								screen	\
								pwgen	\
								git	\
								htop	\
								imagemagick	\
								goaccess	\
								jq	\
								net-tools	\
								mlocate	\
								chrony

# install php module

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
		zip
