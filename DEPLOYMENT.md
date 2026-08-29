# Deploying Promo Alu Plus on an OVHcloud VPS

Target: a live, HTTPS-served public site at `https://promoaluplus.com` with a working
`/pap` admin panel and a one-command way to ship changes afterwards.

**Status as of 2026-08-29.** The application side is finished — every change this guide
once asked for has been made, tested and pushed. The server has been bought and the domain
registered. What remains is provisioning the box, and that work has been handed to an
external engineer who has access to the OVH account and the deploy repository. Sections 3
through 8 are addressed to whoever does that work; sections 9 through 13 are for whoever
maintains the site afterwards.

Written against `main`: Laravel 12.66, Filament 3.3.54, Livewire 3.8.4, Vite/Tailwind 4.
The suite is **213 tests**, green on PHP 8.4.

> **PHP 8.4 exactly — not 8.3, not 8.5.** Below 8.4, `composer install` fails outright:
> `symfony/string`, `symfony/translation` and `symfony/clock` all declare `php >=8.4`.
> Above it, you are on a PHP this application has never been tested against — and Ubuntu
> 26.04 makes that the *default*, so it is the likelier mistake here. `composer.json`
> pins `config.platform.php` to 8.4.0. See §4.1.

---

## 0. The server

| | |
|---|---|
| Host | `vps-28cf29dc.vps.ovh.net` |
| IPv4 | **`152.228.138.65`** |
| IPv6 | `2001:41d0:404:200::8029` |
| OS | **Ubuntu 26.04 LTS** ("Resolute Raccoon") |
| Region | Strasbourg (SBG), `os-sbg6` |
| Model | OVH VPS-1 2027 — 2 vCores / 4 GB / 40 GB NVMe |
| Domain | `promoaluplus.com` |
| Backup | OVH "Automated backup — Standard" |
| Renews | 25 August 2027, manual renewal |

### Does it fit?

Comfortably. The two specs that decide this are RAM and cores: `npm run build` peaks around
1–1.5 GB and would be OOM-killed on a busy 2 GB box, and PHP-FPM, MySQL, nginx and the queue
worker share the two cores without contention at brochure-site traffic.

Disk, measured against the actual project:

| Item | Size |
|---|---|
| OS + PHP + MySQL + nginx + Node | ~6–8 GB |
| `vendor/` (production, `--no-dev`) | ~110 MB |
| `node_modules/` | 51 MB |
| App code + `public/images` | ~25 MB |
| `public/uploads` (all admin uploads today) | 8.7 MB |
| Database | 352 KB |

≈ **9 GB used, 31 GB free.** Uploads would have to grow roughly 100× before storage became
the constraint. The only practical consequence is how many `mysqldump` archives you keep —
see §10.

> **The included "Sauvegarde automatisée" is not a backup strategy.** It snapshots the whole
> VM from yesterday. That is good for "I broke the server" and useless for "a client deleted
> an invoice last Tuesday." It does not replace the `mysqldump` cron in §10.

> **Ubuntu 26.04 changes exactly one step.** It ships **PHP 8.5** as its default. §4.1
> installs 8.4 explicitly from the Sury repository — do not `apt install php` and take what
> you get.

---

## Status board

| # | Change | Status |
|---|---|---|
| 1 | Move `content_docs/json/*` into the repo, fix 4 seeder paths | ✅ **done** — now `database/seeders/content/`; verified by a fresh `migrate:fresh --seed` |
| 2 | `APP_LOCALE=fr` / `APP_FALLBACK_LOCALE=fr` | ✅ **done** — defaults in `.env.example`; still set them in the production `.env` |
| 3 | `config/app.php` → `env('APP_TIMEZONE', 'UTC')`, set `Africa/Tunis` | ✅ **done** |
| 4 | MySQL block in `.env` / `.env.example` | ⚠️ config **done** (commented block + warning in `.env.example`); **the suite has not been run against a real MySQL server** — see note below |
| 5 | Change the seeded admin password | ⏳ **owed** — post-deploy, §7.1 |
| 6 | `trustProxies` if Cloudflare goes in front | conditional — only if it does |
| 7 | Security audit fixes (upload RCE, stored XSS, JSON-LD breakout) | ✅ **done** — see §12 |
| 8 | Reproducible builds: PHP/Node pinned, dependency advisories cleared | ✅ **done** — see §13 |
| 9 | Split into a deploy repo | ✅ **done** — `bendhiab-younes/promoaluplus` (private), 125 commits, 390 files verified identical; see §2 |
| 10 | Move the panel off `/admin` to `/pap` | ✅ **done** — `/admin` now 404s, asserted by test |
| 11 | Stop tracking Filament's generated panel assets | ✅ **done** — composer regenerates them on every install; the committed copies were 8 months stale |
| 12 | Buy the VPS and register the domain | ✅ **done** — see §0 |
| 13 | Provision the server (§3–§8) | ⏳ **in progress** — handed to an external engineer |

