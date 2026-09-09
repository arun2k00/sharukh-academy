# Oacer Academy

A complete Learning Management System for a focused online academy — a public marketing
site, a student learning area, and an administration panel, built on Laravel 13 with
Blade, Tailwind CSS 4 and Alpine.js.

It is designed to run on ordinary shared hosting: PHP, MySQL/MariaDB and Apache or
LiteSpeed. **No Node.js, Redis, queue worker, websocket server or Docker is required in
production** — assets are compiled during development and the built files are deployed.

---

## What is included

**Public website**
Homepage, course catalogue with category filtering, full course detail pages with
curriculum, instructor and FAQ sections, free preview lessons, about and contact pages,
and a public certificate verification page.

**Student learning area**
Dashboard with a resume-where-you-left-off panel, my-courses, the learning interface
(YouTube lessons, text lessons, live-class lessons), per-lesson progress with automatic
video watch tracking, protected downloadable resources, Zoom live classes, certificates
(on-screen, printable and publicly verifiable), and profile and password management.

**Administration panel**
Dashboard, course management, a curriculum builder with reliable ordering controls,
lesson management for every lesson type, resource uploads, live class scheduling,
students, enrolments, certificates, categories, instructors, testimonials, FAQs, contact
messages and editable site settings.

---

## Requirements

| | |
|---|---|
| PHP | 8.3 or newer (`pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `ctype`, `json`, `bcmath`) |
| Database | MySQL 8+ or MariaDB 10.6+ |
| Web server | Apache or LiteSpeed with `mod_rewrite`, or nginx |
| Composer | 2.x |
| Node.js | **Development only**, for compiling assets |

---

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Create the database and point `.env` at it:

```sql
CREATE DATABASE oacer_academy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=oacer_academy
DB_USERNAME=root
DB_PASSWORD=
```

Then migrate, seed the demonstration academy, link storage and build the assets:

```bash
php artisan migrate --seed
php artisan storage:link
npm install && npm run build
php artisan serve
```

### Demonstration accounts

The seeder creates a complete working academy — 6 published courses, 1 draft, 18
modules, 57 lessons, live classes, enrolments at various stages of completion, and the
certificates those completions produced.

| Role | Email | Password | State |
|---|---|---|---|
| Administrator | `admin@oaceracademy.test` | `password` | Full admin access |
| Student | `meera@example.test` | `password` | One course finished, one half done |
| Student | `rohit@example.test` | `password` | Part way through a free course |
| Student | `tom@example.test` | `password` | Enrolled, nothing started |

These are local demonstration credentials only. Change or remove them before going live.

> The seeded lessons point at public YouTube sample videos and the course thumbnails are
> generated placeholders. Replace both with the academy's own material.

---

## Running the tests

```bash
php artisan test
```

180 tests cover authentication and rate limiting, authorisation boundaries (guests,
students and administrators), enrolment, progress and completion arithmetic, protected
downloads, live-class join windows, certificate issuing and duplicate prevention,
curriculum ordering, and the rendering of every page in the application.

---

## Configuration worth knowing

All of these live in `.env`; see `config/academy.php` for the defaults.

| Variable | Purpose |
|---|---|
| `ACADEMY_CERTIFICATE_THRESHOLD` | Default completion percentage before a certificate is issued. Individual courses can override it. |
| `ACADEMY_LIVE_JOIN_WINDOW` | Minutes before a live class starts that the Zoom join button unlocks. |
| `ACADEMY_REQUIRE_EMAIL_VERIFICATION` | Leave `false` until outgoing mail is confirmed working, otherwise new students are locked out by an email that never arrives. Run `php artisan route:clear` after changing it. |
| `PAYMENT_DRIVER` | `manual` by default — see below. |

### Payments

The application ships with a payment-ready architecture but **no payment processing**.
With the default `manual` driver, requesting a paid course records a pending order and
tells the student the academy will be in touch; an administrator then grants access from
**Admin → Enrolments**. Nothing is ever marked paid automatically and no access is
granted without a real enrolment.

To add a gateway later, implement `App\Services\Payments\PaymentGateway`, register it in
`App\Services\Payments\PaymentManager::$gateways`, and set `PAYMENT_DRIVER`. No
controller, route or view has to change.

### Videos and meeting links

Lesson videos accept any YouTube URL shape; only the extracted video ID is stored, and
embeds are served from `youtube-nocookie.com`. Zoom links are restricted to Zoom's own
domains over HTTPS, are never rendered into a page, and are only handed out by a redirect
to an enrolled student inside the join window.

---

## Deploying

See **[DEPLOYMENT.md](DEPLOYMENT.md)** for the shared-hosting guide.
