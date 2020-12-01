FROM php:7.2-apache

#set timezone
ENV TZ=Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

#add user
ENV GID 1000
ENV UID 1000
RUN groupadd -g ${GID} developer && \
    useradd -u ${UID} -g developer -m developer -G root,www-data && \
    usermod -p "*" developer -s /bin/bash && \
    usermod -aG www-data developer

#create volume
VOLUME ["B:/OpenServer/domains/localhost/exchange_rates"]

#install apps
RUN apt-get update \
	&& apt-get upgrade -y \
  	&& apt-get install -y \
    apt-utils \
    apt-transport-https \
    curl \
    vim \
    git \
    build-essential \
    wget \
    zip \
    unzip \
    bzip2 \
    libzip-dev \
    libbz2-dev \
    libfontconfig \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libicu-dev \
    libxml2-dev \
    libmcrypt-dev \
    libpq-dev \
    g++ \
	cron \
    openssh-server \
    libonig-dev

#install php extensions
RUN docker-php-ext-install \
    gd \
    bz2 \
    zip \
    intl \
    iconv \
    bcmath \
    opcache \
    calendar \
    mbstring \
    mysqli \
    pdo \
    pdo_mysql \
    sockets

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Copy hello-cron file to the cron.d directory
COPY cron /etc/cron.d/cron

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/cron

# Apply cron job
RUN crontab /etc/cron.d/cron

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

#set apache env
ENV APACHE_DOCUMENT_ROOT /var/www/html/
ENV APACHE_RUN_USER #1000
ENV APACHE_RUN_GROUP #1000
RUN a2enmod rewrite headers expires

#cleare cahches
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY exchange_rates.conf /etc/apache2/sites-available/exchange_rates.conf
RUN cd /etc/apache2/sites-available/ && a2dissite 000-default.conf && a2ensite exchange_rates.conf

EXPOSE 80 22
