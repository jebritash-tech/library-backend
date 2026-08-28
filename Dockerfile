FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# تثبيت الحزم مع تخطي السكريبتات التلقائية لتفادي أخطاء مرحلة البناء
RUN composer install --no-dev --optimize-autoloader --no-scripts

# إعطاء الصلاحيات وإنشاء مجلد وقاعدة بيانات SQLite إن لم تكن موجودة
RUN mkdir -p /var/www/html/database && \
    touch /var/www/html/database/database.sqlite && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

RUN a2enmod rewrite

COPY .render/apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

# أمر التشغيل الديناميكي لتنظيف الكاش، تطبيق الجداول، ثم تشغيل خادم الويب
CMD ["sh", "-c", "php artisan config:clear && php artisan cache:clear && php artisan migrate --force && apache2-foreground"]
