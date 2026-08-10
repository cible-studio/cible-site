FROM php:8.3-fpm-alpine

# ─────────────────────────────────────────────────────────────────────
# Image ultra-légère pour le site vitrine CIBLE (statique + Blade).
#
# Pas de MySQL (site vitrine sans BDD → carte réseau via JSON statique
# public/data/reseau-map.json). Pas de Redis. Pas de migrate.
# Extensions PHP réduites au strict nécessaire (opcache + gd + zip
# pour composer + xml pour Blade/Laravel).
# ─────────────────────────────────────────────────────────────────────
RUN apk add --no-cache nodejs npm git unzip curl zip && \
    curl -sSLf -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions opcache gd zip xml

RUN echo "upload_max_filesize=10M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size=10M"       >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "memory_limit=256M"       >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_execution_time=60"   >> /usr/local/etc/php/conf.d/uploads.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install deps prod uniquement (pas de --dev pour alléger)
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Assets (Vite build si assets JS/CSS custom — non requis ici mais
# on garde au cas où on ajoute des scripts custom plus tard).
RUN if [ -f package.json ] && [ -d resources/js ]; then npm ci && npm run build || true; fi

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

# Pas de migrate (pas de BDD). Juste cache warmup + serveur PHP built-in.
CMD ["sh", "-c", "php artisan storage:link 2>/dev/null || true && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php -S 0.0.0.0:8000 -t public"]
