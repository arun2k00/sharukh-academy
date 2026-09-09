# Deploying Oacer Academy to shared hosting

This application is deliberately built to run on an ordinary cPanel or LiteSpeed account.
The production server needs **PHP, MySQL/MariaDB and a web server** — nothing else.

It does **not** need Node.js, Redis, Docker, Supervisor, a queue worker or a websocket
server. Cron is optional.

---

## 1. Build the assets before you upload

Compilation happens on your machine, not the server.

```bash
npm install
npm run build
```

This writes `public/build/`, including self-hosted font files. That directory **must** be
uploaded — it is what the server serves. Nothing re-compiles in production.

---

## 2. Install dependencies for production

```bash
composer install --no-dev --optimize-autoloader
```

`--no-dev` leaves out the development tooling (Pint, PHPUnit, Boost).

---

## 3. Upload the files

Upload everything **except** `node_modules/`, `.git/`, `tests/` and your local `.env`.

### Where the public directory goes

A Laravel application keeps only `public/` in the web root. Pick whichever of these your
host supports:

**Preferred — point the domain at `public/`.**
Put the application in `/home/youruser/oacer-academy` and set the domain's document root
to `/home/youruser/oacer-academy/public`. In cPanel this is *Domains → Manage → Document
Root*. Nothing else needs to change.

**Alternative — when the document root is fixed to `public_html`.**
Put the application in `/home/youruser/oacer-academy`, move the *contents* of `public/`
into `public_html/`, then edit `public_html/index.php` so its two require paths point up
one level:

```php
require __DIR__.'/../oacer-academy/vendor/autoload.php';
$app = require_once __DIR__.'/../oacer-academy/bootstrap/app.php';
```

Never leave `.env`, `storage/` or `vendor/` inside the web root.

---

## 4. Create the database

In cPanel: *MySQL Databases* → create a database, create a user, and grant that user
**all privileges** on it.

```sql
CREATE DATABASE oacer_academy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 5. Configure the environment

Copy `.env.example` to `.env` and fill it in:

```dotenv
APP_NAME="Oacer Academy"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=youruser_oacer
DB_USERNAME=youruser_oacer
DB_PASSWORD=your-database-password

MAIL_MAILER=smtp
MAIL_HOST=smtp.your-host.com
MAIL_PORT=587
MAIL_USERNAME=no-reply@your-domain.com
MAIL_PASSWORD=your-mailbox-password
MAIL_SCHEME=tls
MAIL_FROM_ADDRESS="no-reply@your-domain.com"
```

`APP_DEBUG=false` is not optional — with it on, errors expose file paths and
configuration to visitors.

Then generate the application key:

```bash
php artisan key:generate
```

If your host gives you no SSH access, run the commands in this guide from cPanel's
**Terminal**, or use its *Setup PHP App* interface which provides a command runner.

---

## 6. Create the schema

```bash
php artisan migrate --force
```

`--force` is required because the environment is `production`; the command refuses to run
without it.

To start with the demonstration catalogue instead of an empty site, add `--seed`, then
**sign in and change the seeded administrator password immediately**.

---

## 7. Link storage

Course thumbnails, instructor photos and avatars live on the public disk and are served
through a symlink:

```bash
php artisan storage:link
```

If your host blocks symlinks, create `public/storage` as a normal directory and set
`FILESYSTEM_DISK=public` — or ask support to enable `symlink()`.

Lesson and live-class attachments are stored **outside** the web root on purpose and are
streamed by a controller that checks enrolment, so they are never affected by this step.

---

## 8. Permissions

```bash
chmod -R 755 storage bootstrap/cache
```

Both must be writable by the web server user. On most cPanel accounts PHP already runs as
your user and `755` is correct; use `775` only if your host runs PHP as a separate user,
and never `777`.

---

## 9. Cache the configuration

Run these **after** `.env` is final. They make the application noticeably faster:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### After any future change

Any time you edit `.env`, a config file or a route, clear the caches and rebuild them:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

`php artisan optimize:clear` clears configuration, routes, views, events and the
application cache in one go.

---

## 10. Check it works

Visit the site and confirm:

- [ ] The homepage renders with styling — if it is unstyled, `public/build/` was not uploaded
- [ ] `https://your-domain.com/up` returns a health-check page
- [ ] A course page opens and its curriculum expands
- [ ] Registration works and the verification email arrives
- [ ] Signing in as an administrator reaches `/admin`
- [ ] A course thumbnail displays (proves the storage link works)
- [ ] An enrolled student can download a lesson resource, and a signed-out visitor cannot

---

## Scheduled tasks (live class reminders, access-expiry notices)

The academy sends live class reminders (24h / 1h / 15min before) and course
access-expiry notices from a single scheduled command, `academy:send-reminders`,
which runs every five minutes via Laravel's scheduler. It needs no permanent
queue worker or daemon — just one cron entry pointed at `schedule:run`:

```
* * * * * cd /home/youruser/oacer-academy && php artisan schedule:run >> /dev/null 2>&1
```

Without this cron entry, the application still works normally, but scheduled
reminder emails and expiry notices will not go out — the command is
idempotent and safe to leave running from cron indefinitely, so add it during
initial setup rather than waiting until reminders are needed.

---

## Optional: queued mail

`QUEUE_CONNECTION=sync` is the default and needs no worker — mail is sent during the
request. If outgoing mail is slow enough to be noticeable, switch to `database` and run
the queue from cron rather than a permanent worker:

```
* * * * * cd /home/youruser/oacer-academy && php artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
```

---

## Updating an existing installation

```bash
# On your machine
npm run build
composer install --no-dev --optimize-autoloader

# Upload, then on the server
php artisan down
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
```

---

## Troubleshooting

**Everything is unstyled.**
`public/build/` is missing or was not uploaded. Run `npm run build` locally and upload the
whole directory.

**"Vite manifest not found".**
Same cause — `public/build/manifest.json` is missing.

**A 500 page with no detail.**
That is correct behaviour in production. The cause is in `storage/logs/`.

**Images upload but do not appear.**
The storage link is missing. Re-run `php artisan storage:link` (see step 7).

**Changes to `.env` seem to be ignored.**
The configuration is cached. Run `php artisan config:cache` again.

**"Please provide a valid cache path".**
`bootstrap/cache` is missing or not writable — see step 8.

**Students report they never receive the verification email.**
Set `ACADEMY_REQUIRE_EMAIL_VERIFICATION=false`, run `php artisan route:clear`, and fix the
mail settings before turning it back on.
