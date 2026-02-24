# LRMS v2 (Lottery Report Management System)

An advanced web application for managing lottery data, processing XML draws, and generating comprehensive multilingual PDF reports with automated batch validation and queue processing.

![Logo](public/images/logo/logo.png)

---

## 🌟 Project Overview

**LRMS v2** is an enterprise-grade lottery report management system built for the **Development Lotteries Board**. It automates the entire workflow from XML data ingestion to multi-language PDF report generation, supporting 8 different lottery types with complete audit trails and role-based access control.

### Quick Stats
- **Languages Supported**: 3 (English, Sinhala, Tamil)
- **Lottery Types**: 8 (AK, DS, LW, SB, KP, JS, SR, SF)
- **User Roles**: 3 (Super Admin, Admin, Operator)
- **Tech Stack**: Laravel 12, PHP 8.4, MySQL 8, Redis, Docker
- **Deployment**: Fully containerized with Docker Compose

---

## ⚡ Quick Start

Get LRMS v2 running in under 5 minutes:

```bash
# 1. Clone the repository
git clone <repository-url>
cd LRMS

# 2. Copy environment file
cp .env.example .env

# 3. Start Docker containers
docker-compose up -d --build

# 4. Install dependencies
docker-compose exec php composer install

# 5. Generate app key
docker-compose exec php php artisan key:generate

# 6. Setup database
docker-compose exec php php artisan migrate --seed

# 7. Build frontend assets
npm install && npm run build

# 8. Access the application
# Open http://localhost in your browser
# Login: sadmin.dlb@lrms.com / password
```

That's it! 🎉

