# Local setup (safe defaults)

**Read first:** Actual setup details depend on your environment. The archive shows Laravel 11/Blade, Composer, PHPUnit and a separate optional React Bits area. The project Mac has PHP 8.5.7, Laravel 11 and MySQL for local development according to supplied logs.

```bash
cd "$HOME/Desktop/StudyBuddy-main"
composer install
cp .env.example .env  # ONLY on a new checkout; never overwrite an existing .env
php artisan key:generate  # ONLY on a new isolated local environment, not on an active install
php artisan migrate:status
php artisan route:list
php artisan storage:link
php artisan serve
```

**Never** run `migrate:fresh`, `db:wipe`, `db:seed` or a password rotation on an existing environment just to follow an onboarding guide. Confirm DB_CONNECTION and database target **without displaying credentials**. Use a private backup before migrations. The current developer setup includes private local QA fixture accounts; do not publish them or use them on production.

Run `php vendor/bin/phpunit --colors=never` (expected to use in-memory SQLite as configured in `phpunit.xml`) and `php artisan view:cache` for view compilation. When testing new work, watch for PHPUnit-generated `storage/framework/testing` files getting staged accidentally.

**Dependency check:** Use `composer.json` as the PHP source of truth and check `package.json` to determine whether a Node/Vite build is required for the particular feature. Do not assume that `CLAUDE.md` is current.
