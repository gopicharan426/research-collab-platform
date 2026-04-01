FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libssl-dev \
    pkg-config \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install MongoDB PHP extension
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Install PDO MySQL (keep for fallback)
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install MongoDB PHP library via Composer
RUN composer require mongodb/mongodb --no-interaction --no-progress

# Set Apache document root to frontend folder
RUN sed -i 's|/var/www/html|/var/www/html/frontend|g' /etc/apache2/sites-available/000-default.conf

# Allow .htaccess overrides
RUN echo '<Directory /var/www/html/frontend>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]
