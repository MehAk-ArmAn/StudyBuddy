# StudyBuddy Admin CMS

Admin users are stored in the `admin_users` table and authenticate through `/admin/login`.

## Creating the first admin user

Set these environment variables before running seeders:

- `STUDYBUDDY_ADMIN_EMAIL`
- `STUDYBUDDY_ADMIN_PASSWORD`
- optional: `STUDYBUDDY_ADMIN_NAME`

Then run:

```bash
php artisan migrate --seed
```

If these variables are missing, the seeder intentionally does not create a default admin account. This prevents public hardcoded credentials from being exposed in the app or views.
