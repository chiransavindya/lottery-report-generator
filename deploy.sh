#!/bin/bash

# If the script is invoked with 'sh' (dash), re-exec with bash
if [ -z "${BASH_VERSION:-}" ]; then
    exec /usr/bin/env bash "$0" "$@"
fi

set -euo pipefail

echo "🧪 Starting local Docker run for Lottery Report Generator..."

# Resolve project directory to the location of this script
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.yml}"

cd "$PROJECT_DIR"

# Basic prerequisites
if ! command -v docker > /dev/null 2>&1; then
    echo "❌ Docker is not installed or not on PATH"
    exit 1
fi
if ! docker compose version > /dev/null 2>&1; then
    echo "❌ Docker Compose v2 is required (the 'docker compose' subcommand)"
    exit 1
fi

# Ensure server/.env exists
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

# Helpful env sanity check (non-fatal)
if [ -f server/.env ]; then
    if ! grep -q '^DB_HOST=db\b' server/.env; then
        echo "ℹ️  Note: For Docker Compose, set DB_HOST=db in your .env (current may differ)."
    fi
fi

# Stop old containers
echo "🛑 Stopping any running containers..."
docker compose -f "$COMPOSE_FILE" down

# Build images (support optional --no-cache)
BUILD_FLAGS=()
if [[ "${1:-}" == "--no-cache" ]]; then
    BUILD_FLAGS+=(--no-cache)
fi
echo "🏗️  Building Docker images..."
docker compose -f "$COMPOSE_FILE" build "${BUILD_FLAGS[@]}"

# Start containers
echo "🚀 Starting containers..."
docker compose -f "$COMPOSE_FILE" up -d

# Wait for services to initialize
echo "⏳ Waiting for services to initialize..."
sleep 15

# Run Laravel commands inside the app container (dev-oriented: clear caches)
echo "🧹 Running Laravel setup (dev)..."
docker compose -f "$COMPOSE_FILE" exec -T app sh -c "if [ ! -d vendor ]; then echo '📦 Installing Composer dependencies...'; composer install; fi"
docker compose -f "$COMPOSE_FILE" exec -T app php artisan config:clear || true
docker compose -f "$COMPOSE_FILE" exec -T app php artisan cache:clear || true
docker compose -f "$COMPOSE_FILE" exec -T app php artisan route:clear || true
docker compose -f "$COMPOSE_FILE" exec -T app php artisan view:clear || true
docker compose -f "$COMPOSE_FILE" exec -T app php artisan storage:link || true
docker compose -f "$COMPOSE_FILE" exec -T app php artisan migrate || true

# Fix permissions inside container
echo "🔐 Ensuring writable directories..."
docker compose -f "$COMPOSE_FILE" exec -T app sh -c "chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache" || true

# Health check from host (matches nginx exposed port 8080)
APP_PORT=8080
APP_HOST=localhost
HEALTH_URL="http://${APP_HOST}:${APP_PORT}/"
echo "🔍 Checking application health at ${HEALTH_URL} ..."
if curl -fs "${HEALTH_URL}" > /dev/null; then
    echo "✅ Application is healthy and running locally!"
else
    echo "❌ Application health check failed!"
    echo "🪵 Showing last 50 log lines for troubleshooting..."
    docker compose -f "$COMPOSE_FILE" logs --tail=50
    exit 1
fi

# Show running containers
echo ""
echo "📋 Running containers:"
docker compose -f "$COMPOSE_FILE" ps

echo ""
echo "✅ Local environment is up!"
echo "🌐 Open: ${HEALTH_URL%/}"
