# Deploying Promo Alu Plus on an OVHcloud VPS

Target: a live, HTTPS-served public site at `https://promoaluplus.com` (or your domain) with a working `/pap` panel, quote emails actually sending, and a one-command way to ship changes afterwards.

Written against the state of `main` at the time of writing: Laravel 12.60+, Filament 3.3.54, Vite/Tailwind 4.

> **PHP 8.4 is required, not 8.3.** The locked dependencies (`symfony/string`,
> `symfony/translation`, `symfony/clock`) declare `php >=8.4`, so
> `composer install` fails outright on 8.3. `composer.json` pins
> `config.platform.php` to 8.4.0 so this cannot drift silently again.

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

### 1.1 Blocker: four seeders read files outside the repo — ✅ fixed

Kept here because it is the defect that would have made the deploy repo seed an
empty site in silence. As originally found, four seeders read this:

```
database/seeders/ServiceSeeder.php:15       base_path('../content_docs/json/services.json')
database/seeders/FaqSeeder.php:16           base_path('../content_docs/json/questions_frequentes.json')
database/seeders/SiteSettingsSeeder.php:20  base_path('../content_docs/json/notre_histoire.json')
database/seeders/SiteSettingsSeeder.php:21  base_path('../content_docs/json/service_tunisiens_etranger.json')
```

`readJson()` returns `[]` when a file is missing, so **seeding will succeed while doing nothing** — you get an empty site with no error.

**Fixed:** the JSON now lives at `database/seeders/content/` inside the repo and the four
paths use `database_path()`. `SiteCoherenceTest` asserts every seeder source is in-repo and
actually read, so this cannot silently regress.

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

`database/seeders/DatabaseSeeder.php:20-23` creates `admin@promoaluplus.com` / `admin123`. **Change it before the site is reachable from the internet** — see §7.1.

### 1.6 The admin panel is at `/pap`, not `/admin` — ✅ done

`AdminPanelProvider` sets `->path('pap')`, so a visitor idly trying `/admin` gets a 404
instead of a login form. The panel *ID* deliberately stays `admin`: Filament builds route
names from the ID, so they remain `filament.admin.*` and the three places that resolve
them by name keep working.

> **This is tidiness, not a security control.** Anyone who wants the panel will find it —
> the login page is still reachable by anyone who types the right path. If you later want
> a real barrier, do it in nginx rather than in Laravel:
>
> ```nginx
> location ^~ /pap/ {
>     allow <your.home.ip>;
>     allow <your.office.ip>;
>     deny all;
>     try_files $uri $uri/ /index.php?$query_string;
> }
> ```
>
> That stops the request before PHP ever runs. The cost is that the path is then
> hardcoded in a second place — rename it in one and you lock yourself out.

`robots.txt` deliberately does **not** list the path. Adding `Disallow: /pap` would
publish it to precisely the people you would rather not hand it to, and nothing on the
public site links to the panel, so no crawler finds it anyway.

### 1.7 If you put Cloudflare in front

`bootstrap/app.php` configures no trusted proxies. Behind a proxy, Laravel sees the proxy's IP and may generate `http://` URLs. Add `$middleware->trustProxies(at: '*')` if you use Cloudflare or any CDN. Not needed for nginx talking directly to the internet.

---

## 2. Split the repository — ✅ done

The deploy repo is **`https://github.com/bendhiab-younes/promoaluplus`** (private). It holds
the Laravel app at its root — no `alu-workshop-laravel/` prefix, so every path in this
guide is literal on the server.

