# Route organization

`routes/web.php` is Laravel's entrypoint and includes module files under `routes/studybuddy*.php`. Read current route list (`php artisan route:list`) instead of assuming filename order. Ensure routes have named links, required auth/middleware and controller permission enforcement. Add a link in relevant role navigation only when authorization matches. [Inventory](../docs/handbook/generated/ROUTE_INVENTORY.md).
