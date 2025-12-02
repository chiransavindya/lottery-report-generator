# Lottery Report Generator - Cross-Platform Deployment Guide

## Overview
This application is a full-stack lottery report generator with:
- **Backend**: Laravel 9 (PHP 8.2)
- **Frontend**: React 18 + Vite 4 + Inertia.js
- **Database**: MySQL 8.0
- **Server**: Nginx + PHP-FPM
- **Containerization**: Docker + Docker Compose

## Directory Structure
```
lottery-report-generator_ReadyToDeploy/
├── client/                 # Frontend (React/Vite)
│   ├── src/
│   │   ├── Components/    # React components
│   │   ├── Layouts/       # Layout components
│   │   ├── Pages/         # Page components
│   │   ├── css/           # Stylesheets
│   │   ├── app.jsx        # Main entry point
│   │   └── bootstrap.js   # Bootstrap file
│   ├── package.json
│   ├── vite.config.js
│   └── tailwind.config.js
├── server/                # Backend (Laravel)
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── .env.example
│   └── composer.json
├── docker/                # Docker configurations
│   ├── nginx/
│   │   └── default.conf
│   ├── php/
│   │   └── Dockerfile
│   └── mysql/
├── docker-compose.yml
├── deploy.sh             # Deployment script
└── dbCheck.sh           # Database health check
```

## Prerequisites

### All Operating Systems
- Docker Desktop (or Docker Engine + Docker Compose)
- Git (optional, for version control)

### Platform-Specific Installation

#### Windows
1. Install Docker Desktop for Windows
   - Download from: https://www.docker.com/products/docker-desktop
   - Enable WSL 2 backend (recommended)
   - Ensure virtualization is enabled in BIOS

#### macOS
1. Install Docker Desktop for Mac
   - Download from: https://www.docker.com/products/docker-desktop
   - For Apple Silicon (M1/M2), use the ARM64 version

#### Linux
1. Install Docker Engine
   ```bash
   curl -fsSL https://get.docker.com -o get-docker.sh
   sudo sh get-docker.sh
   ```
2. Install Docker Compose
   ```bash
   sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
   sudo chmod +x /usr/local/bin/docker-compose
   ```

## Deployment Instructions

### 1. Clone or Extract the Project
```bash
# If using Git
git clone <repository-url>
cd lottery-report-generator_ReadyToDeploy

# Or extract the zip/tar file
```

### 2. Run the Deployment Script

#### Linux/macOS
```bash
chmod +x deploy.sh
./deploy.sh
```

#### Windows (PowerShell)
```powershell
# Run with bash (Git Bash or WSL)
bash deploy.sh

# Or use Docker Compose directly
docker compose up -d --build
```

### 3. Access the Application
- **Application**: http://localhost:8080
- **Vite Dev Server**: http://localhost:5173 (for HMR)

## Configuration

### Environment Variables
The application uses environment variables defined in `server/.env`:

```env
APP_NAME="Lottery Report Generator"
APP_URL=http://localhost:8080
DB_HOST=db
DB_DATABASE=NewspaperReport_gen
DB_USERNAME=root
DB_PASSWORD=Abcd@1234
```

### Ports
- **8080**: Nginx (Application)
- **5173**: Vite Dev Server (Hot Module Replacement)
- **3306**: MySQL (Internal only)
- **9000**: PHP-FPM (Internal only)

## Docker Services

### 1. app (PHP-FPM)
- Base: `php:8.2-fpm`
- Extensions: PDO, MySQL, Zip, GD
- Healthcheck: Included
- Working Directory: `/var/www/server`

### 2. node (Vite Dev Server)
- Base: `node:20-alpine`
- Purpose: Hot Module Replacement for frontend
- Working Directory: `/var/www/client`

### 3. nginx (Web Server)
- Base: `nginx:stable-alpine`
- Configuration: Custom Laravel-optimized config
- Max Upload Size: 100MB

### 4. db (MySQL)
- Base: `mysql:8.0`
- Database: `NewspaperReport_gen`
- Healthcheck: Included
- Persistent Volume: `db_data`

## Development Workflow

### Starting the Application
```bash
./deploy.sh
```

### Stopping the Application
```bash
docker compose down
```