For detailed setup instructions, see [Installation & Setup](#-installation--setup).

## 📋 Table of Contents
- [Project Overview](#-project-overview)
- [About](#-about)
- [Features](#-features)
- [System Architecture](#-system-architecture)
- [Tech Stack](#-tech-stack)
- [Prerequisites](#-prerequisites)
- [Installation & Setup](#-installation--setup)
  - [Docker Setup (Recommended)](#docker-setup-recommended)
  - [Local Development Setup](#local-development-setup)
- [Configuration](#-configuration)
- [Database Setup](#-database-setup)
- [Running the Application](#-running-the-application)
- [Custom Artisan Commands](#-custom-artisan-commands)
- [Usage Guide](#-usage-guide)
- [Project Structure](#-project-structure)
- [Supported Lottery Types](#-supported-lottery-types)
- [API Endpoints](#-api-endpoints)
- [Security Features](#-security-features)
- [System Requirements & Limits](#-system-requirements--limits)
- [Performance Optimization](#-performance-optimization)
- [Troubleshooting](#-troubleshooting)
- [Environment Variables](#-environment-variables)
- [Deployment Considerations](#-deployment-considerations)
- [Development Team](#-development-team)
- [License](#-license)
- [Support & Documentation](#-support--documentation)

---

## 📖 About

LRMS v2 is a comprehensive lottery report management system designed to automate and streamline the lottery reporting process for the Development Lotteries Board. The system processes XML data from lottery draws, validates the data against business rules, and generates professional PDF reports in multiple languages (English, Sinhala, Tamil) along with consolidated formats.

### Key Capabilities
- **Automated XML Processing**: Parse and validate lottery draw data from XML files
- **Multi-language Support**: Generate reports in English, Sinhala, and Tamil
- **Batch Validation**: Ensure complete lottery sets (8 types) for each draw date
- **Queue Processing**: Background job processing for performance optimization
- **Audit Trail**: Comprehensive logging of all system activities
- **Role-based Access**: Granular permissions for different user types

---

## ✨ Features

### User Management
- **Role-based Access Control (RBAC)**: Three distinct roles with specific permissions
  - **Super Admin**: Full system access, user management, audit logs
  - **Admin**: Batch deletion, report management, data oversight
  - **Operator**: File uploads, report generation, data validation
- **Secure Authentication**: Session-based auth with password hashing (bcrypt)
- **Password Validation**: Minimum 8 characters requirement

### Data Processing
- **XML File Upload**: Support for single and batch XML file uploads (up to 50MB)
- **Smart Parsing**: Automatic extraction of lottery draw data with error handling
- **Batch Validation**: Validates that all 8 required lottery types are present per draw date
- **Background Processing**: Queue-based XML processing for optimal performance
- **Data Integrity**: Transaction support and rollback on validation failures

### Reporting System
- **Multi-language PDF Generation**: Reports in English, Sinhala, and Tamil
- **Consolidated Reports**: Combined reports across multiple lottery types
- **Custom Branding**: Lottery-specific logos and styling
- **Download Management**: Secure PDF download with proper headers
- **Report History**: Track all generated reports with timestamps

### Dashboard & Analytics
- **Role-specific Dashboards**: Customized views based on user permissions
- **Real-time Statistics**: Live counts of batches, files, reports, and users
- **Recent Activity**: Quick access to latest uploads and reports
- **System Health**: Monitor queue status and processing metrics

### Security & Compliance
- **XSS Protection**: Comprehensive input sanitization
- **CSRF Protection**: Token-based request validation
- **SQL Injection Prevention**: Query parameterization
- **Audit Logging**: Complete activity tracking for compliance
- **File Validation**: XML structure and content verification

---

## 🏗 System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                     LRMS v2 Architecture                │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌───────────┐     ┌─────────────┐    ┌────────────┐  │
│  │   Nginx   │────▶│  PHP-FPM    │───▶│   MySQL    │  │
│  │  (Port 80)│     │  (Laravel)  │    │ (Port 3306)│  │
│  └───────────┘     └─────────────┘    └────────────┘  │
│                           │                            │
│                           ▼                            │
│                    ┌─────────────┐                     │
│                    │    Redis    │                     │
│                    │ (Queue/Cache)│                    │
│                    └─────────────┘                     │
│                           │                            │
│                           ▼                            │
│                    ┌─────────────┐                     │
│                    │Queue Worker │                     │
│                    │ (Background)│                     │
│                    └─────────────┘                     │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Core Services
1. **XmlParserService**: Handles XML parsing and data extraction
2. **BatchValidationService**: Validates lottery batches and completeness
3. **ReportGenerationService**: Generates PDFs in multiple languages
4. **FileUploadService**: Manages file uploads and storage
5. **NotificationService**: Handles system notifications and alerts

---

## 🛠 Tech Stack

### Backend
- **Framework**: Laravel 12.x
- **Language**: PHP 8.4 (FPM)
- **Database**: MySQL 8.0 / SQLite (Development)
- **Cache & Queue**: Redis (Alpine)
- **PDF Generation**: DomPDF 3.1, FPDF/FPDI

### Frontend
- **Template Engine**: Blade (Laravel)
- **CSS Framework**: Tailwind CSS 4.0
- **JavaScript**: Vanilla JS, Axios
- **Build Tool**: Vite 7.0
- **Client-side PDF**: jsPDF, html2canvas

### DevOps & Infrastructure
- **Containerization**: Docker & Docker Compose
- **Web Server**: Nginx (Alpine)
- **Process Manager**: PHP-FPM
- **Development**: Laravel Pail, Tinker
- **Code Quality**: Laravel Pint

### Development Tools
- **Package Manager**: Composer 2.x, NPM
- **Version Control**: Git
- **Testing**: PHPUnit 11.5, Mockery
- **Debugging**: Laravel Telescope (optional)

---

## 📋 Prerequisites

### Required Software
- **Docker**: Version 20.10 or higher
- **Docker Compose**: Version 2.0 or higher
- **Git**: For cloning the repository

### For Local Development (Without Docker)
- **PHP**: 8.4 or higher
- **Composer**: 2.x
- **Node.js**: 18.x or higher
- **NPM**: 9.x or higher
- **MySQL**: 8.0 or higher (or SQLite for development)
- **Redis**: 6.x or higher (optional, can use database queue)

### System Requirements
- **RAM**: Minimum 2GB (4GB recommended)
- **Storage**: 10GB free space
- **OS**: Linux, macOS, or Windows with WSL2

---

## 🚀 Installation & Setup

### Docker Setup (Recommended)

#### 1. Clone the Repository
```bash
git clone <repository-url>
cd LRMS
```

#### 2. Environment Configuration
```bash
# Copy the example environment file
cp .env.example .env

# Update .env with Docker MySQL credentials (already configured in docker-compose.yml)
# DB_CONNECTION=mysql
# DB_HOST=mysql
# DB_PORT=3306
# DB_DATABASE=lrms
# DB_USERNAME=lrms_user
# DB_PASSWORD=lrms_password
```

#### 3. Build and Start Docker Containers
```bash
# Build and start all services in detached mode
docker-compose up -d --build

# This will start:
# - nginx (port 80)
# - php-fpm (port 9000)
# - mysql (port 3306)
# - redis (port 6379)
# - queue worker (background)
```

#### 4. Install PHP Dependencies
```bash
docker-compose exec php composer install
```

#### 5. Generate Application Key
```bash
docker-compose exec php php artisan key:generate
```

#### 6. Run Database Migrations and Seeders
```bash
# Run migrations to create database tables
docker-compose exec php php artisan migrate

# Seed default users and lottery types
docker-compose exec php php artisan db:seed
```

#### 7. Install Frontend Dependencies and Build Assets
```bash
# Install Node.js dependencies
npm install

# Build Vite assets for production
npm run build

# OR run in development mode with hot reload
npm run dev
```

#### 8. Set Permissions (if needed)
```bash
docker-compose exec php chown -R www-data:www-data /var/www/html/storage
docker-compose exec php chmod -R 755 /var/www/html/storage
docker-compose exec php chmod -R 755 /var/www/html/bootstrap/cache
```

#### 9. Access the Application
Open your browser and navigate to:
```
http://localhost
```

---

### Local Development Setup

#### 1. Clone the Repository
```bash
git clone <repository-url>
cd LRMS
```

#### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

#### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# For SQLite (Development):
DB_CONNECTION=sqlite

# For MySQL (Production):
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=lrms
# DB_USERNAME=root
# DB_PASSWORD=your_password
```

#### 4. Database Setup
```bash
# Create SQLite database file (if using SQLite)
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed default data
php artisan db:seed
```

#### 5. Build Assets
```bash
# Build for production
npm run build

# OR run development server with hot reload
npm run dev
```

#### 6. Start the Application
```bash
# Using Laravel's built-in server
php artisan serve

# Start queue worker (in a separate terminal)
php artisan queue:work

# Access at http://localhost:8000
```

---

## ⚙️ Configuration

### Key Configuration Files

#### Database Configuration
Edit `config/database.php` or use `.env` variables:
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=lrms
DB_USERNAME=lrms_user
DB_PASSWORD=lrms_password
```

#### Queue Configuration
Edit `config/queue.php` or use `.env`:
```env
QUEUE_CONNECTION=database  # or redis
REDIS_HOST=redis
REDIS_PORT=6379
```

#### File Upload Limits
Edit `docker/nginx/default.conf`:
```nginx
client_max_body_size 50M;
```

Edit `docker/php/custom.ini`:
```ini
upload_max_filesize = 50M
post_max_size = 50M
```

#### Lottery Types Configuration
Edit `config/lotteries.php` to modify the 8 required lottery types:
- AK (Ada Kotipathi)
- DS (Supiri Dhana Sampatha)
- LW (Lagna Wasanawa)
- SB (Super Ball)
- KP (Kapruka)
- JS (Jaya Sampatha)
- SR (Sasiri)
- SF (Shanida)

---

## 💾 Database Setup

### Default Users & Credentials

After running `php artisan db:seed`, the following users are created:

| Role | Email | Password | Permissions |
|------|-------|----------|-------------|
| **Super Admin** | `sadmin.dlb@lrms.com` | `password` | Full system access, user management, audit logs |
| **Admin** | `admin.dlb@lrms.com` | `password` | Batch deletion, report oversight, data management |
| **Operator** | `ops.dlb@lrms.com` | `password` | File uploads, report generation, data entry |

> ⚠️ **Security Note**: Change these default passwords immediately in production!

### Database Tables
The system uses 9 core tables:

| Table | Purpose | Key Fields |
|-------|---------|------------|
| `users` | User accounts and authentication | email, password, role, is_active |
| `lottery_types` | Supported lottery types (8 types) | code, name_en, name_si, name_ta |
| `upload_batches` | Batch upload tracking | status, total_files, processed_files |
| `upload_files` | Individual XML file tracking | filename, status, file_path, error_message |
| `draws` | Parsed lottery draw data | lottery_type_id, draw_number, draw_date |
| `reports` | Generated PDF reports | type, language, status, file_path |
| `audit_logs` | System activity logging | user_id, action, model_type, details |
| `jobs` | Queue job tracking | queue, payload, attempts, available_at |
| `cache` | Application cache | key, value, expiration |

### Relationships
- `upload_batches` → `upload_files` (One-to-Many)
- `upload_files` → `draws` (One-to-One)
- `lottery_types` → `draws` (One-to-Many)
- `users` → `upload_batches` (One-to-Many)
- `users` → `reports` (One-to-Many)
- `users` → `audit_logs` (One-to-Many)

### Indexes
All foreign keys and frequently queried columns are indexed for optimal performance.

---

## 🎮 Running the Application

### Using Docker (Recommended)

```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f

# Stop all services
docker-compose down

# Restart specific service
docker-compose restart php

# View queue worker logs
docker-compose logs -f queue
```

### Useful Docker Commands

```bash
# Access PHP container shell
docker-compose exec php bash

# Run artisan commands
docker-compose exec php php artisan <command>

# Clear caches
docker-compose exec php php artisan cache:clear
docker-compose exec php php artisan config:clear
docker-compose exec php php artisan view:clear

# Check queue status
docker-compose exec php php artisan queue:work --once

# Database operations
docker-compose exec php php artisan migrate:fresh --seed  # Reset database
```

### Local Development

```bash
# Start Laravel server
php artisan serve

# Start queue worker (separate terminal)
php artisan queue:work

# Start Vite dev server (separate terminal)
npm run dev

# Watch for file changes
php artisan pail
```

---

## 🛠 Custom Artisan Commands

The system includes custom maintenance commands:

### Cleanup Commands

```bash
# Delete old PDF reports (default: 7 days)
php artisan reports:cleanup-old
php artisan reports:cleanup-old --days=30  # Custom retention period

# Clean up draft reports
php artisan reports:cleanup-drafts

# Delete processed XML files to save disk space
php artisan uploads:cleanup
```

### Scheduled Tasks

The following tasks run automatically (configured in `routes/console.php`):

| Task | Schedule | Description |
|------|----------|-------------|
| `reports:cleanup-old --days=7` | Daily at 2:00 AM | Removes PDF reports older than 7 days |
| `uploads:cleanup` | Daily at 3:00 AM | Deletes processed XML files to free storage |

> **Note**: Ensure the queue worker is running for scheduled tasks to execute properly.

---

## 🕹 Usage Guide

### 1. Login
- Navigate to `http://localhost` (Docker) or `http://localhost:8000` (local)
- Use one of the default credentials listed above
- First-time login redirects to dashboard

### 2. Dashboard
- **Super Admin**: View all system statistics, user counts, audit logs
- **Admin**: Monitor batches, files, reports, and recent activity
- **Operator**: Access upload and report sections

### 3. Upload XML Files
1. Navigate to **Uploads** from the sidebar
2. Click **New Batch Upload**
3. Select one or multiple XML files (up to 50MB each)
4. System automatically:
   - Parses XML data
   - Validates lottery types
   - Checks for complete sets (8 lotteries per draw date)
   - Processes files in queue
5. View batch status and validation results

### 4. Generate Reports
1. Navigate to **Reports** from the sidebar
2. Click **Generate New Report**
3. Select:
   - Draw date
   - Report type (English, Sinhala, Tamil, Consolidated)
   - Lottery types (if applicable)
4. Click **Generate**
5. Download PDF when ready

### 5. User Management (Super Admin Only)
1. Navigate to **Users**
2. Add, edit, or deactivate users
3. Assign roles and permissions
4. Monitor user activity in audit logs

### 6. View Batches and Files
- Click on any batch to view:
  - Uploaded files
  - Validation status
  - Parsed draw data
  - Associated reports
- Delete batches (Admin/Super Admin only)

---

## 📁 Project Structure

```
LRMS/
├── app/
│   ├── Console/Commands/       # Artisan commands
│   ├── Http/
│   │   ├── Controllers/        # Application controllers
│   │   ├── Middleware/         # Custom middleware
│   │   └── Requests/           # Form request validation
│   ├── Jobs/
│   │   └── ProcessXmlFile.php  # Queue job for XML processing
│   ├── Models/                 # Eloquent models
│   │   ├── User.php
│   │   ├── LotteryType.php
│   │   ├── UploadBatch.php
│   │   ├── UploadFile.php
│   │   ├── Draw.php
│   │   ├── Report.php
│   │   └── AuditLog.php
│   └── Services/               # Business logic services
│       ├── XmlParserService.php
│       ├── BatchValidationService.php
│       ├── ReportGenerationService.php
│       ├── FileUploadService.php
│       └── NotificationService.php
├── config/                     # Configuration files
│   ├── lotteries.php          # Lottery types config
│   ├── reports.php            # Report generation config
│   └── ...
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── docker/                    # Docker configuration
│   ├── nginx/
│   │   └── default.conf       # Nginx config
│   ├── php/
│   │   ├── Dockerfile         # PHP-FPM Dockerfile
│   │   └── custom.ini         # PHP configuration
│   └── mysql/
├── docs/                      # Documentation
│   ├── PDF_GENERATION.md
│   ├── PDF_QUICK_START.md
│   └── ACCORDION_UI.md
├── public/                    # Public assets
│   ├── css/
│   ├── images/
│   └── build/                 # Compiled Vite assets
├── resources/
│   ├── css/                   # Source CSS
│   ├── js/                    # Source JavaScript
│   └── views/                 # Blade templates
├── routes/
│   ├── web.php               # Web routes
│   └── console.php           # Console routes
├── storage/                   # File storage
│   ├── app/                  # Uploaded files
│   ├── logs/                 # Application logs
│   └── framework/            # Framework files
├── docker-compose.yml         # Docker Compose config
├── composer.json             # PHP dependencies
├── package.json              # Node.js dependencies
└── vite.config.js            # Vite configuration
```

---

## 🎰 Supported Lottery Types

The system requires all 8 lottery types for a complete draw batch:

| Code | English Name | Sinhala Name | Tamil Name |
|------|--------------|--------------|------------|
| **AK** | Ada Kotipathi | ඇද කෝටිපති | அட கோடிபதி |
| **� Deployment Considerations

### Production Checklist

- [ ] **Change default passwords** for all seeded users
- [ ] **Set `APP_ENV=production`** in `.env`
- [ ] **Disable debug mode** (`APP_DEBUG=false`)
- [ ] **Configure proper APP_URL** with your domain
- [ ] **Use strong database credentials**
- [ ] **Enable HTTPS** with SSL certificates
- [ ] **Configure Redis** for queue and cache
- [ ] **Set up automated backups** for database
- [ ] **Configure email settings** for notifications
- [ ] **Review file upload limits** based on requirements
- [ ] **Set up log rotation** for `storage/logs/`
- [ ] **Enable scheduled tasks** with cron
- [ ] **Monitor disk space** for storage/app/
- [ ] **Configure firewall rules** (allow only 80/443)
- [ ] **Set up monitoring** (uptime, errors, performance)

### Enabling Scheduled Tasks in Production

Add to your server's crontab:
```bash
* * * * * cd /path/to/LRMS && php artisan schedule:run >> /dev/null 2>&1
```

Or with Docker:
```bash
* * * * * docker-compose exec -T php php artisan schedule:run >> /dev/null 2>&1
```

### Backup Strategy

#### Database Backup
```bash
# Manual backup
docker-compose exec mysql mysqldump -u lrms_user -p lrms > backup_$(date +%Y%m%d).sql

# Automated daily backup (add to cron)
0 1 * * * docker-compose exec -T mysql mysqldump -u lrms_user -plrms_password lrms | gzip > /backups/lrms_$(date +\%Y\%m\%d).sql.gz
```

#### Files Backup
```bash
# Backup storage directory
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/app/
```

### SSL/HTTPS Configuration

For production, update `docker/nginx/default.conf`:
```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com;
    
    ssl_certificate /etc/nginx/ssl/cert.pem;
    ssl_certificate_key /etc/nginx/ssl/key.pem;
    
    # ... rest of configuration
}

server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}
```

### Environment-Specific Settings

#### Development
```env
APP_ENV=local
APP_DEBUG=true
LOG_LEVEL=debug
```

#### Staging
```env
APP_ENV=staging
APP_DEBUG=true
LOG_LEVEL=info
```

#### Production
```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=warning
```

---

## �DS** | Supiri Dhana Sampatha | සුපිරි ධන සම්පත | சுபிரி தன சம்பத |
| **LW** | Lagna Wasanawa | ලග්න වාසනාව | லக்னா வாசனாவா |
| **SB** | Super Ball | සුපර් බෝල් | சூப்பர் பால் |
| **KP** | Kapruka | කප්රුක | கப்ருக |
| **JS** | Jaya Sampatha | ජය සම්පත | ஜெய சம்பத |
| **SR** | Sasiri | සසිරි | சசிரி |
| **SF** | Shanida | ශනිදා | ஷானிடா |

---

## 👥 Development Team

### LRMS v2 Development
- **[Chiran Savindya](https://github.com/chiransavindya/)** - Lead Developer
- **[Niwantha Sithumal](https://github.com/N1wan7ha)** - Lead Developer

### LRMS v1 Foundation
- **[Nimesha Madurangi](https://github.com/NimeshaMadurangi/report-generation)** - Original LRMS v1 Creator

---

## 🤝 Contributing

This is a proprietary system developed for the Development Lotteries Board. For contributions or enhancements:

1. Contact the development team
2. Follow Laravel coding standards
3. Write comprehensive tests for new features
4. Update documentation accordingly
5. Submit detailed change requests

---

## 📄 License

© 2025-2026 LRMS v2. All rights reserved.

Developed for the **Development Lotteries Board**, Sri Lanka.

This software is proprietary and confidential. Unauthorized copying, distribution, or use is strictly prohibited.

---

## � Troubleshooting

### Common Issues

#### Queue Jobs Not Processing
```bash
# Check queue worker status
docker-compose logs queue

# Restart queue worker
docker-compose restart queue

# Manually process a job
docker-compose exec php php artisan queue:work --once
```

#### File Upload Errors
- **Issue**: "File too large" error
- **Solution**: Check `docker/nginx/default.conf` (client_max_body_size) and `docker/php/custom.ini` (upload_max_filesize, post_max_size)
- **Default Limits**: 50MB per file

#### Database Connection Issues
```bash
# Check MySQL container status
docker-compose ps mysql

# View MySQL logs
docker-compose logs mysql

# Restart MySQL
docker-compose restart mysql
```

#### Permission Errors
```bash
# Fix storage permissions
docker-compose exec php chown -R www-data:www-data /var/www/html/storage
docker-compose exec php chmod -R 755 /var/www/html/storage
docker-compose exec php chmod -R 755 /var/www/html/bootstrap/cache
```

#### PDF Generation Failures
- **Issue**: PDF not generating or displaying incorrectly
- **Solution**: 
  1. Check `storage/logs/laravel.log` for errors
  2. Ensure DomPDF dependencies are installed
  3. Verify font files in `storage/fonts/`
  4. Check memory limits in `docker/php/custom.ini` (default: 512M)

#### Clear All Caches
```bash
# Clear all application caches
docker-compose exec php php artisan optimize:clear

# Or individually
docker-compose exec php php artisan cache:clear
docker-compose exec php php artisan config:clear
docker-compose exec php php artisan route:clear
docker-compose exec php php artisan view:clear
```

---

## 🔐 Security Features

### Authentication & Authorization
- **Session-based Authentication**: Secure cookie-based sessions
- **Password Hashing**: Bcrypt with configurable rounds (default: 12)
- **Role-based Access Control**: Custom `CheckRole` middleware
- **Account Status**: Active/inactive user management
- **Session Timeout**: 120 minutes (configurable)

### Data Protection
- **CSRF Protection**: Token validation on all POST requests
- **XSS Prevention**: Blade template escaping by default
- **SQL Injection Prevention**: Eloquent ORM with parameterized queries
- **File Validation**: XML structure and malicious content checks
- **Secure File Storage**: Private storage with controlled access

### Audit & Compliance
- **Audit Logs**: Complete tracking of user activities
- **Error Logging**: Comprehensive error tracking and reporting
- **File Integrity**: Checksum validation for uploaded files
- **Access Logs**: Nginx access logs for security monitoring

---

## 📊 System Requirements & Limits

### PHP Configuration (docker/php/custom.ini)
```ini
memory_limit = 512M              # Maximum memory per request
upload_max_filesize = 50M        # Maximum upload file size
post_max_size = 50M              # Maximum POST data size
max_execution_time = 300         # 5 minutes timeout
```

### Queue Processing
- **Timeout**: 300 seconds (5 minutes) per job
- **Retries**: 3 attempts on failure
- **Connection**: Database or Redis
- **Worker**: Dedicated container running in background

### Storage
- **Uploads**: `storage/app/uploads/` (temporary, auto-deleted after processing)
- **Reports**: `storage/app/reports/` (auto-cleanup after 7 days)
- **Logs**: `storage/logs/` (Laravel application logs)
- **Cache**: Database or Redis-based caching

---

## 🌐 API Endpoints

While LRMS v2 is primarily a web application, key routes include:

### Authentication
- `GET /login` - Login form
- `POST /login` - Authenticate user
- `POST /logout` - Logout user

### Dashboard
- `GET /dashboard` - Main dashboard (all roles)

### Uploads (Operator+)
- `GET /uploads` - List all batches
- `POST /uploads` - Upload XML files
- `GET /uploads/{batch}` - View batch details
- `DELETE /uploads/{batch}` - Delete batch (Admin+)

### Reports (Operator+)
- `GET /reports` - List all reports
- `GET /reports/create` - Report generation form
- `POST /reports` - Generate single language report
- `POST /reports/consolidated` - Generate consolidated report
- `GET /reports/{report}/preview/{language}` - Preview PDF
- `GET /reports/{report}/download/{language}` - Download PDF
- `GET /reports/{report}/download-zip` - Download all languages as ZIP
- `GET /reports/{report}/download-merged` - Download merged PDF
- `DELETE /reports/{report}` - Delete report (Admin+)

### User Management (Super Admin)
- `GET /users` - List users
- `POST /users` - Create user
- `GET /users/{user}/edit` - Edit user form
- `PUT /users/{user}` - Update user
- `DELETE /users/{user}` - Delete user

---

## 📞 Support & Documentation

For detailed documentation, refer to the `/docs` directory:
- [PDF Generation Guide](docs/PDF_GENERATION.md)
- [PDF Quick Start](docs/PDF_QUICK_START.md)
- [Accordion UI Guide](docs/ACCORDION_UI.md)

For issues and feature requests, please contact the development team.

---

## 🚀 Performance Optimization

### Production Recommendations

1. **Enable Caching**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. **Use Redis for Queue & Cache**
```env
QUEUE_CONNECTION=redis
CACHE_STORE=redis
REDIS_HOST=redis
```

3. **Optimize Composer Autoloader**
```bash
composer install --optimize-autoloader --no-dev
```

4. **Enable OPcache** (already configured in Docker)

5. **Database Indexing**
- All foreign keys are indexed
- Compound indexes on frequently queried columns

6. **Asset Optimization**
```bash
npm run build  # Minifies CSS/JS for production
```

---

## 📝 Environment Variables

Key environment variables (`.env`):

```env
# Application
APP_NAME="LRMS v2"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

# Database (Docker)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=lrms
DB_USERNAME=lrms_user
DB_PASSWORD=lrms_password

# Queue & Cache
QUEUE_CONNECTION=database    # or redis
CACHE_STORE=database         # or redis

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Security
BCRYPT_ROUNDS=12

# File Storage
FILESYSTEM_DISK=local
```
