STUDYBUDDY SPARKLE + LOCAL DEMO CARDS — ADDITIVE PATCH v2

WHAT IT ADDS
- New colorful, responsive homepage discovery cards connected to the existing Apps CMS.
- Topic filtering and a surprise-me button, without fake statistics or new backend APIs.
- Six distinct original SVG illustrations used as local-only concept artwork.
- Explicit DEMO labels on home, app catalogue, and detail screens.
- One opt-in seeder for six fictional sample app records, plus scoped cleanup seeder.

SAFETY
- This ZIP assumes StudyBuddy_NextChapter_PATCH_ONLY.zip was already applied.
- No existing app, school, auth or reward models are replaced.
- No migrations or seeding occurs simply by unzipping the patch.
- Demo seeders refuse to run if APP_ENV is not local/testing, DB isn't local,
  or the apps table does not exist. Seed only on a separate local database.
- The six demo slugs are all prefixed demo-next-, and all say (Demo).
- Demo apps cannot be played or downloaded and do not award points.
- Database seeding never updates existing rows or touches real app data.

INSTALLATION (from ~/Desktop/StudyBuddy-main)
1. Copy this ZIP to ~/Downloads.
2. Stop artisan serve with Ctrl+C.
3. Run: ditto -xk ~/Downloads/StudyBuddy_Sparkle_Demo_v2_PATCH.zip .
4. Run: php artisan optimize:clear
5. Start: php artisan serve
6. Check homepage visual changes at http://127.0.0.1:8000

OPTIONAL SAMPLE DATA SEED (after confirming .env points to LOCAL disposable/test DB)
Run in a separate terminal in the StudyBuddy-main project directory:
php artisan db:seed --class=StudyBuddyLocalDemoAppsSeeder
Refresh http://127.0.0.1:8000 and http://127.0.0.1:8000/apps

REMOVE ONLY DEMOS (local DB only)
php artisan db:seed --class=StudyBuddyRemoveLocalDemoAppsSeeder

NO migrate:fresh, db:wipe, reset --hard or force-push needed.
Actual local DB and PHP integration validation must still run on your Mac.