The split kept the full history (125 of the monorepo's 136 commits touched the app) and
was verified file-by-file against the monorepo subdirectory: 390 files, identical lists.
Root-level scratch (`content_docs/`, `firecrawl_scrape_result.json`, `patch_seeder.py`,
`.idea/`) is not in it, which is the point — none of it reaches the server.

For the record, what was run:

```bash
cd ~/Alu-workshop
git subtree split --prefix=alu-workshop-laravel -b laravel-only
git push https://github.com/bendhiab-younes/promoaluplus.git laravel-only:main
```

> **The split rewrote commit SHAs.** The two repos share no common ancestor and can never
> be merged or rebased into each other. Pick one as the working copy — see §13.1 — and
> keep the monorepo untouched until the first VPS deploy is verified, since it is the
> only rollback.

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

### 4.1 PHP 8.4 + extensions

Debian 12 (via Sury):

```bash
apt install -y lsb-release ca-certificates curl gnupg
curl -sSLo /tmp/debsury.gpg https://packages.sury.org/php/apt.gpg
mv /tmp/debsury.gpg /etc/apt/trusted.gpg.d/
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list
apt update
```

Ubuntu 24.04 ships 8.3, not 8.4, so the Sury/ondrej repo is required there too.

```bash
apt install -y php8.4-fpm php8.4-cli php8.4-mysql php8.4-mbstring \
               php8.4-xml php8.4-curl php8.4-zip php8.4-gd \
               php8.4-intl php8.4-bcmath
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

Tune `/etc/php/8.4/fpm/php.ini`:

```ini
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 12M
```

`upload_max_filesize` matters: hero slides accept up to **8 MB** (`HeroSlideResource`, `maxSize(8192)`) and service images 5 MB. The PHP default of 2 M would reject them with a confusing error.

```bash
systemctl restart php8.4-fpm
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

Generate the deploy key **first** — the repo is private, so the clone fails without it:

```bash
su - deploy
ssh-keygen -t ed25519 -C "vps-deploy" -f ~/.ssh/id_ed25519 -N ""
cat ~/.ssh/id_ed25519.pub
```

Paste that public key at
`https://github.com/bendhiab-younes/promoaluplus/settings/keys` → **Add deploy key**.
Leave *Allow write access* **unchecked** — the server only ever pulls, and a read-only key
cannot be used to push malicious code back if the VPS is compromised.

Then clone (SSH, not HTTPS — the deploy key only authenticates over SSH):

```bash
exit                                    # back to root
mkdir -p /var/www && chown deploy:deploy /var/www
su - deploy
git clone git@github.com:bendhiab-younes/promoaluplus.git /var/www/promoalu
cd /var/www/promoalu
```

First connection asks to trust `github.com` — answer `yes`.

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
APP_URL=https://promoaluplus.com

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

So **you can go live with no mail configured at all** and lose nothing: every request lands in the database and appears in `/pap` → Devis. What you lose is the notification — nobody tells you a request arrived, and the customer gets no confirmation. You would be relying on remembering to check the panel. Worth fixing, but it is not a launch gate.

**You do not need a paid provider — you need an authenticated relay.** Sending straight from the VPS fails in practice: a new OVH IP has no sending reputation, no SPF and no DKIM, the admin notification goes to a Gmail address, and Gmail spam-folders or rejects unauthenticated mail from unknown IPs. You would also be running and securing Postfix for two emails a day, and some hosts throttle outbound port 25.

Ranked by effort:

| Option | Cost | Trade-off |
|---|---|---|
| **Gmail SMTP + App Password** (shown above) | free, ~500/day | Sender shows as `promoaluplus@gmail.com`, not your domain — Gmail rewrites `MAIL_FROM_ADDRESS` to the authenticated account. Needs 2FA enabled to generate the App Password |
| **OVH email hosting** | often bundled with the domain | Mail from `contact@promoaluplus.com`, SPF/DKIM pre-wired |
| **Brevo free tier** | free, 300/day | Mail from your domain; requires adding DNS records |

Start with Gmail. Move to a domain sender when the domain exists and you care that customers see `@promoaluplus.com`.

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

> **Domain: `.com`, decided.** `.tn` goes through ATI-accredited registrars and
> generally wants company documents (registre de commerce / patente), which can
> take days. `.com` registers in minutes and unblocks HTTPS immediately. You can
> add the `.tn` later and point it at the same VPS — nothing in this guide has to
> change except adding it to the `certbot -d` list and to `server_name`.


At your registrar, point an **A record** for `promoaluplus.com` and `www` at the VPS IPv4. Wait for propagation (`dig +short promoaluplus.com`) before requesting a certificate — certbot fails otherwise.

### 6.2 Server block

`/etc/nginx/sites-available/promoalu`:

```nginx
server {
    listen 80;
    server_name promoaluplus.com www.promoaluplus.com;
    root /var/www/promoalu/public;

    index index.php;
    charset utf-8;

    client_max_body_size 12M;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Uploaded files are never executed. `^~` makes this prefix location win
    # over the `\.php$` regex below, so nothing an admin uploads can be run
    # even if it somehow lands with a .php name. Defence in depth: the
    # application also derives the stored extension from file content.
    location ^~ /uploads/ {
        location ~ \.php$ { return 403; }
        expires 1y;
        add_header Cache-Control "public";
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
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
certbot --nginx -d promoaluplus.com -d www.promoaluplus.com
```

Certbot rewrites the server block for TLS and installs a renewal timer. Confirm with `systemctl list-timers | grep certbot`.

---

## 7. The queue worker — without this, no emails are sent

Both mailables implement `ShouldQueue` and `QuoteController:33-34` dispatches with `->queue()`, on `QUEUE_CONNECTION=database`. **With no worker running, every quote request queues a job that is never processed.** The customer gets no confirmation and you get no notification, with no error anywhere.

> **Install the worker even though you have deferred SMTP.** Without mail configured the
> jobs still get created and still need draining — otherwise the `jobs` table grows
> forever. Set `MAIL_MAILER=log` in `.env` until you wire up a real sender: the worker
> then "delivers" to `storage/logs/laravel.log` and the queue stays clean. Nothing is
> lost either way — **the quote itself is saved to the database before the mail is ever
> queued**, so it appears in `/pap` regardless of whether email works.

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
ExecStart=/usr/bin/php8.4 artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

```bash
systemctl daemon-reload
systemctl enable --now promoalu-worker
systemctl status promoalu-worker
```

`--max-time=3600` recycles the process hourly so a long-lived worker never holds stale code or leaks memory.

**Let `deploy` restart the worker without a password.** `deploy.sh` (§9) ends with
`sudo systemctl restart promoalu-worker`, and a normal `sudo` would sit there asking for a
password — which nobody can type, because the script runs over a non-interactive
`ssh deploy@<ip> '...'`. The deploy hangs, then times out with the site still in
maintenance mode. As root:

```bash
echo 'deploy ALL=(root) NOPASSWD: /bin/systemctl restart promoalu-worker' \
    > /etc/sudoers.d/promoalu-deploy
chmod 440 /etc/sudoers.d/promoalu-deploy
visudo -c                                    # must print "parsed OK"
```

Scoped to that one command — `deploy` gains no other root powers.

### 7.1 Change the admin password

```bash
php artisan tinker
```

```php
$u = App\Models\User::where('email', 'admin@promoaluplus.com')->first();
$u->update(['password' => bcrypt('<a long unique password>')]);
```

Do this **before** announcing the URL. `admin123` is in the seeder and therefore in your git history.

---

## 8. Verify it is actually live

Work through this in a browser, not just curl:

- [ ] `https://promoaluplus.com` loads over HTTPS with a valid padlock
- [ ] The page is in **French** on first visit (not English — see §1.2)
- [ ] The homepage hero carousel shows images and rotates
- [ ] The language switcher works for FR / EN / AR, and Arabic renders right-to-left
- [ ] `/services` shows all 9 services with images
- [ ] The footer service list is populated
- [ ] `/contact` → submit a real quote request → it appears under `/pap` → Devis
- [ ] *(only once mail is configured)* the same request delivers **both** emails — notification to you, confirmation to the customer. This is the queue-worker test; if the quote appears in the admin but no mail arrives, the worker is not running (§7). Skip this line if you launched without SMTP
- [ ] `/pap` → log in → change a service title → confirm it changes on the public page
- [ ] `/pap` → Contenu → Slides d'accueil → **upload an image** → confirm it appears on the homepage (this is the `public/uploads` permission test)
- [ ] `/pap` → Devis → open one → download the PDF and the Excel export (this is the `gd` / `zip` test)
- [ ] `https://promoaluplus.com/.env` returns 404, not a file

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
| **500 only on the devis PDF/Excel** | `php8.4-gd` or `php8.4-zip` is missing |

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
| 1 | Move `content_docs/json/*` into the repo, fix 4 seeder paths | ✅ **done** — now `database/seeders/content/`; verified by a fresh `migrate:fresh --seed` |
| 2 | `APP_LOCALE=fr` / `APP_FALLBACK_LOCALE=fr` | ✅ **done** — defaults in `.env.example`; still set them in the production `.env` |
| 3 | `config/app.php` → `env('APP_TIMEZONE', 'UTC')`, set `Africa/Tunis` | ✅ **done** |
| 4 | MySQL block in `.env` / `.env.example` | ⚠️ config **done** (commented block + warning in `.env.example`); **the suite has not been run against a real MySQL server** — see note below |
| 5 | Change the seeded admin password | post-deploy (§7.1) |
| 6 | `trustProxies` if you add Cloudflare | only if needed |
| 7 | Security audit fixes (upload RCE, stored XSS, JSON-LD breakout) | ✅ **done** — see §12 |
| 8 | Reproducible builds: PHP/Node pinned, dependency advisories cleared | ✅ **done** — see §13 |
| 9 | Split into a deploy repo | ✅ **done** — `bendhiab-younes/promoaluplus` (private), 125 commits, 390 files verified identical; see §2 |
| 10 | Move the panel off `/admin` to `/pap` | ✅ **done** — see §1.6; `/admin` now 404s, asserted by test |

> **On row 4:** nothing in the codebase is SQLite-specific — the only raw SQL
> (`StatsOverview.php:21-23`) is portable ANSI, `->change()` works natively on
> MySQL in Laravel 11+, and JSON columns are supported on both. That is a
> reading of the code, not a test result. The 199-test suite still runs on
> SQLite. Before launch, run it once against a real MySQL database — most
> cheaply on the VPS itself after §4.2, with
> `DB_CONNECTION=mysql php artisan test`.

---

## 12. Security fixes applied before launch

A pre-deployment audit of the whole application found four issues. All are
fixed and covered by `tests/Feature/SecurityHardeningTest.php`.

**1. Remote code execution through an admin image upload (HIGH).** Filament
stored uploads under the client's own extension while `->image()` validated
only the sniffed MIME type, so a valid GIF carrying a PHP payload and named
`payload.php` was written into `public/uploads` as `<ulid>.php` — executable by
any web server that runs PHP by extension. The stored extension is now derived
from the file's actual content (`AppServiceProvider::hardenFileUploads()`),
applied centrally so a future upload field cannot miss it, and the nginx block
in §6.2 refuses to execute anything under `/uploads/`.

**2. Stored XSS via SVG upload (MEDIUM).** `image/*` accepts `image/svg+xml`,
and SVG is active content served same-origin. Uploads are now restricted to
JPEG, PNG, WebP, AVIF and GIF.

**3. Stored XSS via unsanitised admin HTML (MEDIUM).** A service's `svg_icon`
and rich-text description were rendered through `{!! !!}` untouched, so anyone
reaching the admin panel could store script that ran for every visitor. Both go
through `App\Support\SafeHtml` now. SVG is scrubbed as XML rather than HTML so
that `viewBox` keeps its capitalisation — sanitising SVG as HTML lower-cases it
and every icon silently loses its scaling.

**4. JSON-LD `</script>` breakout (MEDIUM).** The structured-data blocks used
`JSON_UNESCAPED_SLASHES`, so a `</script>` sequence in any admin-editable value
closed the block early. `JSON_HEX_TAG` is now set on all three. The FAQ schema
was already safe via `strip_tags()`; the `LocalBusiness` schema in the shared
layout — present on every page — was not.

Also verified and found already correct: no secrets in git history, signed-URL
protection on `GET /storage/{path}`, auth on the Filament export routes and the
PDF/Excel routes, parameterised SQL, and mass assignment bounded by the
validation allowlist.

> `User::canAccessPanel()` returns `true` for every authenticated user. That is
> safe **only** because the panel offers no registration and nothing outside the
> seeders creates a `User`. If you ever enable self-registration, this becomes a
> privilege-escalation bug — gate it first.

---

## 13. Keeping deploys reproducible

**You never push to the VPS.** You push to GitHub; the VPS pulls. Nothing beyond
standard SSH access is needed — there is no "git access" option to buy from OVH.

```
your machine  --git push-->  GitHub  <--git pull--  VPS (deploy.sh)
```

Set up once: generate a key on the VPS (`ssh-keygen -t ed25519`) and add the
public half to the repo's **Settings → Deploy keys** as read-only. That is what
lets a private repo be pulled without a password. Full steps in §5.1.

### 13.1 Which repo do you work in from now on?

The split (§2) rewrote every commit SHA, so `Alu-workshop` and `promoaluplus`
have no shared ancestor — git will refuse to merge or rebase between them. That
makes this a decision, not a detail:

**Recommended — make `promoaluplus` the working copy.** Clone it fresh and work
there. `git push` then behaves normally forever, and the local layout matches
the VPS exactly. One-time cost: copy over the untracked things git does not
carry — `.env`, `database/database.sqlite`, `public/uploads/`, then run
`composer install && npm ci && npm run build`.

**Alternative — keep working in the monorepo.** Then every release needs an
extra step to publish the app:

```bash
git subtree push --prefix=alu-workshop-laravel \
    https://github.com/bendhiab-younes/promoaluplus.git main
```

That re-derives the split each time (slow — it walks every commit — but correct,
and it fast-forwards the deploy repo cleanly; this is how the §2 doc update was
published). Use `subtree push` directly; you do not need a `laravel-only`
branch, and keeping a stale one around only invites pushing it by mistake.

Either way, **keep the monorepo until the first deploy is verified end to end.**
It is the only rollback.

### What makes the build come out the same every time

- **Lockfiles are committed** — `composer.lock` and `package-lock.json`. The
  deploy uses `composer install` and `npm ci`, both of which install exactly the
  locked versions. Never `composer update` on the server.
- **PHP is pinned** — `composer.json` sets `config.platform.php` to `8.4.0`, so
  dependency resolution targets the server's PHP no matter which version you
  happen to run locally. This was not theoretical: the lock already contained
  Symfony 8 components requiring PHP 8.4 while this guide still said 8.3, which
  would have failed `composer install` on the first deploy.
- **Node is pinned** — `.nvmrc` (22) and `engines.node` in `package.json`. Run
  `nvm use` on the VPS before `npm ci` if you have nvm installed.

### The rule that actually bites

`php artisan optimize` caches config, routes and views. **With that cache in
place, editing `.env` has no effect until the cache is rebuilt.** `deploy.sh`
already runs it; if you change `.env` by hand afterwards, run it again.

### Dependency advisories

`composer audit` was run before launch: 52 advisories across 19 packages,
including 10 rated high, three of which touched this application directly —
CRLF injection in Laravel's `email` validation rule (used by the public quote
form), SMTP command injection in `symfony/mime` (mail is sent to a
user-supplied address), and an XSS in Filament's RichEditor.

After updating, **5 remain and none reach production**: `phpunit` and
`symfony/yaml` are dev-only and excluded by `composer install --no-dev`, and
`psy/psysh` arrives via `laravel/tinker`. Re-run `composer audit` periodically;
it is the cheapest security check available.
