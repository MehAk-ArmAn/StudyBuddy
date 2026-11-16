# StudyBuddy

Small learning games, plus the website and admin CMS that publish them.
Laravel 11 + Blade, no JavaScript build step.

## Getting started

```bash
composer install
cp .env.example .env
php artisan key:generate

# Local development uses sqlite. Production uses MySQL (see .env.example).
touch database/database.sqlite
# then set DB_CONNECTION=sqlite in .env

php artisan migrate --seed
php artisan storage:link          # required, or uploaded app images will 404
php artisan serve
```

Open http://localhost:8000

## Admin

| | |
|---|---|
| URL | `/admin/login` |
| Email | `admin@studybuddy.local` |
| Password | `ChangeMe12345!` |

**Change this password after your first sign-in.** You can also set
`STUDYBUDDY_ADMIN_EMAIL` and `STUDYBUDDY_ADMIN_PASSWORD` in `.env` before
seeding. The seeder is idempotent — running it again updates the same account
rather than creating a second administrator.

## Adding an app

StudyBuddy ships with an **empty app catalogue on purpose**. Apps are added
through the admin — never by editing code, templates or database rows.

1. Admin → **Apps** → **Add app**
2. **The basics** — name, category, one-line summary. The web address is
   generated from the name.
3. **Artwork** — upload a card image and a cover image (or paste a path).
4. **Availability & launching**
   - *Browser version*: tick **Playable in the browser**, then either upload a
     ZIP containing `index.html`, or paste an external `https://` launch address.
   - *App stores*: paste the Google Play / App Store links you have. Leave the
     rest blank — buttons only appear for platforms you fill in.
5. **Save as draft**, then use **Preview page** and **Test browser launch**.
6. **Save & publish.** It is live on `/apps` immediately.

To hide an app later, use **Unpublish** — do not delete it. Deleting is
permanent, requires typing the app name, and removes its uploaded files.

## Brand

| | |
|---|---|
| Name | **StudyBuddy** |
| Slogan | **Learn. Play. Grow. Your Way.** |
| Domain | StudyBuddy.fun (used only where a domain belongs) |

Brand values and icon paths live in `config/studybuddy.php`; the admin can
override them under Site Settings. All identity artwork is local, in
`public/assets/studybuddy-brand/`, so the site keeps its logo and favicon even
if an external host is unreachable.

## Where things live

| Area | Path |
|---|---|
| Apps CMS | `/admin/control-room/apps` |
| Homepage content | `/admin/control-room/homepage-cms` |
| Pages (About, Support, …) | `/admin/pages` |
| Header & footer | `/admin/control-room/shell` |
| Site settings | `/admin/control-room/site-settings` |
| Users | `/admin/control-room/users` |
| Contact inbox | `/admin/control-room/messages` |
| Platform, checklist, points | `/admin/control-room/final-platform` |

## Public routes

`/` · `/apps` · `/apps/{slug}` · `/play/{slug}` (browser launcher) ·
`/for-parents` · `/for-teachers` · `/about-us` · `/support` ·
`/privacy-policy` · `/data-deletion` · `/contact`

These URLs are relied on by published mobile apps — treat them as a contract and
change them additively.

## Tests

```bash
./vendor/bin/phpunit
```

Runs against an in-memory sqlite database, so it never touches your data.

## Deployment notes

- Set `APP_DEBUG=false` and a real `APP_KEY`.
- Run `php artisan storage:link` on the server.
- `storage/app/studybuddy-web-apps/` holds extracted browser builds. Keep it
  writable and include it with `storage/app/studybuddy-app-packages/` in backups;
  do not expose either directory directly through the web server.
- `git submodule update --init` restores the shared image library in
  `public/assets/studybuddy-imgs`.
- Never commit `.env`.

## Further reading

- `CLAUDE.md` — conventions and constraints for anyone (or any agent) changing this code
- `docs/STUDYBUDDY_QA.md` — what the rescue pass fixed and verified
