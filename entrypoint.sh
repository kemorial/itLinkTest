#!/bin/sh
# docker/entrypoint.sh

set -e

echo "🔄 Waiting for PostgreSQL to be ready..."

# Максимальное количество попыток
MAX_RETRIES=30
RETRY_COUNT=0

# Ждем, пока PostgreSQL примет подключения
until php -r "new PDO('pgsql:host=database;port=5432;dbname=car_service', 'postgres', 'superPassword');" > /dev/null 2>&1; do
    RETRY_COUNT=$((RETRY_COUNT + 1))
    if [ $RETRY_COUNT -eq $MAX_RETRIES ]; then
        echo "❌ PostgreSQL not available after $MAX_RETRIES attempts"
        exit 1
    fi
    echo "⏳ Waiting for PostgreSQL... ($RETRY_COUNT/$MAX_RETRIES)"
    sleep 2
done

echo "✅ PostgreSQL is ready. Checking migration status..."

# Проверяем существование таблицы миграций через Yii2
# (более чистый способ, чем прямой SQL-запрос)
MIGRATION_TABLE_EXISTS=0

# Пробуем выполнить команду migrate/history
if php yii migrate/history 1 > /dev/null 2>&1; then
    MIGRATION_TABLE_EXISTS=1
fi

if [ "$MIGRATION_TABLE_EXISTS" -eq 0 ]; then
    echo "🚀 First run detected (no migration table). Applying ALL migrations..."
    php yii migrate --interactive=0
    echo "✅ Initial migrations completed."
else
    echo "📌 Migration table exists. Checking for new migrations..."
    
    # Проверяем, есть ли непримененные миграции
    # migrate/new возвращает 1 если есть новые миграции
    if php yii migrate/new --interactive=0 2>&1 | grep -q "No new migrations"; then
        echo "✅ No new migrations. Schema is up to date."
    else
        echo "🆕 New migrations found. Applying..."
        php yii migrate --interactive=0
    fi
fi

echo "starting application..."
exec php /var/www/app/yii serve 0.0.0.0
