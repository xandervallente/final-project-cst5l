#!/bin/sh
echo "=== Starting PHP server ==="
echo "PORT=${PORT}"
echo "PWD=$(pwd)"
exec php -S 0.0.0.0:${PORT} -t /app
