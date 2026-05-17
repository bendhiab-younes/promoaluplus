# Laravel Common Commands

## 1. Clear All Caches (Most Common Fix)

```bash
php artisan optimize:clear
```

Clears: config, cache, routes, views, events, and compiled classes.

```bash
php artisan config:clear && php artisan cache:clear
```

## 2. Rebuild Cached Files

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 3. Verify Translations

```bash
php artisan tinker --execute="echo __('messages.site_description');"
```

## 4. Check Routes & Config

```bash
php artisan route:list
php artisan about
```

## 5. Database

```bash
php artisan migrate:status          # Check pending migrations
php artisan migrate                  # Run pending migrations
php artisan db:seed --force          # Re-run seeders if needed
php artisan migrate:fresh            # Drop all tables & re-migrate (destructive!)
php artisan migrate:fresh --seed     # Fresh + seed
```

## 6. Permissions

```bash
chmod -R 775 storage bootstrap/cache
```

## 7. Production Deployment

```bash
php artisan optimize
```

## 8. Browse Routes

```bash
php artisan route:list --compact
php artisan route:list --path=api
```

## 9. Clear Specific Cache

```bash
php artisan cache:clear          # Clear application cache
php artisan config:clear         # Clear config cache
php artisan route:clear          # Clear route cache
php artisan view:clear           # Clear compiled views
php artisan event:clear         # Clear event cache
```

## 10. Debug

```bash
php artisan tinker                           # Interactive PHP REPL
php artisan make:request MyRequest          # Create form request
php artisan make:model MyModel -m            # Create model + migration
php artisan make:seeder MyTableSeeder        # Create seeder
```

## Quick Fix Checklist (When Something Isn't Working)

1. `php artisan optimize:clear`
2. `php artisan config:clear`
3. **Browser cache refresh** (Ctrl+Shift+R)
4. Check `.env` configuration
5. Run `php artisan route:list` to verify routes exist
6. Check `storage/logs/laravel.log` for errors

---

**Important**: Always clear **browser cache** too - especially when using CDNs like Cloudflare.