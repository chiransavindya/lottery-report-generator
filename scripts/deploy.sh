#!/bin/bash

# If the script is invoked with 'sh' (dash), re-exec with bash
if [ -z "${BASH_VERSION:-}" ]; then
    exec /usr/bin/env bash "$0" "$@"
fi

set -euo pipefail

# Default to prod if no argument provided
MODE="${1:-prod}"
DOMAIN="news.dlb.lk"

if [ "$MODE" = "dev" ]; then
    echo "🧪 Starting in DEVELOPMENT mode..."
    COMPOSE_FILES="-f docker-compose.yml -f docker-compose.dev.yml"
    TARGET_ENV_FILE="server/.env"
    APP_ENV="local"
    APP_DEBUG="true"
    # In dev, we keep the hot file (Vite creates it)
    REMOVE_HOT=false
elif [ "$MODE" = "prod" ]; then
    echo "🚀 Starting in PRODUCTION mode..."
    COMPOSE_FILES="-f docker-compose.yml -f docker-compose.prod.yml"
    TARGET_ENV_FILE="server/.env.production"
    APP_ENV="production"
    APP_DEBUG="false"
    REMOVE_HOT=true
else
    echo "❌ Invalid mode. Use 'dev' or 'prod'."
    exit 1
fi

# Resolve project directory to the location of this script
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_DIR"

# Basic prerequisites
if ! command -v docker > /dev/null 2>&1; then
    echo "❌ Docker is not installed or not on PATH"
    exit 1
fi

# Ensure server/.env exists (required for base docker-compose.yml)
if [ ! -f server/.env ]; then
    echo "⚠️  server/.env not found. Copying from .env.example..."
    if [ -f server/.env.example ]; then
        cp server/.env.example server/.env
        echo "✅ Created server/.env"
    else
        echo "❌ server/.env.example not found! Cannot create .env."
        exit 1
    fi
fi

# Ensure .env.production exists if in prod mode
if [ "$MODE" = "prod" ] && [ ! -f server/.env.production ]; then
    echo "⚠️  server/.env.production not found. Copying from server/.env..."
    cp server/.env server/.env.production
    echo "✅ Created server/.env.production"
fi

# Update .env file based on mode
# We use sed to update the values in place
if [ "$(uname)" = "Darwin" ]; then SED_I="sed -i ''"; else SED_I="sed -i"; fi

echo "🔧 Updating $TARGET_ENV_FILE for $MODE mode..."
$SED_I "s/^APP_ENV=.*/APP_ENV=$APP_ENV/" "$TARGET_ENV_FILE"
$SED_I "s/^APP_DEBUG=.*/APP_DEBUG=$APP_DEBUG/" "$TARGET_ENV_FILE"
$SED_I "s|^APP_URL=.*|APP_URL=http://$DOMAIN|" "$TARGET_ENV_FILE"

# Export variables for docker-compose
export APP_DOMAIN="$DOMAIN"
export APP_PORT=80

# Stop old containers
echo "🛑 Stopping any running containers..."
docker compose $COMPOSE_FILES down --remove-orphans

# Remove hot file if in prod mode
if [ "$REMOVE_HOT" = "true" ]; then
    if [ -f server/public/hot ]; then
        echo "🔥 Removing stale 'hot' file..."
        rm server/public/hot
    fi
fi

# Build images
echo "🏗️  Building Docker images..."
docker compose $COMPOSE_FILES build

# Start containers
echo "🚀 Starting containers..."
docker compose $COMPOSE_FILES up -d

# Wait logic
if [ "$MODE" = "prod" ]; then
    # Wait for frontend build to complete
    echo "⏳ Waiting for frontend build to complete..."
    docker compose $COMPOSE_FILES logs -f node &
    PID=$!
    while [ "$(docker compose $COMPOSE_FILES ps -q node | xargs docker inspect -f '{{.State.Running}}' 2>/dev/null)" = "true" ]; do
        sleep 2
    done
    kill $PID 2>/dev/null || true
    echo "✅ Frontend build complete."
else
    echo "⏳ Starting Vite dev server in background..."
    # No need to wait, it runs continuously
fi

# Wait for services to initialize
echo "⏳ Waiting for database and server to initialize..."
sleep 10

# Run Laravel commands
echo "🧹 Running Laravel setup..."

# Install dependencies
if [ "$MODE" = "prod" ]; then
    docker compose $COMPOSE_FILES exec -T app sh -c "if [ ! -d vendor ]; then echo '📦 Installing Composer dependencies...'; composer install --no-dev --optimize-autoloader; fi"
else
    docker compose $COMPOSE_FILES exec -T app sh -c "if [ ! -d vendor ]; then echo '📦 Installing Composer dependencies...'; composer install; fi"
fi

# Fix permissions
echo "🔐 Ensuring writable directories..."
docker compose $COMPOSE_FILES exec -T app sh -c "chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache" || true

# Migrations and Cache
echo "🗄️  Running migrations..."
docker compose $COMPOSE_FILES exec -T app php artisan migrate --force

if [ "$MODE" = "prod" ]; then
    echo "⚡ Optimizing application..."
    docker compose $COMPOSE_FILES exec -T app php artisan optimize:clear
    docker compose $COMPOSE_FILES exec -T app php artisan config:cache
    docker compose $COMPOSE_FILES exec -T app php artisan route:cache
    docker compose $COMPOSE_FILES exec -T app php artisan view:cache
else
    echo "🧹 Clearing cache for dev..."
    docker compose $COMPOSE_FILES exec -T app php artisan optimize:clear
fi

docker compose $COMPOSE_FILES exec -T app php artisan storage:link || true

# Health check
HEALTH_URL="http://localhost:80/"
PUBLIC_URL="http://$DOMAIN"

echo "🔍 Checking application health..."
if curl -fs "${HEALTH_URL}" > /dev/null; then
    echo "✅ Application is healthy!"
else
    echo "⚠️  Application check returned an error or timed out."
fi

echo ""
echo "✅ Deployment complete ($MODE mode)!"
echo "🌐 URL: ${PUBLIC_URL}"
if [ "$MODE" = "dev" ]; then
    echo "⚡ Vite HMR: ${PUBLIC_URL}:5173"
fi
