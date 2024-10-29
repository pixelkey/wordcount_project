FROM php:7.4-apache

# Install the MySQL PDO extension
RUN docker-php-ext-install pdo pdo_mysql

# Install mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# PHP modules
RUN a2enmod rewrite
RUN a2enmod headers

# Get the .env file environment variable ENABLE_SSL and set to false by default
ARG ENABLE_SSL=false
ARG SQUASH_DOMAIN
# echo the value of the environment variable ENABLE_SSL
RUN echo "ENABLE_SSL is set to $ENABLE_SSL"

RUN if [ "$ENABLE_SSL" = "true" ] ; then \
    a2enmod ssl \
    ;fi

RUN mkdir /usr/local/bin/certs
COPY ./docker/certs/ /usr/local/bin/certs/
# Configure Apache to use SSL
RUN if [ "$ENABLE_SSL" = "true" ] ; then \
    mkdir -p /etc/certs \
    && cp -r /usr/local/bin/certs/* /etc/certs/ \
    && echo '<VirtualHost *:443>' > /etc/apache2/sites-available/wordpress-ssl.conf \
    && echo '  DocumentRoot /var/www/html' >> /etc/apache2/sites-available/wordpress-ssl.conf \
    && echo '  SSLEngine on' >> /etc/apache2/sites-available/wordpress-ssl.conf \
    && echo '  SSLCertificateFile /etc/certs/${SQUASH_DOMAIN}.pem' >> /etc/apache2/sites-available/wordpress-ssl.conf \
    && echo '  SSLCertificateKeyFile /etc/certs/${SQUASH_DOMAIN}-key.pem' >> /etc/apache2/sites-available/wordpress-ssl.conf \
    && echo '</VirtualHost>' >> /etc/apache2/sites-available/wordpress-ssl.conf \
    && a2ensite wordpress-ssl \
    ;fi

# Install additional required and optional PHP extensions for WordPress
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install exif \
    && docker-php-ext-install zip \
    && docker-php-ext-install intl


# Configure Apache to use the .htaccess files
RUN echo '<Directory "/var/www/html">' > /etc/apache2/conf-available/wordpress.conf \
    && echo '  AllowOverride All' >> /etc/apache2/conf-available/wordpress.conf \
    && echo '</Directory>' >> /etc/apache2/conf-available/wordpress.conf \
    && a2enconf wordpress

RUN apt-get update && apt-get install -y iputils-ping

# Install netcat-openbsd (nc), WordPress CLI, and MySQL client
RUN apt-get update && \
    apt-get install -y netcat-openbsd \
    default-mysql-client && \
    curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x wp-cli.phar && \
    mv wp-cli.phar /usr/local/bin/wp

# Forward Message to mailhog
RUN curl --location --output /usr/local/bin/mhsendmail https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64 && \
    chmod +x /usr/local/bin/mhsendmail
RUN echo 'sendmail_path="/usr/local/bin/mhsendmail --smtp-addr=mailhog:1025 --from=no-reply@gbp.lo"' > /usr/local/etc/php/conf.d/mailhog.ini

# Install unzip and other utilities
RUN apt-get install -y unzip && \
    rm -rf /var/lib/apt/lists/*

# Copy the entrypoint script to the container and make it executable
COPY entrypoint-wordpress.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint-wordpress.sh

# Copy all the contents of packages folder inside /usr/local/bin folder
COPY ./docker/assets/packages/ /usr/local/bin/packages/
# Copy all the contents of wp-content folder inside /usr/local/bin folder
COPY ./docker/assets/wp-content/ /usr/local/bin/wp-content/
COPY ./docker/config/apache/.htaccess /usr/local/bin/apache/.htaccess


WORKDIR /var/www/html

# Set the entrypoint
ENTRYPOINT ["entrypoint-wordpress.sh"]