> **On row 4:** nothing in the codebase is SQLite-specific — the only raw SQL
> (`StatsOverview.php:21-23`) is portable ANSI, `->change()` works natively on
> MySQL in Laravel 11+, and JSON columns are supported on both. That is a
> reading of the code, not a test result. The 213-test suite still runs on
> SQLite. Before launch, run it once against a real MySQL database — most
> cheaply on the VPS itself after §4.2, with
> `DB_CONNECTION=mysql php artisan test`.

---

## 1. The application side — done

Where the board says "done", here is what was actually wrong. It is worth reading once:
most of these were **silent failures** — the kind that deploy cleanly and then misbehave
with no error anywhere — and recognising the shape of one is what stops it coming back.

| # | Was | Now |
|---|---|---|
| 1.1 | Four seeders read JSON from **outside** the repo. `readJson()` returns `[]` on a missing file, so a split-off repo would seed **successfully and empty** — a site with zero services and no error anywhere. | Sources live at `database/seeders/content/` and are read with `database_path()`. `SiteCoherenceTest` asserts every seeder source is in-repo and actually read, so it cannot regress. |
| 1.2 | `APP_LOCALE=en` meant a first-time visitor got **English UI chrome over French content** — `SetLocale` only overrides once someone picks a language. | `.env.example` ships `APP_LOCALE=fr` / `APP_FALLBACK_LOCALE=fr`. The production `.env` must set them too — §5.2. |
| 1.3 | `config/app.php` hardcoded `'timezone' => 'UTC'`. Tunisia is UTC+1, so a devis created at 00:30 was **dated the previous day** on its PDF. | Reads `env('APP_TIMEZONE', 'UTC')`; `.env.example` sets `Africa/Tunis`. |
| 1.4 | SQLite in production would deadlock: sessions, cache **and** the queue all run on the database, and SQLite allows one writer — a worker polling while a visitor writes a session gives `SQLSTATE[HY000]: database is locked`. | MySQL block and a warning in `.env.example`. Nothing in the code is SQLite-specific; the only raw SQL (`StatsOverview.php:21-23`) is portable ANSI. |
| 1.6 | The admin panel answered at `/admin`, so a visitor guessing at the address bar met a login form. | Panel is at **`/pap`**; `/admin` 404s. See below. |
| 12 | Four security holes — upload RCE, stored XSS twice, JSON-LD breakout. | Fixed and regression-tested. §12. |

### Still depends on the deploy

- **The production `.env`** — §5.2 lists every value that must be set.
- **The seeded admin password.** `database/seeders/DatabaseSeeder.php` creates
  `admin@promoaluplus.com` / `admin123`. That is in git history, so it must be changed
  before the URL is announced — §7.1.
- **`trustProxies`, only if Cloudflare or a CDN goes in front.** `bootstrap/app.php`
  configures no trusted proxies; behind one, Laravel sees the proxy's IP and can generate
  `http://` URLs. Add `$middleware->trustProxies(at: '*')` in that case. Not needed for
  nginx facing the internet directly.

### On the panel living at `/pap`

`AdminPanelProvider` sets `->path('pap')`. The panel *ID* deliberately stays `admin`,
because Filament builds route names from the ID — they remain `filament.admin.*`, and
three places resolve them by name. Renaming the ID to match the path is the obvious-looking
tidy-up that breaks all three silently.

> **This is tidiness, not a security control.** The login page is still reachable by anyone
> who types the right path. If a real barrier is wanted, it belongs in nginx, not Laravel:
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
> That stops the request before PHP runs. The cost is that the path is then hardcoded in a
> second place — rename it in one and you lock yourself out.

`robots.txt` deliberately does **not** list the path. `Disallow: /pap` would publish it to
exactly the people you would rather not hand it to, and nothing on the public site links to
the panel, so no crawler finds it anyway.

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
> be merged or rebased into each other. `promoaluplus` is now the working copy — §13.1 —
> and the monorepo is kept untouched until the first VPS deploy is verified, since it is
> the only rollback.

---

## 3. First contact with the server

```bash
ssh ubuntu@152.228.138.65
```

OVH's Ubuntu images create a sudo-capable **`ubuntu`** user and disable direct root login,
so try that first; if your order was configured for root access, `ssh root@152.228.138.65`
works instead. From the `ubuntu` account, become root with `sudo -i` for the rest of §3
and §4.

If the key you registered at order time is not your default (`~/.ssh/id_ed25519`), point at
it explicitly: `ssh -i ~/.ssh/<key> ubuntu@152.228.138.65`.

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

### 4.0 What the server needs — and what it does not

