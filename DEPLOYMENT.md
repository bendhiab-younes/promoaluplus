# Deploying Promo Alu Plus on an OVHcloud VPS

Target: a live, HTTPS-served public site at `https://promoaluplus.tn` (or your domain) with a working `/admin` panel, quote emails actually sending, and a one-command way to ship changes afterwards.

Written against the state of `main` at the time of writing: Laravel 12.44, PHP ^8.2, Filament 3, Vite/Tailwind 4.

---

## 0. Buying the VPS

### Recommended specs

| Resource | Recommended | Minimum | Why |
|---|---|---|---|
| vCores | **2** | 1 | PHP-FPM + MySQL + a queue worker + nginx. One core survives, two stops you thinking about it |
| RAM | **4 GB** | 2 GB | MySQL alone wants ~512 MB–1 GB. `npm run build` (Vite) peaks around 1–1.5 GB and **will OOM-kill on a busy 2 GB box** |
| Storage | **80 GB NVMe** | 40 GB | The app is small; this is headroom for uploads, MySQL, and backups |
| Bandwidth | Whatever is included | — | A brochure site will not come close |
| OS | **Debian 12** or **Ubuntu 24.04 LTS** | — | This guide uses Debian/Ubuntu package names |
| Datacenter | **Gravelines or Roubaix (France)** | — | Lowest latency to Tunisia of OVH's European regions, and the FR-TN storefront defaults there |

### Chosen: OVH **VPS-1** (2027 lineup)

> 2 vCores · 4 GB RAM · 40 GB SSD NVMe · trafic illimité · 500 Mbit/s
> ~12,92 DT HT / ~15,38 DT TTC per month

This matches the two specs that matter — 2 vCores and 4 GB RAM, the latter being what decides whether `npm run build` survives on the server. The 40 GB disk is below the 80 GB suggested above, but that was headroom rather than a requirement. Measured against the actual project:

| Item | Size |
|---|---|
| OS + PHP + MySQL + nginx + Node | ~6–8 GB |
| `vendor/` (production, `--no-dev`) | ~110 MB |
| `node_modules/` | 51 MB |
| App code + `public/images` | ~25 MB |
| `public/uploads` (all admin uploads today) | 8.7 MB |
| Database | 352 KB |

≈ **9 GB used, 31 GB free.** Uploads would have to grow roughly 100× before storage became the constraint. The only consequence is backup retention — see the note in §10.

**VPS-2** (4 vCores / 8 GB / 75 GB, ~2× the price) is not needed. It would only make sense if this box later also ran Redis, Meilisearch, or a second application; for one Laravel site plus a queue worker, the extra 4 GB sits idle.

> **"Sauvegarde automatisée 1 jour"** included with the VPS is a snapshot of the whole VM from yesterday. It is good for "I broke the server" and useless for "a client deleted an invoice last Tuesday." It does not replace the `mysqldump` cron in §10.

### At order time

- Choose **SSH key** authentication, not password
- Note the IPv4 address — you point DNS at it
- Pick a hostname you will recognise

---

## 1. Project changes required BEFORE you deploy

These are changes to the codebase, not the server. **Do them first** — several are silent failures, not crashes.

### 1.1 Blocker: four seeders read files outside the repo

If you split `alu-workshop-laravel` into its own GitHub repo (recommended), these break:

```
database/seeders/ServiceSeeder.php:15       base_path('../content_docs/json/services.json')
database/seeders/FaqSeeder.php:16           base_path('../content_docs/json/questions_frequentes.json')
database/seeders/SiteSettingsSeeder.php:20  base_path('../content_docs/json/notre_histoire.json')
database/seeders/SiteSettingsSeeder.php:21  base_path('../content_docs/json/service_tunisiens_etranger.json')
```

`readJson()` returns `[]` when a file is missing, so **seeding will succeed while doing nothing** — you get an empty site with no error.

**Fix:** move `content_docs/json/*.json` (84 KB, 5 files) into the repo at `database/seeders/content/`, and change the four paths to `database_path('seeders/content/....json')`.

### 1.2 A first-time visitor sees the site in English

`config/app.php:81` reads `env('APP_LOCALE', 'en')` and `.env.example` sets `APP_LOCALE=en`. `SetLocale` middleware only overrides it once a visitor picks a language:

```php
$locale = session('locale', config('app.locale'));
```

