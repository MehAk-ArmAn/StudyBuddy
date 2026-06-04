# StudyBuddy

StudyBuddy is a Laravel 11 foundation for a premium cosmic learning universe. It uses Blade views, custom CSS/JS assets, MySQL-ready migrations, demo seeders, and a dark navy galaxy interface with purple/cyan glow, 3D glass panels, floating planets, comet motion, Play Store style mini app cards, and a dolphin/book mascot direction.

## Stack

- Laravel 11
- PHP 8.2+
- MySQL 8+ or compatible MariaDB
- Blade views
- Public CSS and JavaScript assets in `public/assets`
- Database-backed sessions, cache, and queues for local parity with MySQL

## Required routes

All required routes are registered in `routes/web.php`:

| Path | Purpose |
| --- | --- |
| `/` | Cosmic landing page |
| `/showcase` | Design showcase |
| `/apps` | Mini app constellation |
| `/apps/math-quest` | Math Quest product page |
| `/apps/math-quest/play` | Math Quest playable prototype route |
| `/demo/primary` | Primary learner dashboard demo |
| `/demo/secondary` | Secondary learner dashboard demo |
| `/demo/parent` | Parent dashboard demo |
| `/demo/teacher` | Teacher dashboard demo |
| `/rewards` | Cosmic rewards catalogue |
| `/demo/admin` | Admin console demo |

## MySQL setup

StudyBuddy is configured for MySQL in `.env.example`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=studybuddy
DB_USERNAME=root
DB_PASSWORD=
```

Create a local database before running migrations:

```sql
CREATE DATABASE studybuddy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

If you use a dedicated MySQL user, create one and grant access:

```sql
CREATE USER 'studybuddy'@'localhost' IDENTIFIED BY 'secret-password';
GRANT ALL PRIVILEGES ON studybuddy.* TO 'studybuddy'@'localhost';
FLUSH PRIVILEGES;
```

Then update your copied `.env` file:

```dotenv
DB_CONNECTION=mysql
DB_DATABASE=studybuddy
DB_USERNAME=studybuddy
DB_PASSWORD=secret-password
```

The database foundation is intentionally MySQL-first with no file-database fallback configured.

## Install and preview

From the repository root:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan route:list
php artisan serve
```

Open the app at [http://localhost:8000](http://localhost:8000).

## Demo login data

The seeders create demo users for later authentication work. Each demo account uses the password `password`:

- `primary@studybuddy.test`
- `secondary@studybuddy.test`
- `parent@studybuddy.test`
- `teacher@studybuddy.test`
- `admin@studybuddy.test`

## Project foundation map

- `routes/web.php` defines all browser routes.
- `app/Http/Controllers` contains page controllers for home, apps, dashboards, and rewards.
- `app/Models` contains Eloquent models for users, mini apps, rewards, dashboard cards, and site content.
- `database/migrations` contains MySQL-compatible schema migrations.
- `database/seeders` contains demo seeders for users, mini apps, rewards, dashboards, and site content.
- `resources/views/layouts` contains the main Blade layout.
- `resources/views/partials` contains reusable navigation, footer, mascot, app card, and dashboard card partials.
- `resources/views/pages`, `resources/views/apps`, and `resources/views/demo` contain route-specific pages.
- `public/assets/css/studybuddy.css` contains the custom cosmic UI.
- `public/assets/js/studybuddy.js` contains lightweight UI motion and prototype interactions.
