# Imagen base con PHP y Apache
FROM php:8.2-apache

# Información del mantenedor
LABEL maintainer="jersson14071996@gmail.com"
LABEL description="Sistema Tours Micaela - Facturación Electrónica SUNAT"

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP requeridas
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    soap

# Habilitar extensión OpenSSL (ya viene habilitada por defecto)
RUN docker-php-ext-enable opcache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Apache
RUN a2enmod rewrite headers

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . /var/www/html/

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Crear directorios necesarios y establecer permisos
RUN mkdir -p greenter/xml greenter/cdr greenter/pdf greenter/certificados \
    && mkdir -p Fotos controller/usuario/fotos controller/choferes/fotos controller/empresa/FOTOS \
    && mkdir -p view/MPDF \
    && chmod -R 755 greenter/xml greenter/cdr greenter/pdf \
    && chmod -R 755 Fotos \
    && chmod -R 755 controller/usuario/fotos \
    && chmod -R 755 controller/choferes/fotos \
    && chmod -R 755 controller/empresa/FOTOS \
    && chmod -R 755 view/MPDF \
    && chown -R www-data:www-data /var/www/html

# Configurar PHP
RUN echo "upload_max_filesize = 20M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "date.timezone = America/Lima" >> /usr/local/etc/php/conf.d/timezone.ini

# Configurar Apache VirtualHost
RUN echo '<VirtualHost *:80>\n\
    ServerAdmin admin@toursmicaela.com\n\
    DocumentRoot /var/www/html\n\
    <Directory /var/www/html>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Exponer puerto 80
EXPOSE 80

# Comando de inicio
CMD ["apache2-foreground"]
