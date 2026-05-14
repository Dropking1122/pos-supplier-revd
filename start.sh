#!/bin/bash
MYSQL_DATA=/home/runner/mysql-data
MYSQL_RUN=/home/runner/mysql-run
MYSQL_SOCK=$MYSQL_RUN/mysql.sock

mkdir -p $MYSQL_RUN

# Install PHP dependencies if not present
if [ ! -f /home/runner/workspace/vendor/autoload.php ]; then
    echo "Installing PHP dependencies..."
    cd /home/runner/workspace && composer install --no-interaction 2>&1
fi

# Install Node dependencies and build assets if not present
if [ ! -d /home/runner/workspace/public/build ]; then
    echo "Building frontend assets..."
    cd /home/runner/workspace && npm install && npm run build 2>&1
fi

# Init MySQL data dir if not present
if [ ! -d "$MYSQL_DATA/mysql" ]; then
    echo "Initializing MySQL data directory..."
    mysqld --initialize-insecure --datadir=$MYSQL_DATA --user=runner 2>/dev/null
fi

mysql_ping_ok() {
    mysqladmin -u root -ppos_password --socket="$MYSQL_SOCK" ping 2>/dev/null | grep -q alive
}

if mysql_ping_ok; then
    echo "MySQL already running."
else
    echo "Starting MySQL..."
    mysqld \
      --datadir=$MYSQL_DATA \
      --socket=$MYSQL_SOCK \
      --pid-file=$MYSQL_RUN/mysql.pid \
      --port=3306 \
      --mysqlx=OFF \
      --bind-address=127.0.0.1 \
      --user=runner > $MYSQL_DATA/mysql.log 2>&1 &

    # Wait for MySQL to be ready
    for i in $(seq 1 30); do
        sleep 1
        if mysqladmin -u root --socket="$MYSQL_SOCK" ping 2>/dev/null | grep -q alive; then
            echo "MySQL ready! Setting up database..."
            mysql -u root --socket="$MYSQL_SOCK" 2>/dev/null << 'SQL'
CREATE DATABASE IF NOT EXISTS pos_supplier CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'pos_password';
FLUSH PRIVILEGES;
SQL
            break
        elif mysql_ping_ok; then
            echo "MySQL ready (password already set)."
            break
        fi
        echo "Waiting for MySQL... ($i/30)"
    done
fi

echo "Starting Laravel..."
cd /home/runner/workspace

# Create .env if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

# Patch .env with correct DB credentials and APP_URL for this environment
if [ -f .env ]; then
    sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=pos_password|" .env
    grep -q "^DB_SOCKET=" .env && sed -i "s|^DB_SOCKET=.*|DB_SOCKET=$MYSQL_SOCK|" .env || echo "DB_SOCKET=$MYSQL_SOCK" >> .env
    if [ -n "$REPLIT_DEV_DOMAIN" ]; then
        sed -i "s|^APP_URL=.*|APP_URL=https://$REPLIT_DEV_DOMAIN|" .env
    fi
fi

php artisan config:clear 2>/dev/null || true
php artisan storage:link --force 2>/dev/null || true
php artisan migrate --force 2>/dev/null || true
php artisan db:seed --force 2>/dev/null || true

# Run Laravel Scheduler in background
php artisan schedule:work >> /tmp/scheduler.log 2>&1 &
echo "Laravel Scheduler running (PID: $!)"

php artisan serve --host=0.0.0.0 --port=5000
