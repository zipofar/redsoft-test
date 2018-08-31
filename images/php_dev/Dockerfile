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

RUN pecl install xdebug-2.6.1 \
    && docker-php-ext-enable xdebug

ARG XDEBUG_INI=/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN echo "xdebug.default_enable = off" >> ${XDEBUG_INI} \
    && echo "xdebug.remote_enable = on" >> ${XDEBUG_INI} \
    && echo "xdebug.remote_autostart = off" >> ${XDEBUG_INI} \
    && echo "xdebug.remote_connect_back = off" >> ${XDEBUG_INI} \
    && echo "xdebug.remote_port = 9001" >> ${XDEBUG_INI} \
    && echo "xdebug.remote_host = 192.168.0.81" >> ${XDEBUG_INI}

WORKDIR /var/www/html

CMD ["php-fpm"]
