ARG PHP_VERSION
FROM wordpress:php${PHP_VERSION}-apache

ARG XDEBUG_VERSION=2.9.6

RUN apt-get update; \
	apt-get install -y --no-install-recommends \
	# WP-CLI dependencies.
	bash less default-mysql-client git \
	# MailHog dependencies.
	msmtp;

COPY local/php.ini /usr/local/etc/php/php.ini

# Setup xdebug. The latest version supported by PHP 5.6 is 2.5.5.
RUN	pecl install "xdebug-${XDEBUG_VERSION}"; \
	docker-php-ext-enable xdebug \
	mv /usr/local/etc/php/conf.d/disabled/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/ \
	echo "xdebug.default_enable = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
	echo "xdebug.remote_autostart = 0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
	echo "xdebug.remote_connect_back = 0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
	echo "xdebug.remote_enable = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
	echo "xdebug.remote_port = 9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
	echo "xdebug.remote_log = /var/www/html/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini;

# Install PDO MySQL driver.
RUN docker-php-ext-install \
    pdo_mysql

ADD https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh /usr/local/bin/wait_for_it
RUN chmod 755 /usr/local/bin/wait_for_it