### Viewing Logs
```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f app
docker compose logs -f node
docker compose logs -f nginx
docker compose logs -f db
```

### Rebuilding After Changes
```bash
# Full rebuild
./deploy.sh --no-cache

# Or manually
docker compose down
docker compose build --no-cache
docker compose up -d
```

### Running Artisan Commands
```bash
docker compose exec app php artisan <command>

# Examples:
docker compose exec app php artisan migrate
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:list
```

### Installing PHP Dependencies
```bash
docker compose exec app composer install
docker compose exec app composer update
```

### Installing Node Dependencies
```bash
docker compose exec node npm install
docker compose exec node npm update
```

## Database Management

### Check Database Connection
```bash
./dbCheck.sh
```

### Access MySQL CLI
```bash
docker compose exec db mysql -uroot -pAbcd@1234 NewspaperReport_gen
```

### Backup Database
```bash
docker compose exec db mysqldump -uroot -pAbcd@1234 NewspaperReport_gen > backup.sql
```

### Restore Database
```bash
docker compose exec -T db mysql -uroot -pAbcd@1234 NewspaperReport_gen < backup.sql
```

## Troubleshooting

### Port Already in Use
If ports 8080 or 5173 are already in use:

1. Edit `docker-compose.yml`:
```yaml
ports:
  - "8081:80"  # Change 8080 to 8081
```

2. Update `server/.env`:
```env
APP_URL=http://localhost:8081
```

### Permission Issues (Linux)
```bash
# Fix storage permissions
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### Frontend Not Hot Reloading
1. Ensure the node container is running:
```bash
docker compose ps node
```

2. Check node logs:
```bash
docker compose logs -f node
```

3. Restart node service:
```bash
docker compose restart node
```

### Database Connection Failed
1. Wait for MySQL to fully initialize (15-30 seconds)
2. Check database health:
```bash
./dbCheck.sh
```

3. Verify credentials in `server/.env`

### Build Failures
1. Clean Docker cache:
```bash
docker system prune -a
```

2. Rebuild without cache:
```bash
./deploy.sh --no-cache
```

## Production Deployment

### Building for Production
```bash
# Build frontend assets
docker compose exec node npm run build

# Optimize Laravel
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

### Environment Configuration
1. Copy `server/.env.example` to `server/.env`
2. Update:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_KEY` (generate with `php artisan key:generate`)
   - Database credentials
   - `APP_URL` to your production domain

### Security Considerations
- Change default database password
- Generate new `APP_KEY`
- Use HTTPS in production
- Configure proper firewall rules
- Regular security updates

## Cross-Platform Notes

### Windows-Specific
- Use Git Bash or WSL for running shell scripts
- Line endings: Ensure scripts use LF, not CRLF
- Docker Desktop must be running before deployment

### macOS-Specific
- Apple Silicon (M1/M2): Docker images are multi-arch compatible
- File permissions are handled automatically

### Linux-Specific
- May need to add user to docker group:
  ```bash
  sudo usermod -aG docker $USER
  ```
- Logout and login for changes to take effect

## File Upload Configuration

The application supports XML file uploads with:
- Max file size: 100MB (configurable in `docker/nginx/default.conf`)
- Allowed types: `.xml`
- Upload endpoint: `/upload`

To change max upload size, edit:
1. `docker/nginx/default.conf`: `client_max_body_size`
2. Rebuild: `docker compose restart nginx`

## Maintenance

### Regular Tasks
1. **Update Dependencies**:
   ```bash
   docker compose exec app composer update
   docker compose exec node npm update
   ```

2. **Clear Caches**:
   ```bash
   docker compose exec app php artisan cache:clear
   docker compose exec app php artisan config:clear
   docker compose exec app php artisan route:clear
   docker compose exec app php artisan view:clear
   ```

3. **Database Backups**: Schedule regular backups

### Monitoring
- Check container health: `docker compose ps`
- View resource usage: `docker stats`
- Monitor logs: `docker compose logs -f`

## Support

For issues or questions:
1. Check logs: `docker compose logs -f`
2. Verify all services are running: `docker compose ps`
3. Check database connectivity: `./dbCheck.sh`
4. Review this documentation

## License
[Your License Here]
