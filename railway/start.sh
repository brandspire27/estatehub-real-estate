#!/bin/sh
set -eu

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY must be set in Railway variables." >&2
    exit 1
fi

export DB_CONNECTION=mysql
export DB_HOST="${DB_HOST:-${MYSQLHOST:-}}"
export DB_PORT="${DB_PORT:-${MYSQLPORT:-3306}}"
export DB_DATABASE="${DB_DATABASE:-${MYSQLDATABASE:-}}"
export DB_USERNAME="${DB_USERNAME:-${MYSQLUSER:-}}"
export DB_PASSWORD="${DB_PASSWORD:-${MYSQLPASSWORD:-}}"

if [ -z "${APP_URL:-}" ] && [ -n "${RAILWAY_PUBLIC_DOMAIN:-}" ]; then
    export APP_URL="https://${RAILWAY_PUBLIC_DOMAIN}"
fi

if [ -z "$DB_HOST" ] || [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ]; then
    echo "Attach a Railway MySQL service and provide its MYSQL* variables." >&2
    exit 1
fi

touch storage/installed
php artisan migrate --force

user_count="$(php -r '$pdo = new PDO("mysql:host=" . getenv("DB_HOST") . ";port=" . getenv("DB_PORT") . ";dbname=" . getenv("DB_DATABASE") . ";charset=utf8mb4", getenv("DB_USERNAME"), getenv("DB_PASSWORD")); echo $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();' | tr -d '\r')"
if [ "${user_count:-0}" = "0" ]; then
    php artisan db:seed --force
    php artisan db:seed --class=LocalitySeeder --force
    php artisan db:seed --class=PackageSeeder --force
fi

php artisan optimize

exec php -S "0.0.0.0:${PORT:-8080}" -t public public/index.php