FROM php:8.2-cli

RUN docker-php-ext-install mysqli pdo_mysql

WORKDIR /app
COPY . .

RUN printf '#!/bin/sh\nexec php -S 0.0.0.0:$PORT -t /app\n' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]