So the first page view renders **English UI chrome**, while all database content (`getTranslatedTitle()` and friends) falls back to **French**. A mixed-language homepage for a Tunisian company.

**Fix:** in the production `.env`, set `APP_LOCALE=fr` and `APP_FALLBACK_LOCALE=fr`.

### 1.3 Timezone is hardcoded to UTC

`config/app.php:68` is `'timezone' => 'UTC'` — hardcoded, not env-driven. Tunisia is UTC+1, so a devis created at 00:30 local time is dated the **previous day** on the PDF.

**Fix:** change that line to `'timezone' => env('APP_TIMEZONE', 'UTC')` and set `APP_TIMEZONE=Africa/Tunis` in `.env`.

### 1.4 Switch to MySQL

SQLite is not suitable here because `.env` runs **sessions, cache and the queue** on the database. SQLite allows one writer at a time; a queue worker polling while a visitor writes a session row produces `SQLSTATE[HY000]: database is locked`.

There is nothing SQLite-specific in the code (the only raw SQL, in `StatsOverview.php:21-23`, is portable ANSI), so the switch is just configuration on a fresh database.

### 1.5 Change the default admin password

`database/seeders/DatabaseSeeder.php:20-23` creates `admin@promoaluplus.tn` / `admin123`. **Change it before the site is reachable from the internet** — see §7.1.

### 1.6 If you put Cloudflare in front

`bootstrap/app.php` configures no trusted proxies. Behind a proxy, Laravel sees the proxy's IP and may generate `http://` URLs. Add `$middleware->trustProxies(at: '*')` if you use Cloudflare or any CDN. Not needed for nginx talking directly to the internet.

---

## 2. Split the repository (recommended)

Keeping the parent monorepo works, but a repo containing only the Laravel app makes the deploy path obvious and avoids shipping `content_docs`, scratch scripts and audit markdown to the server.

**Keep the history** rather than starting fresh:

```bash
cd ~/Alu-workshop
git subtree split --prefix=alu-workshop-laravel -b laravel-only
```

Create an **empty private** repo on GitHub (no README, no .gitignore), then:

```bash
git remote add clean git@github.com:<you>/promoalu-plus.git
git push clean laravel-only:main
```

Do §1.1 (move `content_docs/json`) **before** the split, or immediately after in the new repo.

---

## 3. First contact with the server

SSH in as root using the key you registered at order time.

### 3.1 A non-root deploy user

```bash
adduser deploy
usermod -aG sudo deploy
rsync --archive --chown=deploy:deploy ~/.ssh /home/deploy
```

### 3.2 Harden SSH

Edit `/etc/ssh/sshd_config`:

```
PermitRootLogin no
PasswordAuthentication no
```

```bash
systemctl restart ssh
```

**Open a second terminal and confirm `ssh deploy@<ip>` works before closing the first one.**

### 3.3 Firewall

```bash
apt update && apt install -y ufw fail2ban
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw enable
```

### 3.4 Timezone

```bash
timedatectl set-timezone Africa/Tunis
```

---

## 4. Install the stack

### 4.1 PHP 8.3 + extensions

Debian 12 (via Sury):

```bash
apt install -y lsb-release ca-certificates curl gnupg
curl -sSLo /tmp/debsury.gpg https://packages.sury.org/php/apt.gpg
mv /tmp/debsury.gpg /etc/apt/trusted.gpg.d/
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list
apt update
```

Ubuntu 24.04 ships PHP 8.3 directly — skip the repo step.

```bash
apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring \
               php8.3-xml php8.3-curl php8.3-zip php8.3-gd \
               php8.3-intl php8.3-bcmath
```

**Every one of those extensions is actually used:**

| Extension | Needed by |
|---|---|
| `gd` | `DevisDocument::printLogoPath()` uses `imagecreatefrompng()` / `imagecreatetruecolor()`; also DomPDF and PhpSpreadsheet images |
| `zip` + `xml` | `phpoffice/phpspreadsheet` (the Excel devis export) |
| `mbstring` + `xml` | `barryvdh/laravel-dompdf` |
| `intl` | Filament, number/date formatting |
| `mysql` | the database |
| `bcmath` | monetary arithmetic on quote and invoice totals |

Tune `/etc/php/8.3/fpm/php.ini`:

```ini
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 12M
```

