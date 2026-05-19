FROM php:8.2-cli

RUN docker-php-ext-install mysqli pdo_mysql

WORKDIR /app
COPY . .

RUN chmod +x /app/start.sh

CMD ["/app/start.sh"]
