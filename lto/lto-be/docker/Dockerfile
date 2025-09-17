FROM php:8.4.10-fpm

# 必要なパッケージのインストール
RUN apt-get update && \
    apt-get install -y \
        zip unzip libzip-dev locales git libxml2-dev libonig-dev curl msmtp mailutils supervisor \
        && docker-php-ext-install mysqli mbstring pdo pdo_mysql zip soap opcache pcntl

# PECL拡張のインストール
RUN pecl install redis xdebug grpc-1.66.0 protobuf && \
    docker-php-ext-enable redis xdebug grpc protobuf

# クリーンアップ
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# ロケール・タイムゾーン設定
RUN echo "ja_JP.UTF-8 UTF-8" >> /etc/locale.gen && locale-gen ja_JP.UTF-8
ENV LANG=ja_JP.UTF-8 \
    LANGUAGE=ja_JP:ja \
    LC_ALL=ja_JP.UTF-8 \
    TZ=Asia/Tokyo
RUN ln -snf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime && echo "Asia/Tokyo" > /etc/timezone

# Composerのインストール
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Node.jsとnpmのインストール
RUN curl -fsSL https://deb.nodesource.com/setup_current.x | bash - && \
    apt-get update && apt-get install -y nodejs

# 一般ユーザーとグループを追加
ARG UID=1000
ARG GID=1000
ARG USER=appuser
ARG GROUP=appgroup

RUN if ! getent group ${GROUP} > /dev/null 2>&1; then \
      if ! getent group ${GID} > /dev/null 2>&1; then \
        groupadd -g ${GID} ${GROUP}; \
      else \
        groupadd ${GROUP}; \
      fi \
    fi \
 && useradd -l -u ${UID} -g ${GROUP} -m -s /bin/bash ${USER}

RUN mkdir -p /var/log && chown ${USER}:${GROUP} /var/log

ARG APP_CODE_PATH_CONTAINER
RUN mkdir -p ${APP_CODE_PATH_CONTAINER} && chown ${USER}:${GROUP} ${APP_CODE_PATH_CONTAINER}

# Ensure supervisor directories have proper permissions
RUN mkdir -p /var/log/supervisor && chown ${USER}:${GROUP} /var/log/supervisor
RUN mkdir -p /var/run/supervisor && chown ${USER}:${GROUP} /var/run/supervisor

# Expose FPM port
EXPOSE 9000

# Set working directory
WORKDIR ${APP_CODE_PATH_CONTAINER}
# Copy startup script
COPY ./start.sh /start.sh

# コンテナ起動時にstartup scriptを実行 (php-fpm + queue worker 管理)
CMD ["/start.sh"]