`upload_max_filesize` matters: hero slides accept up to **8 MB** (`HeroSlideResource`, `maxSize(8192)`) and service images 5 MB. The PHP default of 2 M would reject them with a confusing error.

```bash
systemctl restart php8.3-fpm
```

### 4.2 MySQL

```bash
apt install -y mysql-server        # Ubuntu
# apt install -y mariadb-server    # Debian
mysql_secure_installation
```

```sql
CREATE DATABASE promoalu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'promoalu'@'localhost' IDENTIFIED BY '<a-long-random-password>';
GRANT ALL PRIVILEGES ON promoalu.* TO 'promoalu'@'localhost';
FLUSH PRIVILEGES;
```

`utf8mb4` is not optional — the site stores Arabic content.

### 4.3 Composer, Node, nginx

```bash
apt install -y nginx git unzip
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
apt install -y nodejs
```

Node 22 matches the local toolchain (Vite 7 / Tailwind 4).

---

## 5. Deploy the application

### 5.1 Clone

```bash
mkdir -p /var/www && chown deploy:deploy /var/www
su - deploy
git clone git@github.com:<you>/promoalu-plus.git /var/www/promoalu
cd /var/www/promoalu
```

Use a **deploy key** (read-only) for a private repo: `ssh-keygen -t ed25519 -C "vps-deploy"`, then add the public key under the repo's Settings → Deploy keys.

### 5.2 Environment

```bash
cp .env.example .env
php artisan key:generate
nano .env
```

Production values:

```dotenv
APP_NAME="PromoAlu+"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://promoaluplus.tn

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_TIMEZONE=Africa/Tunis

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=promoalu
DB_USERNAME=promoalu
DB_PASSWORD=<the password from 4.2>

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_SCHEME=tls
MAIL_USERNAME=promoaluplus@gmail.com
MAIL_PASSWORD=<16-character Gmail App Password>
MAIL_FROM_ADDRESS="promoaluplus@gmail.com"
MAIL_FROM_NAME="PromoAlu+"
MAIL_ADMIN_ADDRESS=promoaluplus@gmail.com
```

> **`APP_DEBUG=false` is not optional.** With it on, any error page prints your database password to the visitor.

#### Email: what it does and does not block

`QuoteController:29` creates the quote **before** it attempts to send anything, and the mail calls are wrapped in a try/catch that logs and continues:

```php
$quote = Quote::create($validated);   // the lead is already saved

try {
    Mail::to($quote->email)->queue(new QuoteRequestReceived($quote));
    Mail::to(config('mail.admin_email'))->queue(new QuoteRequestNotification($quote));
} catch (\Exception $e) { \Log::error(...); }
```

So **you can go live with no mail configured at all** and lose nothing: every request lands in the database and appears in `/admin` → Devis. What you lose is the notification — nobody tells you a request arrived, and the customer gets no confirmation. You would be relying on remembering to check the panel. Worth fixing, but it is not a launch gate.

**You do not need a paid provider — you need an authenticated relay.** Sending straight from the VPS fails in practice: a new OVH IP has no sending reputation, no SPF and no DKIM, the admin notification goes to a Gmail address, and Gmail spam-folders or rejects unauthenticated mail from unknown IPs. You would also be running and securing Postfix for two emails a day, and some hosts throttle outbound port 25.

Ranked by effort:

| Option | Cost | Trade-off |
|---|---|---|
| **Gmail SMTP + App Password** (shown above) | free, ~500/day | Sender shows as `promoaluplus@gmail.com`, not your domain — Gmail rewrites `MAIL_FROM_ADDRESS` to the authenticated account. Needs 2FA enabled to generate the App Password |
| **OVH email hosting** | often bundled with the domain | Mail from `contact@promoaluplus.tn`, SPF/DKIM pre-wired |
| **Brevo free tier** | free, 300/day | Mail from your domain; requires adding DNS records |

Start with Gmail. Move to a domain sender when the domain exists and you care that customers see `@promoaluplus.tn`.

### 5.3 Install and build

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

`npm run build` is **mandatory**, not an optimisation: `layouts/app.blade.php` loads CSS through `@vite`, and without `public/build/manifest.json` every page returns a 500.

### 5.4 Database

```bash
php artisan migrate --force
php artisan db:seed --force
```

`--force` is required because `APP_ENV=production` makes both commands interactive otherwise.