| Component | | Notes |
|---|---|---|
| **PHP 8.4 + FPM** | required | 8.4 exactly. See §4.1 — this is the one item most likely to go wrong on Ubuntu 26.04. |
| **MySQL 8.0+** | required | Not Postgres. §4.2. |
| **nginx** | required | Use the hardened server block in §6.2, not a stock one — it carries the rule that stops anything under `/uploads/` executing. |
| **Node 22 + npm** | required | Build-time only; not needed at runtime. But genuinely required: without `npm run build` every page returns 500. |
| **Composer 2** | required | `composer install --no-dev`. Never `composer update` on the server. |
| **certbot** | required | Let's Encrypt via the nginx plugin. §6.3. |
| **Redis** | **not needed** | Cache, sessions and the queue all run on the `database` driver. Nothing in the codebase touches Redis, and neither `predis/predis` nor `ext-redis` is a dependency. The `REDIS_*` keys in `.env.example` are Laravel's stock defaults and are never read. |
| **Memcached · SQS · S3** | **not needed** | Also stock config keys that are never read. Uploads go to local disk, not object storage. |
| **Supervisor** | optional | The queue worker is specified as a systemd unit in §7. Supervisor is fine instead — the requirement is that a worker runs, not which supervisor runs it. |

### 4.1 PHP 8.4 + extensions

**Ubuntu 26.04 ships PHP 8.5 as its default `php` package. Do not use it.** The lockfile
was resolved against `config.platform.php = 8.4.0` and the whole 213-test suite has only
ever run on 8.4. Laravel 12's supported matrix is PHP 8.2–8.4; 8.5 is newer than the
release this app was built and tested against. A first deploy is the wrong moment to also
change PHP minor version — install 8.4 explicitly and revisit 8.5 later, deliberately,
with the suite as the check.

PHP 8.4 is not in Ubuntu 26.04's own repositories, so add Ondřej Surý's (the same
repository Debian 12 and Ubuntu 24.04 need, for the opposite reason — they ship 8.3):

```bash
apt install -y lsb-release ca-certificates curl gnupg
curl -sSLo /tmp/php.gpg https://packages.sury.org/php/apt.gpg
mv /tmp/php.gpg /etc/apt/trusted.gpg.d/
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list
apt update
```

Confirm 8.4 is actually offered before installing — if Sury has not yet published for
`resolute`, this prints nothing and the next command would silently pull 8.5:

```bash
apt-cache policy php8.4-fpm
```

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

`ctype`, `fileinfo`, `filter`, `hash`, `iconv`, `json`, `openssl`, `pcre`, `session`,
`tokenizer` and `zlib` are required too — the production dependency graph declares all of
them — but they ship inside `php8.4-cli` / `php8.4-common`, so they need no separate
package and are easy to mistake for missing when auditing the list above.

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

MAIL_MAILER=log
MAIL_ADMIN_ADDRESS=promoaluplus@gmail.com
```

> **`APP_DEBUG=false` is not optional.** With it on, any error page prints your database password to the visitor.

> **`MAIL_MAILER=log` is the intended launch state**, not an oversight — SMTP has been
> deliberately deferred and no credentials exist yet. Mail "delivers" to
> `storage/logs/laravel.log`, which keeps the queue draining. Read the next subsection
> before deciding this is a problem; it is not a launch gate. When a real sender is wanted,
> the SMTP block is at the end of that subsection.

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

Start with Gmail; move to a domain sender when you care that customers see
`@promoaluplus.com`. The block to paste in when that day comes:

```dotenv
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

Then `php artisan optimize` — with the config cached, editing `.env` alone changes nothing.

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

`promoaluplus.com` is **registered**. A `.tn` was considered and deferred: it goes through
ATI-accredited registrars and generally wants company documents, which takes days. One can
be added later and pointed at the same VPS — nothing here changes except adding it to the
`certbot -d` list and to `server_name`.

At the registrar, create these two records:

| Type | Name | Value |
|---|---|---|
| A | `@` | `152.228.138.65` |
| A | `www` | `152.228.138.65` |

Optionally add `AAAA` records pointing at `2001:41d0:404:200::8029` for IPv6.

**Do this first, before touching the server** — propagation is the only step here you
cannot hurry, and it runs in the background while you work through §3–§5. Confirm before
requesting a certificate, or certbot fails:

```bash
dig +short promoaluplus.com          # must print 152.228.138.65
dig +short www.promoaluplus.com
```

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

## 7. The queue worker — required even with mail deferred

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

### 13.1 Where the work happens now — settled

**`promoaluplus` is the working copy.** It is cloned locally, dependencies installed, suite
green. `git push` behaves normally; there is no extra publish step.

The old `Alu-workshop` monorepo is kept **only as a rollback** until the first deploy is
verified end to end. Do not make changes there.

> **The two repos can never be merged.** The split (§2) rewrote every commit SHA, so they
> share no common ancestor and git refuses to rebase or merge between them. If something is
> committed to the monorepo by mistake, republish it rather than attempting a merge:
>
> ```bash
> git subtree push --prefix=alu-workshop-laravel \
>     https://github.com/bendhiab-younes/promoaluplus.git main
> ```
>
> That re-derives the split — slow, since it walks every commit, but correct, and it
> fast-forwards the deploy repo cleanly. No `laravel-only` branch is needed; keeping a
> stale one around only invites pushing it by mistake.

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
