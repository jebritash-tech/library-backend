FROM php:8.2-apache

# تثبيت الحزم المطلوبة لـ Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev

# تفعيل وحدات الـ Apache
RUN a2enmod rewrite

# تثبيت إضافات PHP المطلوبة
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تحديد مجلد العمل
WORKDIR /var/www/html

# نسخ ملفات المشروع
COPY . .

# ضبط مسار الـ Document Root لـ Apache ليطير إلى مجلد public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -s 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -s 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# تثبيت حزم الفندور الصامتة
RUN composer install --no-dev --optimize-autoloader

# إعطاء صلاحيات الكتابة لمجلدات التخزين والكاش
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# أمر التشغيل التلقائي عند إقلاع الحاوية
CMD php artisan config:clear && php artisan cache:clear && apache2-foreground
