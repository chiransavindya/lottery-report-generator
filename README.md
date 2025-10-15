# Newspaper Advertisement Automation System

## Overview
A Laravel 9 + React application for automating lottery report generation with multi-language support (English, Sinhala, Tamil) and PDF export capabilities.

## Tech Stack

### Backend
- **Laravel** 9.19
- **PHP** ^8.0.2

### Frontend
- **React** 18.2.0
- **Inertia.js** 1.0.0 (SPA-like experience with Laravel)
- **Vite** 3.0.0 (Build tool)

### Styling
- **Tailwind CSS** 3.1.0
- Custom CSS for lottery tickets

### Libraries
- **html2canvas** 1.4.1 (Screenshot generation)
- **jsPDF** 2.5.1 (PDF export)
- **Axios** 1.1.2 (API calls)

## Features

- ✅ Multi-language support (English, Sinhala, Tamil)
- ✅ PDF generation and export
- ✅ Real-time lottery data fetching via API
- ✅ Responsive design with Tailwind CSS
- ✅ Multiple lottery ticket types:
  - Ada Kotipathi
  - Jaya Sampatha
  - Kapruka
  - Lagna Wasana
  - Sasiri
  - Shanida
  - Superball
  - Supiridana
  - ~~Jayoda~~ (Temporarily disabled)

## Project Structure

```
lottery-report-generator/
├── app/                        # Laravel backend
├── resources/
│   ├── js/
│   │   ├── Pages/             # Inertia.js pages
│   │   │   ├── Report.jsx     # Main report generation
│   │   │   ├── Dashboard.jsx
│   │   │   └── Auth/
│   │   ├── Components/        # React components
│   │   │   ├── *English.jsx   # English lottery tickets
│   │   │   ├── *Sinhala.jsx   # Sinhala lottery tickets
│   │   │   ├── *Tamil.jsx     # Tamil lottery tickets
│   │   │   ├── header*.jsx    # Headers (3 languages)
│   │   │   └── footer*.jsx    # Footers (3 languages)
│   │   └── Layouts/           # Layout components
│   ├── css/                   # Stylesheets
│   │   ├── app.css           # Main Tailwind CSS
│   │   ├── report.css
│   │   └── *.css             # Lottery-specific styles
│   └── views/                # Blade templates
│       └── app.blade.php     # Main Inertia layout
├── public/
│   └── images/               # Lottery logos
├── routes/
│   ├── web.php              # Web routes
│   └── api.php              # API routes
├── composer.json            # PHP dependencies
├── package.json             # Node.js dependencies
└── vite.config.js          # Vite configuration
```

## Installation

### Prerequisites
- PHP >= 8.0.2
- Composer
- Node.js & npm
- MySQL/PostgreSQL

### Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd lottery-report-generator
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Update `.env` with your database credentials

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Build frontend assets**
   ```bash
   npm run dev    # Development
   npm run build  # Production
   ```

7. **Start development server**
   ```bash
   php artisan serve
   ```

## API Endpoints

- `GET /api/lottery` - Fetch lottery data

## Usage

1. Navigate to `/report` page
2. Select lottery type and language
3. View generated report
4. Export as PDF using the download button

## Development

- **Frontend:** React components with Inertia.js
- **Backend:** Laravel API endpoints
- **Styling:** Tailwind CSS + custom lottery styles
- **Build:** Vite for fast HMR (Hot Module Replacement)

## License

[Your License Here]

## Author

[Your Name/Organization]
