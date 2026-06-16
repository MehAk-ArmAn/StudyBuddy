# StudyBuddy

## Clean Main CMS Build

This branch is the cleaned main StudyBuddy website build: one public homepage, one frontend layout, one navbar, one footer, and one admin CMS that controls homepage content.

### Removed prototype/demo systems

Old public prototype routes and their route entries were removed for app-store pages, app play pages, rewards, student dashboards, parent dashboards, teacher dashboards, showcase pages, demo pages, fake pricing/support redirects, and old portal-style authentication pages. Prototype-only controllers, models, seeders, views, and the prototype content migration were removed so `migrate:fresh --seed` creates only the final CMS-focused data model.

### Final public route

- `GET /` named `home` renders the database-driven homepage.

### Final admin routes

- `GET /admin/login`
- `POST /admin/login`
- `POST /admin/logout`
- `GET /admin/dashboard`
- CRUD resources for site settings, media assets, navigation items, footer items, homepage sections, and homepage section items.

### Database tables used

- `users` with `is_admin` for admin access
- `site_settings`
- `media_assets`
- `navigation_items`
- `footer_items`
- `homepage_sections`
- `homepage_section_items`
- Laravel framework tables: `cache`, `jobs`, `sessions`, `password_reset_tokens`

### Admin login details

- Email: `admin@studybuddy.local`
- Password: `ChangeMe12345!`

### Migration and seed commands

```bash
composer dump-autoload
php artisan optimize:clear
php artisan migrate:fresh --seed
```

### CMS editing locations

- Homepage sections: `/admin/homepage-sections`
- Cards/items inside sections: `/admin/homepage-sections/{section}/items`
- Navbar links: `/admin/navigation-items`
- Footer links, legal groups, and social links: `/admin/footer-items`
- Media/image records: `/admin/media-assets`
- SEO, brand, logo, favicon, CTA, and legal text settings: `/admin/site-settings`

### Backup before future changes

Create a safety branch before future major edits:

```bash
git checkout main
git pull
git checkout -b backup-before-next-studybuddy-change
git push origin backup-before-next-studybuddy-change
```

Emergency backup branch note: `backup-main-safe` exists only as an emergency backup and should not be modified.
