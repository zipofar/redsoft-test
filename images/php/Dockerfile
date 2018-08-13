FROM php:7.2-fpm

RUN apt-get update && apt-get install -y \
		curl \
		wget \
		git \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libmcrypt-dev \
		libxml2-dev \
	&& docker-php-ext-install -j$(nproc) iconv mbstring pdo_mysql zip xml json\
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

CMD ["php-fpm"]
