# 09 — Operations manual

## 1. Components and ports

| Component | Runs as | Default port | Exposed to the internet? |
|---|---|---|---|
| Next.js web client | Node 20.12+ process | 3000 | Yes, through the reverse proxy |
| Laravel API | PHP-FPM 8.2+ behind nginx | 8000 | Only `/api/v1/integrations/gepg*` (GePG callbacks), ideally only from GePG's addresses. Everything else is reached by the Next server over localhost or the private network |
| Database | MySQL 8 / MariaDB 10.6+ | 3306 | No |
| File storage | `backend/storage/app/private` | — | No |

## 2. Configuration

### API — `backend/.env`
| Key | Purpose |
|---|---|
| `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` | Standard Laravel |
| `APP_TIMEZONE` | Default `Africa/Dar_es_Salaam` |
| `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Use a dedicated database and a dedicated database user |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` | Email notifications. `log` until SMTP is available |
| `FRONTEND_URL` | Base of links in emails |
| `SANCTUM_TOKEN_MINUTES` | Session length; default 720 |
| `QUEUE_CONNECTION` | `database` in production (run a worker, §3); `sync` runs jobs inline for development |
| `GEPG_*` | See §9 |
| `SMS_DRIVER`, `SMS_SENDER` | `log` until a gateway adapter is written |

### Web client — `frontend/.env`
| Key | Purpose |
|---|---|
| `API_URL` | Where the API is, as seen from the Next server, ending `/api/v1` |
| `NEXT_PUBLIC_APP_NAME` | Name in the browser title |

## 3. First deployment

API:

```bash
cd backend && composer install --no-dev --optimize-autoloader && php artisan key:generate
```

```bash
cd backend && php artisan migrate --force
```

```bash
cd backend && ADMIN_EMAIL=admin@example.go.tz php artisan db:seed --class=ProductionSeeder --force
```

The last command creates the first System Administrator and prints a one-time password. **Do not run the default seeder in production** — it creates demo accounts with a known password.

```bash
cd backend && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Web client:

```bash
cd frontend && npm ci && npm run build
```

```bash
cd frontend && npm run start -- -p 3000
```

Run the web client under a process manager (systemd or pm2) so it restarts on failure and on boot.

### Reverse proxy
- Serve the public hostname over HTTPS and proxy `/` to the Next process.
- Proxy `/api/v1/integrations/` to the API if a payment platform will call back.
- Set `client_max_body_size 12m` (documents and payment evidence are up to 10 MB).
- The web client sends HSTS, CSP and the other security headers itself in production.

### Scheduler (required)
One cron entry runs the daily housekeeping (close registration, mark overdue invoices, send reminders):

```bash
* * * * * cd /path/to/backend && php artisan schedule:run >> /dev/null 2>&1
```

To run it by hand:

```bash
cd backend && php artisan gememis:housekeeping
```

### File permissions
`backend/storage` and `backend/bootstrap/cache` must be writable by the PHP user. Uploaded files live in `storage/app/private` and are never served directly.

## 4. Hardening the audit trail in the database

The application refuses to change audit entries. Add triggers so the database refuses too (MySQL/MariaDB):

```sql
CREATE TRIGGER audit_logs_no_update BEFORE UPDATE ON audit_logs
FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_logs is append-only';
CREATE TRIGGER audit_logs_no_delete BEFORE DELETE ON audit_logs
FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_logs is append-only';
```

Also grant the application's database user no `DROP` or `TRUNCATE` on that table.

## 5. Updating

```bash
cd backend && php artisan down
```

Pull the new code, then:

```bash
cd backend && composer install --no-dev --optimize-autoloader && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

```bash
cd frontend && npm ci && npm run build
```

Restart the web client process, then:

```bash
cd backend && php artisan up
```

Take a database backup before any update that includes migrations.

## 6. Backup and restore

Back up **both** the database and `backend/storage/app/private` (documents and payment evidence); one without the other is incomplete.

```bash
mysqldump --single-transaction gememis | gzip > gememis-$(date +%F).sql.gz
```

```bash
tar czf gememis-files-$(date +%F).tar.gz -C /path/to/backend/storage/app private
```

Daily, kept off the server, with a restore rehearsed at least once before go-live. Restore is the reverse: load the dump into an empty database, unpack the files, run `php artisan migrate --force`.

## 7. Monitoring

| Check | How |
|---|---|
| API alive | `GET /api/v1/health` returns `{"status":"ok"}` |
| Web client alive | `GET /login` returns 200 |
| Errors | `backend/storage/logs/laravel.log`; the web client's process log |
| Scheduler running | A `Housekeeping complete` run each day; overdue invoices change status |
| Failed sign-ins | Audit trail, area `auth` |

## 8. Routine administration

| Task | Where |
|---|---|
| Create, deactivate, unlock users; reset passwords | Users page (System Administrator) |
| Unlock an administrator who is locked out | `php artisan tinker` → `App\Models\User::where('email','…')->update(['locked_at'=>null,'failed_logins'=>0]);` |
| Prune expired tokens | Scheduled daily (`sanctum:prune-expired`) |
| Replace GePG keys | New key pair and certificate exchange with GePG; update the files and paths, `php artisan config:cache` |