Seeding populates services, FAQs, hero slides, project types, testimonials and site settings. It is safe to re-run: every seeder uses `firstOrCreate`, so **re-seeding never overwrites content the admin has edited**.

### 5.5 Permissions

```bash
sudo chown -R deploy:www-data /var/www/promoalu
sudo find /var/www/promoalu -type f -exec chmod 644 {} \;
sudo find /var/www/promoalu -type d -exec chmod 755 {} \;
sudo chmod -R ug+rwx storage bootstrap/cache public/uploads
```

`public/uploads` **must be writable by `www-data`** — every image an admin uploads through Filament lands there (`config/filesystems.php` defines the `uploads` disk rooted at `public_path('uploads')`).

### 5.6 Caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:assets
```

---

## 6. nginx and HTTPS

### 6.1 DNS first

At your registrar, point an **A record** for `promoaluplus.tn` and `www` at the VPS IPv4. Wait for propagation (`dig +short promoaluplus.tn`) before requesting a certificate — certbot fails otherwise.

### 6.2 Server block

`/etc/nginx/sites-available/promoalu`:

```nginx
server {
    listen 80;
    server_name promoaluplus.tn www.promoaluplus.tn;
    root /var/www/promoalu/public;

    index index.php;
    charset utf-8;

    client_max_body_size 12M;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* { deny all; }

    location ~* \.(jpg|jpeg|png|gif|webp|avif|svg|ico|css|js|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    error_page 404 /index.php;
}
```

**`root` must end in `/public`.** Pointing it at the project directory exposes `.env` to the internet.

`client_max_body_size 12M` must match `post_max_size` from §4.1, or large hero uploads fail at the nginx layer before PHP sees them.

```bash
ln -s /etc/nginx/sites-available/promoalu /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
```

### 6.3 Certificate

```bash
apt install -y certbot python3-certbot-nginx
certbot --nginx -d promoaluplus.tn -d www.promoaluplus.tn
```

Certbot rewrites the server block for TLS and installs a renewal timer. Confirm with `systemctl list-timers | grep certbot`.

---

## 7. The queue worker — without this, no emails are sent

Both mailables implement `ShouldQueue` and `QuoteController:33-34` dispatches with `->queue()`, on `QUEUE_CONNECTION=database`. **With no worker running, every quote request queues a job that is never processed.** The customer gets no confirmation and you get no notification, with no error anywhere.

`/etc/systemd/system/promoalu-worker.service`:

```ini
[Unit]
Description=PromoAlu queue worker
After=network.target mysql.service

[Service]
User=deploy
Group=www-data
Restart=always
RestartSec=5
WorkingDirectory=/var/www/promoalu
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

```bash
systemctl daemon-reload
systemctl enable --now promoalu-worker
systemctl status promoalu-worker
```

`--max-time=3600` recycles the process hourly so a long-lived worker never holds stale code or leaks memory.

### 7.1 Change the admin password

```bash
php artisan tinker
```

```php
$u = App\Models\User::where('email', 'admin@promoaluplus.tn')->first();
$u->update(['password' => bcrypt('<a long unique password>')]);
```

Do this **before** announcing the URL. `admin123` is in the seeder and therefore in your git history.

---

## 8. Verify it is actually live

Work through this in a browser, not just curl:

- [ ] `https://promoaluplus.tn` loads over HTTPS with a valid padlock
- [ ] The page is in **French** on first visit (not English — see §1.2)
- [ ] The homepage hero carousel shows images and rotates
- [ ] The language switcher works for FR / EN / AR, and Arabic renders right-to-left
- [ ] `/services` shows all 9 services with images
- [ ] The footer service list is populated
- [ ] `/contact` → submit a real quote request → it appears under `/admin` → Devis
- [ ] *(only once mail is configured)* the same request delivers **both** emails — notification to you, confirmation to the customer. This is the queue-worker test; if the quote appears in the admin but no mail arrives, the worker is not running (§7). Skip this line if you launched without SMTP
- [ ] `/admin` → log in → change a service title → confirm it changes on the public page
- [ ] `/admin` → Contenu → Slides d'accueil → **upload an image** → confirm it appears on the homepage (this is the `public/uploads` permission test)
- [ ] `/admin` → Devis → open one → download the PDF and the Excel export (this is the `gd` / `zip` test)
- [ ] `https://promoaluplus.tn/.env` returns 404, not a file

---

## 9. Shipping changes after launch

You do **not** need a CI/CD pipeline. Save this as `/var/www/promoalu/deploy.sh`:

```bash
#!/usr/bin/env bash
set -euo pipefail
cd /var/www/promoalu

php artisan down --render="errors::503"
trap 'php artisan up' EXIT

git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan optimize          # clears + rebuilds config/route/view cache
php artisan filament:assets
sudo systemctl restart promoalu-worker
```

```bash
chmod +x deploy.sh
```

Then a release is `ssh deploy@<ip> '/var/www/promoalu/deploy.sh'`.

`php artisan optimize` is required after every deploy — with `config:cache` in place, a changed `.env` has **no effect** until the cache is rebuilt. This is the single most common "why didn't my change apply" on a Laravel VPS.

**Add GitHub Actions later, if at all.** A pipeline would run these exact commands over SSH. Build it when running the script by hand becomes annoying, not before. The one real argument for it: if you stay on a 2 GB VPS, move `npm run build` into Actions and rsync `public/build/` up, so the server never runs Vite.

---

## 10. Backups

Two things are irreplaceable, and neither is in git:

1. **The MySQL database** — quotes, invoices, all admin-edited content
2. **`public/uploads/`** — every image uploaded through the admin (gitignored by design)

`/home/deploy/backup.sh`:

```bash
#!/usr/bin/env bash
set -euo pipefail
STAMP=$(date +%F)
DEST=/home/deploy/backups
mkdir -p "$DEST"

mysqldump --single-transaction promoalu | gzip > "$DEST/db-$STAMP.sql.gz"
tar czf "$DEST/uploads-$STAMP.tar.gz" -C /var/www/promoalu/public uploads

find "$DEST" -type f -mtime +7 -delete
```

> **Why 7 days and not 30:** the VPS-1 disk is 40 GB. At today's 8.7 MB of uploads, 30 daily tarballs is nothing — but if the image library grows to a couple of GB, 30 copies would fill the disk. Seven days on-box is plenty *provided* you sync the backups off the VPS, which is the next paragraph and not optional.

```bash
chmod +x /home/deploy/backup.sh
crontab -e
# 0 3 * * * /home/deploy/backup.sh
```

**Copy these off the VPS.** A backup that only exists on the machine it protects is not a backup — sync to OVH Object Storage, S3, or `rsync` to another host.

> **Before the very first `migrate --force` on a database with real data**, take a manual dump. Two migrations are one-way: `convert_quotes_project_type_to_project_types` drops a column after backfilling it, and `consolidate_about_history_settings` deletes rows with a deliberately empty `down()`.

---

## 11. When something is wrong

| Symptom | Cause |
|---|---|
| **Every page 500s** | `npm run build` was never run — no `public/build/manifest.json`. Or `storage/` is not writable |
| **A change to `.env` does nothing** | `config:cache` is stale. Run `php artisan optimize` |
| **Quote emails never arrive** | The queue worker is not running (`systemctl status promoalu-worker`), or `MAIL_MAILER` is still `log` |
| **Uploaded images 404** | `public/uploads` is not writable by `www-data`, or nginx `root` is wrong |
| **Upload rejected around 2 MB** | `upload_max_filesize` / `post_max_size` in php.ini, or nginx `client_max_body_size` |
| **Arabic renders as `????`** | The database is not `utf8mb4` |
| **`database is locked`** | You are still on SQLite — see §1.4 |
| **500 only on the devis PDF/Excel** | `php8.3-gd` or `php8.3-zip` is missing |

Logs, in the order worth checking:

```bash
tail -f /var/www/promoalu/storage/logs/laravel.log
journalctl -u promoalu-worker -f
tail -f /var/log/nginx/error.log
```

---

## Summary of what must change in the project

| # | Change | Status |
|---|---|---|
| 1 | Move `content_docs/json/*` into the repo, fix 4 seeder paths | **not done** |
| 2 | `APP_LOCALE=fr` / `APP_FALLBACK_LOCALE=fr` | env only |
| 3 | `config/app.php:68` → `env('APP_TIMEZONE', 'UTC')`, set `Africa/Tunis` | **not done** |
| 4 | MySQL block in `.env` / `.env.example` | **not done** |
| 5 | Change the seeded admin password | post-deploy (§7.1) |
| 6 | `trustProxies` if you add Cloudflare | only if needed |
