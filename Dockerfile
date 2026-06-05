FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-req=ext-intl

FROM node:24-alpine AS assets
WORKDIR /app
COPY package.json pnpm-lock.yaml pnpm-workspace.yaml .npmrc ./
RUN corepack enable && pnpm install --frozen-lockfile
COPY . .
RUN pnpm run build

FROM dunglas/frankenphp

WORKDIR /app

RUN install-php-extensions pdo_pgsql pcntl intl

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public ./public
COPY Caddyfile /etc/caddy/Caddyfile

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80 443
