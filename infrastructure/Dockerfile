# Dockerfile
# マルチステージビルドでイメージサイズを最適化
FROM php:8.3-fpm-alpine AS base

# システムパッケージのインストール
# ランタイム依存パッケージのみ
RUN apk update && apk add --no-cache \
    mysql-client \
    git \
    unzip \
    supervisor \
    icu \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
    oniguruma \
    curl \
    libxml2 \
    bash \
    sudo \
    shadow

# ビルドステージ: PHP拡張機能のコンパイル
FROM base AS builder

# ビルド依存パッケージのインストール
RUN apk add --no-cache --virtual .build-deps \
    icu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    curl-dev \
    libxml2-dev \
    pcre-dev \
    $PHPIZE_DEPS

# PHP拡張機能の構成とインストール
# openssl, session, tokenizer, curl, mbstring, xml関連 は標準で有効なため除外
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        opcache \
        pcntl \
        exif \
        bcmath \
        gd \
        mysqli \
        zip \
        intl

# Redis拡張機能のインストール
RUN pecl install redis \
    && docker-php-ext-enable redis

# 最終ステージ: 本番イメージ
FROM base

# ビルドステージから拡張機能をコピー
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# PHP設定ファイルのコピー
COPY ./php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY ./php/php-fpm.conf /usr/local/etc/php-fpm.d/zz-custom.conf

# 権限対策: www-dataのUID/GIDを変更可能にする
ARG HOST_UID=1000
ARG HOST_GID=1000
RUN deluser www-data 2>/dev/null || true \
    && delgroup www-data 2>/dev/null || true \
    && addgroup -g ${HOST_GID} www-data \
    && adduser -D -u ${HOST_UID} -G www-data -s /bin/bash www-data \
    && mkdir -p /home/www-data \
    && chown -R www-data:www-data /home/www-data \
    && echo "www-data ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/www-data

# 作業ディレクトリの設定
WORKDIR /var/www/html

# ディレクトリ所有権の設定
RUN chown -R www-data:www-data /var/www/html

# ポートの公開（Nginxとの通信用）
EXPOSE 9000

# php-fpmをrootで起動（内部でwww-dataにダウングレード）
CMD ["php-fpm"]