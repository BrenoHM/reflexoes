# syntax=docker/dockerfile:1
#
# Build em 3 estágios:
#   1) vendor  - composer install (PHP 8.5 CLI)
#   2) assets  - npm ci && npm run build (Node)
#   3) runtime - imagem final servida por Apache + PHP 8.5 (mod_php)
#
# "assets" copia o vendor/ do estágio 1 antes de rodar o Vite: o
# tailwind.config.js escaneia classes dentro de
# vendor/laravel/framework/.../Pagination (usado por $reflections->links()
# no painel admin), então o CSS de produção só sai completo se o Composer já
# tiver rodado. Nem Node nem o vendor de desenvolvimento vão para a imagem
# final - só o resultado (vendor de produção + assets compilados).

#############################################
# 1) vendor
#############################################
FROM php:8.5-cli AS vendor
WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# mbstring é exigido pelo laravel/framework; libonig-dev é a lib usada para
# compilá-lo. unzip é o fallback do Composer para extrair pacotes quando a
# extensão PHP "zip" não está presente (não instalamos "zip" - a aplicação
# não precisa dela).
RUN apt-get update && apt-get install -y --no-install-recommends \
        $PHPIZE_DEPS \
        libonig-dev \
        unzip \
    && docker-php-ext-install mbstring \
    && rm -rf /var/lib/apt/lists/*

COPY . .

RUN composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction \
        --no-progress \
        --prefer-dist

#############################################
# 2) assets
#############################################
FROM node:24-alpine AS assets
WORKDIR /var/www/html

COPY . .
COPY --from=vendor /var/www/html/vendor ./vendor

RUN npm ci && npm run build

#############################################
# 3) runtime
#############################################
FROM php:8.5-apache AS runtime
WORKDIR /var/www/html

# Extensões realmente usadas pela aplicação:
#   - pdo_mysql: DB_CONNECTION=mysql
#   - mbstring:  exigida pelo laravel/framework
# opcache já vem builtin nesta imagem base (não precisa de
# docker-php-ext-install; só configuração - ver docker/php/production.ini).
# GD/Imagick não entram: o QR Code do Pix usa o writer SVG do
# endroid/qr-code, que não depende de extensão de imagem nenhuma.
RUN apt-get update && apt-get install -y --no-install-recommends \
        $PHPIZE_DEPS \
        libonig-dev \
        tini \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mbstring \
    && a2enmod rewrite \
    && ln -sf /dev/stdout /var/log/apache2/access.log \
    && ln -sf /dev/stderr /var/log/apache2/error.log \
    && apt-get purge -y --auto-remove $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

COPY docker/php/production.ini /usr/local/etc/php/conf.d/zz-production.ini
COPY docker/apache/ports.conf /etc/apache2/ports.conf
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY . .
COPY --from=vendor /var/www/html/vendor ./vendor
COPY --from=assets /var/www/html/public/build ./public/build

# A aplicação não faz upload de arquivos hoje (ver README, seção "Arquivos
# persistentes"), mas storage/ e bootstrap/cache precisam ser graváveis pelo
# Laravel de qualquer forma (logs de emergência, cache de config/rotas/views
# gerado pelo entrypoint a cada start). Só essas pastas ficam com o dono
# www-data - o código da aplicação continua pertencendo ao root.
RUN mkdir -p \
        storage/app/private \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/testing \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Valor só para permitir "docker run" local sem -e PORT=...; em produção o
# Render sempre injeta o seu próprio $PORT, que sobrescreve este default.
ENV PORT=8080
EXPOSE 8080

# tini como PID 1: sem ele, o Apache assume o papel de PID 1 diretamente e
# reage a sinais como SIGWINCH (encaminhado por algumas camadas de
# terminal/orquestração) como se fosse um shutdown gracioso, encerrando o
# container sozinho. tini também garante o reap correto de processos zumbis.
ENTRYPOINT ["/usr/bin/tini", "--", "/usr/local/bin/entrypoint.sh"]
