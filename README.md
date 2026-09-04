# KPSTA website

CodeIgniter **4.7.4** on **PHP 8.2+**. Migrated from CodeIgniter 3.1.0 / PHP 7.

This file is the handover note for the migration: what changed, how to run it,
what to check before it goes live, and what was deliberately left alone.

---

## 1. TL;DR

Four commits on this branch:

| Commit | What |
|---|---|
| `Make the CodeIgniter 3 app run on PHP 8` | The CI3 app, fixed to run on PHP 8.2+. Kept as a working fallback in history. |
| `Migrate the application from CodeIgniter 3 to CodeIgniter 4` | The CI4 migration. `application/` and `system/` are gone. |
| `Add developer handover README...` | This file, plus removal of stale CI3 files. |
| `Replace the cp-based deploy...` | `deploy.sh`, because the old deploy cannot ship a CI4 site. |

The first commit is a complete, deployable state on its own. If CI4 needs more
soak time than you have, you can ship that commit and keep the site on CI3
while it runs on a supported PHP.

**Two things need you before this goes live:** rotate the credentials in
[§7](#7-security-actions-you-still-need-to-take) — they are still in git history
— and verify against a real database copy, [§8](#8-what-to-check-before-this-goes-live).
Deployment itself is now handled by `deploy.sh`; see [§6](#6-deployment).

---

## 2. What changed

### PHP 8 (first commit)

| Problem | Fix |
|---|---|
| CI 3.1.0 does not parse on PHP 8 — `Profiler.php` used the removed `$this->_compile_{$section}` syntax | Upgraded `system/` to CI **3.1.13**, the final CI3 release. The tree was stock and unmodified, so this was a clean swap. |
| **Swiftmailer 5.4.4 is a hard `ParseError` on PHP 8** (`$line{3}` in `AbstractSmtpTransport`). The contact form was fatal. | Replaced with **symfony/mailer** `^6.4`. Swiftmailer is EOL and its `newInstance()` factories were removed in 6.x, so the library was rewritten rather than version-bumped. |
| Vendored TCPDF 6.2.13 (30 MB) emitted a deprecation for every optional-parameter-before-required declaration, on every request that loaded it | Composer **tecnickcom/tcpdf `^6.11`**. Same output, zero deprecations. |
| `$_POST['pdfName']` was read unguarded and concatenated onto an upload path before `unlink()` — a path traversal | `posted_filename()` helper: guards the read, strips any path, rejects separators. |
| Credentials committed to the repo | Moved to environment variables. **See [§7](#7-security-actions-you-still-need-to-take) — the old ones are still in git history.** |

### CodeIgniter 4 (second commit)

- Framework now comes from Composer (`vendor/codeigniter4/framework`). `application/`
  and `system/` deleted.
- **45** controllers (41 ported + 4 base classes), **35** models (33 + 2 base),
  **135** views.
- **224** CI3 routes became **349** explicit CI4 routes.
- Config moved to `.env`. `env.example` is the template.
- Aauth ported into `app/Libraries/Aauth.php` (see [§5](#5-authentication-aauth)).

### Deployment (fourth commit)

- `deploy.sh` replaces `cp * $DEPLOYPATH`, which cannot ship a CI4 site and
  whose obvious fixes would either publish `.git` under the document root or
  delete `uploads/`. See [§6](#6-deployment).
- `.htaccess` additionally denies `.git`, `.env` and the dependency manifests.

---

## 3. The one thing to understand: the compatibility layer

**Read this before you touch a controller.**

CI3 and CI4 share no migration path. This application is ~35k lines with roughly
**3,400 CI3 API call sites across 117 files** — `$this->load->view()`,
`$this->input->post()`, `$this->db->order_by()`, `redirect()`, and so on.

Rewriting all of those by hand would have produced a diff nobody could
meaningfully review, with no way to confirm correctness short of full manual QA
of every screen.

So instead: **the app runs on CI4's kernel behind a compatibility layer that
implements the CI3 surface the code actually uses.** Controller and model bodies
ported across essentially unchanged.

```
app/Libraries/Ci3/
├── Loader.php          $this->load        (model, library, view, vars, helper, database, config)
├── Input.php           $this->input       (post, get, server, cookie, is_ajax_request, ip_address)
├── Uri.php             $this->uri         (segment, uri_string, ...)
├── Session.php         $this->session     (userdata, set_userdata, flashdata, ...)
├── Output.php          $this->output      (nocache, set_output, set_header, ...)
├── Config.php          $this->config      (load, item)
├── Lang.php            $this->lang        (load, line)
├── Email.php           $this->email       -> App\Libraries\Mail
├── Encrypt.php         $this->encrypt     -> CI4 Encryption
├── FormValidation.php  $this->form_validation
├── Pagination.php      $this->pagination
├── Upload.php          $this->upload
├── Registry.php        request-scoped state for the global helpers
└── Database/
    ├── Connection.php  $this->db          CI3 query builder over CI4's
    └── Result.php                         num_rows(), result_array(), row(), ...
```

Plus `app/Common.php`, which redefines `base_url()`, `site_url()`, `redirect()`,
`show_404()`, `get_instance()`, `form_error()`, `validation_errors()` and
`set_value()` with CI3 semantics. CI4 loads `app/Common.php` *before*
`system/Common.php` and guards every global with `function_exists()` — this is
the framework's own documented extension point, not a hack.

### Four behaviours that are deliberate, not accidental

These will look wrong if you don't know why they're there. Please don't
"fix" them without reading:

1. **`redirect()` sends the header and calls `exit`** — it does *not* return a
   `RedirectResponse` like CI4's. All 27 call sites invoke it as a statement,
   and the admin/membership access checks depend on it halting the request.
   Making it return would let an unauthorised request continue into the
   controller.

2. **Views are rendered with `$this` bound to the controller.** 22 view files
   read `$this->uri->segment()`, `$this->session->userdata()` and
   `$this->aauthGroupId` directly. CI4's renderer binds `$this` to the *view*
   object, which would break all of them.

3. **View data accumulates across a request.** CI3 merged every array passed to
   `view()` into a cache that later views could also see. This is load-bearing:
   `admin/download/download.php` reads `$menuType`, which is only ever passed to
   a partial rendered *earlier* in the same request. Dropping it turns that into
   an undefined variable. (I hit this during the migration.)

4. **CI3 constructors became `ci3Init()`.** CI4 has no request, response or
   session attached at construction time, and the access checks need all three.
   `BaseController::initController()` calls `ci3Init()` once everything exists.
   A ported controller chains with `parent::ci3Init()` exactly where it used to
   call `parent::__construct()`.

### Controller hierarchy

```
CodeIgniter\Controller
└── App\Controllers\BaseController      wires the CI3 accessors, buffers view output
    └── AppController                   was MY_Controller  — admin access check, pagination config
        ├── PublicController            was Public_Controller
        └── MembershipController        was Membership_Controller
```

---

## 4. Running it locally

```bash
composer install
cp env.example .env          # then fill in the database section
php spark key:generate       # writes encryption.key into .env
```

Point a vhost at the **project root** (not `public/` — see [§6](#6-deployment)),
or for a quick look:

```bash
php -S 127.0.0.1:8080 -t . _router.php
```

`_router.php` is a tiny front-controller shim for PHP's built-in server; it is
gitignored and not used in production. Create it with:

```php
<?php
$p = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($p !== '/' && file_exists(__DIR__ . $p) && !is_dir(__DIR__ . $p)) return false;
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
```

Set `CI_ENVIRONMENT = development` in `.env` while working — CI4 turns warnings
into exceptions there, which surfaces problems that CI3 used to swallow.

---

## 5. Authentication (Aauth)

**Aauth was ported, not rewritten — deliberately.** It is the only thing between
the admin and membership areas and the public internet, and it carries a lot of
accumulated behaviour: group inheritance, per-menu permissions, TOTP,
login-attempt throttling. Reimplementing that against the same tables is exactly
how authentication holes get introduced.

Only the plumbing changed:

- namespaced into `App\Libraries\Aauth`
- `PHPGangsta_GoogleAuthenticator` and `ReCaptcha` (defined in `app/Helpers/`)
  qualified to the global namespace
- `$this->CI` resolves via `get_instance()`
- the database handle is the CI3-shaped wrapper, so the query builder calls
  inside are untouched

**The `aauth_*` tables and session keys are unchanged**, so stored passwords and
live sessions survive the migration. Nobody gets logged out.

Config lives in `app/Config/Ci3/aauth.php` (CI3 format, read through the
`Config` shim).

---

## 6. Deployment

Deployment is handled by **`deploy.sh`**, invoked from `.cpanel.yml`. Run it by
hand or from any CI with `DEPLOYPATH` set:

```bash
DEPLOYPATH=/home1/credaqwv/public_html/kpsta/kpsta.in ./deploy.sh
```

### Why the old one-liner had to go

The previous task was `cp * $DEPLOYPATH`. That worked under CI3, where the
framework was committed under `system/`. It cannot work now, and the obvious
fixes are worse:

| Problem | Consequence |
|---|---|
| **CI4 lives in `vendor/`, which is gitignored and untracked** | The server gets an application with no framework — a dead site. |
| `cp` without `-r` does not recurse | `app/`, `public/`, `css/` never transfer. |
| `cp *` is a glob, so it skips dotfiles | `.htaccess` and `.env` never transfer. |
| "Just use `cp -r .`" | **Publishes `.git` under the document root** — ~98MB whose history contains the credentials listed in §7. Full repository disclosure. |
| Any `rsync --delete` variant | **Destroys `uploads/`**, which is user data that exists only on the server. |

### What `deploy.sh` guarantees

- **Never copies** `.git`, `.env`, `node_modules`, `tests/`, the Playwright
  config, `_router.php`, or itself.
- **Never deletes anything** at the destination, so `uploads/`, the server's
  `.env` and live sessions survive every deploy.
- Runs `composer install --no-dev --optimize-autoloader` if Composer is on the
  host. If it is not, and `vendor/` is missing, it **fails loudly** rather than
  leaving a frameworkless site. If `vendor/` is already there it warns that
  dependencies were not refreshed.
- Creates `writable/` subdirectories and warns if `.env` is absent.

It prefers `rsync` and falls back to `tar` when rsync is unavailable, so it
works on a bare shared host.

**If the host has no Composer at all**, build locally with
`composer install --no-dev --optimize-autoloader` and upload `vendor/` once by
hand, repeating whenever `composer.lock` changes. Do **not** commit `vendor/` —
it is third-party code and would make every future diff unreadable.

### One-time server setup

- Create `.env` on the server by hand from `env.example`, with the real
  credentials and `CI_ENVIRONMENT = production`. It is gitignored and
  `deploy.sh` never overwrites it.
- Ensure `writable/` is writable by the web user.
- **If an earlier deploy already left `.git` in the document root, delete it**,
  then rotate the credentials in §7. `.htaccess` now blocks `.git`, `.env` and
  the dependency manifests as a second line of defence, but a rewrite rule is
  not a substitute for the files not being there.

### About the document root

The front controller is at the **project root**, not in `public/`, because that
is how the host serves this site.

CodeIgniter's preferred arrangement points the document root at `public/` so
`app/`, `writable/` and `vendor/` are unreachable over HTTP by construction. The
`.htaccess` denies are equivalent protection *only while `.htaccess` is being
honoured*. If the host lets you move the document root, do it — that fails safe
instead of relying on a rewrite rule.

---

## 7. Security actions you still need to take

### Rotate the leaked credentials — they are still in git history

Removing them from `HEAD` does not remove them from history. All of these were
committed and must be treated as compromised:

| Credential | Was in |
|---|---|
| Production DB password (`Kpsta987`) | `application/config/database.php` |
| A second production DB password (`hrrcuPxUG9n3xnN`) | `application/config/database_prod.php` |
| Gmail app password | `application/libraries/Mail.php` |
| reCAPTCHA secret key | `application/config/aauth_prod.php` |

Rotate each at the source (hosting panel, Google account, reCAPTCHA admin), then
put the new values in `.env` on the server. Purging history with
`git filter-repo` is optional and doesn't help if the old values still work —
**rotate first.**

Worth knowing: the CI3 config picked the active database by sniffing
`HTTP_HOST`, which is client-controlled. That's gone.

### Two things left as they were, on purpose

- **CSRF protection is off**, as it was under CI3. The 33 existing forms carry
  no tokens, so enabling it is its own change — turn on the `csrf` filter in
  `app/Config/Filters.php` and add `<?= csrf_field() ?>` to every form.
- **Aauth's password hashing is weak.** `use_password_hash` is `false`, so
  passwords are `sha256(md5(user_id) . password)` compared with `==`. Switching
  to bcrypt means flipping that flag *and* writing a rehash-on-login migration
  (verify against the old scheme, re-hash with the new one on success). Not a
  change to make casually — it touches every existing account.

---

## 8. What to check before this goes live

Verification so far was done on PHP 8.4 against a live MariaDB:

- all **23** public routes return 200
- all **47** authenticated admin routes return under 500
- login, session, PDF generation and the database backup all work
- 291 files lint clean; PHPCompatibility 8.2–8.4 reports **0 errors**
- production mode renders with no debug output leaking
- rendered output matches the CI3 baseline **to within one byte per page**

**Two gaps that only you can close:**

1. **The database was reconstructed, not real.** I built the schema by replaying
   the app's own SQL errors, so tables were structurally correct but **almost
   entirely empty**. Loops over populated result sets never executed. This is
   the largest remaining unknown and where a query-builder translation bug would
   surface. Restore a copy of production into a scratch database and click
   through.

2. **Write paths were not exercised end to end.** Reads were. Creating,
   editing, deleting and file upload were not driven with real payloads.

### Suggested order

1. Restore a production database copy locally.
2. Run the existing Playwright suite — 6 spec files in `tests/`, covering the
   admin CRUD flows. It points at `http://kpsta.test`; adjust `baseURL` in
   `playwright.config.js`.
3. Manually exercise, with real data: add/edit/delete for news, order-circular,
   gallery and downloads; a file upload of each permitted type; membership
   teacher add + bulk add; PDF generation from the membership area; the contact
   form (needs `mail.dsn` set).
4. Check `writable/logs/` afterwards — CI4 logs warnings there even when the
   page renders fine.
5. Deploy to a staging URL before production.

---

## 9. Known issues and limitations

- `admin/backup/kpsta_db` and `admin/backup/website` are routed but the
  controller methods were **never implemented**. They 404 — exactly as they did
  under CI3. Either implement them or drop the routes.
- The compatibility layer implements the CI3 surface **this application uses**,
  not all of CI3. Code that reaches for an unimplemented method will get a clear
  `RuntimeException` / `BadMethodCallException` rather than silent breakage —
  add the method to the relevant shim.
- `Pagination.php` reproduces CI3's markup for the Bootstrap tag config this app
  passes it. Other configurations are supported but less exercised.
- `Upload.php` is slightly *stricter* than CI3: it checks the real MIME type as
  well as the extension, so a `.php` renamed to `.jpg` is now rejected. If a
  legitimate upload type gets refused, that check is the first place to look.

### Fixed along the way

`admin/Backup::downloadDB` stat'd a different path than it wrote to (wrong
`Content-Length`, plus a warning that CI4's stricter handling turns fatal), and
interpolated the database password into a shell command unescaped. Arguments are
escaped now and the password goes via `MYSQL_PWD` so it stays out of the process
list.

---

## 10. Modernising later (optional)

Nothing forces this. The shim is stable and can stay indefinitely. But if you
want to retire it, do it file by file rather than all at once — there is no
deadline and no big-bang step:

| CI3 | CI4 |
|---|---|
| `$this->load->view('x', $d)` | `return view('x', $d)` |
| `$this->load->model('X_model')` | `new \App\Models\X_model()` |
| `$this->input->post('x')` | `$this->request->getPost('x')` |
| `$this->uri->segment(3)` | pass it as a route parameter instead |
| `$this->db->order_by(...)` | `$db->table(...)->orderBy(...)` |
| `redirect('x')` | `return redirect()->to('x')` |
| `$this->form_validation` | CI4's `Validation` service |

A sensible finishing line: once a controller no longer touches `$this->load`,
`$this->input` or `$this->uri`, change its base class from `AppController` to
CI4's `BaseController` and delete its `ci3Init()`.

`redirect()` is the one to save for last — it is global, and changing its
semantics affects every call site at once.
