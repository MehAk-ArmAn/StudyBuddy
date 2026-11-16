# StudyBuddy Project Structure

StudyBuddy now follows a cleaner Laravel-style structure inspired by the organized BangTan project:

```text
app/                  Application code: controllers, middleware, models
bootstrap/            Laravel bootstrapping and generated cache
config/               Configuration files
database/             Migrations and seeders
public/               Public entry point and browser assets
public/assets/studybuddy-imgs/  Git submodule / external asset library
resources/views/      Blade views grouped by feature
routes/web.php        Core Laravel web routes
routes/studybuddy.php All StudyBuddy feature routes in one organized registry
storage/              Runtime logs/cache/uploads, not source code
vendor/               Composer dependencies, not edited manually
```

## Route rule
Do not create new `routes/studybuddy_phase*.php` patch files anymore. Add StudyBuddy routes inside `routes/studybuddy.php`, grouped by feature.

## Asset rule
Use the GitHub image library under:

```text
public/assets/studybuddy-imgs/
```

Brand logo path:

```text
assets/studybuddy-imgs/brand/logo-icon.png
```

Main tagline:

```text
Learn. Play. Grow. Your Way.
```

## Admin rule
The main admin experience is:

```text
/admin/control-room
```

Legacy `/admin/studybuddy/...` names are kept only so older forms do not break.