## 9. GePG (control numbers and payments)

Public money must be collected through GePG (Public Finance Act, as amended by the Finance Act 2017). GeMEMIS is built to be the institution's own **Service Provider system**: it sends bills, receives control numbers and payments, and reconciles daily. Until the institution is registered it runs against a built-in **simulator** (`GEPG_MODE=simulator`).

### 9.1 Onboarding (the institution, with the Ministry of Finance)

1. The accounting officer of the institution collecting the fees applies to the Ministry of Finance (GePG) to register it as a **Service Provider**, naming GeMEMIS as the billing system. The GePG helpdesk manual gives gepg.support@hazina.go.tz; confirm the current contact with MoF.
2. Agree the **collection bank account** and the **GFS revenue codes** for exhibition fees.
3. Receive: SP code, sub-SP code, system ID; the **test environment** address; the **integration specification**; GePG's **certificate**.
4. Generate GeMEMIS's key pair on the server and give GePG the public certificate:

```bash
openssl req -x509 -newkey rsa:2048 -keyout gememis-gepg.key -out gememis-gepg.crt -days 730 -subj "/CN=GeMEMIS GePG"
```

5. Register GeMEMIS's **callback address** (shown on the Payment gateway page) and the server's public IP with GePG.
6. Check the message formats in the specification against `backend/app/Services/Gepg/GepgXml.php`, the paths and headers in `config/gepg.php`, and the status codes. Adjust there only.
7. Run UAT-19 (`07-test-plan.md`) in GePG's test environment, then switch to production values.

### 9.2 Settings (`backend/.env`)

| Key | Value |
|---|---|
| `GEPG_MODE` | `off`, `simulator` or `live` |
| `MANUAL_PAYMENTS` | `exception` (default): Finance may record a payment outside GePG, with evidence and a reason, verified by a second Finance Officer. `off`: GePG only — nobody can record a payment by hand. Keep at least two active Finance Officers while it is `exception` |
| `GEPG_SP_CODE`, `GEPG_SUB_SP_CODE`, `GEPG_SP_SYS_ID`, `GEPG_SP_NAME` | From MoF |
| `GEPG_BASE_URL` | GePG's address (test, then production) |
| `GEPG_PATH_BILL`, `GEPG_PATH_REUSE`, `GEPG_PATH_CANCEL`, `GEPG_PATH_RECONCILIATION` | Only if the specification's paths differ from the defaults |
| `GEPG_COM` | Value of the `Gepg-Com` header |
| `GEPG_PRIVATE_KEY_PATH`, `GEPG_PRIVATE_KEY_PASSPHRASE` | GeMEMIS's private key, outside the repository, readable only by the PHP user |
| `GEPG_CERTIFICATE_PATH` | GePG's certificate |
| `GEPG_SIGNATURE_ALGORITHM` | `sha1` or `sha256`, as the specification says |
| `GEPG_DEFAULT_GFS_CODE` | Used when a space category has no GFS code |
| `GEPG_PAYMENT_OPTION` | `1` full, `2` part payments allowed, `3` exact — as agreed with MoF |
| `GEPG_COLLECTION_ACCOUNT` | Collection account number |
| `GEPG_CODE_FAILURE`, `GEPG_CODE_INVALID_SIGNATURE` | The specification's codes, if they differ from 7201 / 7303 |

Set a **GFS code on every space category** (exhibition page → category → Edit). In simulator mode with no keys configured, GeMEMIS generates its own test keys in `storage/app/private/gepg-simulator`; live mode refuses to start bills without real keys.

### 9.3 Running

- Bills are sent by a queued job. In production use `QUEUE_CONNECTION=database` and keep a worker running (systemd or pm2): `php artisan queue:work --tries=5`.
- The scheduler (§3) runs housekeeping (retries failed bills, extends expired control numbers) and `gepg:reconcile` every morning for the day before. By hand: `php artisan gepg:reconcile 2027-03-18`.
- The **Payment gateway** page shows the mode, problems, reconciliation results and the verbatim message log.
- Simulator only: `php artisan gepg:simulate-payment <control number> [amount] [--silent]`.

### 9.4 Other providers

- **Email:** set the `MAIL_*` keys. No code change.
- **SMS:** implement the gateway's HTTP call in `Notifier::sms()` (`backend/app/Services/Notifier.php`), selected by `SMS_DRIVER`. Control numbers are sent by SMS, so do this before go-live.

## 10. Security checklist before go-live

- [ ] HTTPS only; HTTP redirects to HTTPS
- [ ] `APP_DEBUG=false`
- [ ] Demo seeder never run; no `@gememis.test` or `@exhibitor.test` accounts exist
- [ ] First administrator has changed the one-time password
- [ ] Database user limited to the GeMEMIS database
- [ ] API not reachable from the internet except `/api/v1/integrations/gepg*`, ideally restricted to GePG's addresses
- [ ] Audit-trail triggers installed
- [ ] Backups running and a restore tested
- [ ] Cron entry for the scheduler installed
- [ ] `GEPG_MODE=live` with production keys; private key file readable only by the PHP user; every space category has a GFS code
- [ ] Queue worker running; first morning reconciliation shows *All matched*
