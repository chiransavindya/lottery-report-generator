#!/bin/bash

# If the script is invoked with 'sh' (dash), re-exec with bash
if [ -z "${BASH_VERSION:-}" ]; then
    exec /usr/bin/env bash "$0" "$@"
fi

set -euo pipefail

# Configuration
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
COMPOSE_FILE="docker-compose.yml"
DB_CONTAINER="db"
DB_NAME="NewspaperReport_gen"
DB_USER="root"
DB_PASS="Abcd@1234"

cd "$PROJECT_DIR"

# 🧩 Database connectivity check
echo "🔍 Checking database connectivity..."
if docker compose exec -T $DB_CONTAINER mysql -u$DB_USER -p$DB_PASS -e "USE $DB_NAME;" > /dev/null 2>&1; then
    echo "✅ Database connection successful — '$DB_NAME' is accessible!"
else
    echo "❌ Database connection failed!"
    echo "🪵 Showing DB container logs..."
    docker compose logs $DB_CONTAINER --tail=30
    exit 1
fi
