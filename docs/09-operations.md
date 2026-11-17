# 09 — Operations manual

## 1. Components and ports

| Component | Runs as | Default port | Exposed to the internet? |
|---|---|---|---|
| Next.js web client | Node 20.12+ process | 3000 | Yes, through the reverse proxy |
| Laravel API | PHP-FPM 8.2+ behind nginx | 8000 | Only `/api/v1/integrations/*` (payment callback). Everything else is reached by the Next server over localhost or the private network |
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
| `PAYMENT_WEBHOOK_SECRET` | Shared secret for the payment callback; empty disables it |
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
| Rotate the payment callback secret | Change `PAYMENT_WEBHOOK_SECRET`, `php artisan config:cache`, give the new value to the payment platform |

## 9. Connecting real providers

- **Email:** set the `MAIL_*` keys. No code change.
- **SMS:** implement the gateway's HTTP call in `Notifier::sms()` (`backend/app/Services/Notifier.php`), selected by `SMS_DRIVER`.
- **Payment platform:** adapt `FinanceController::gatewayCallback` to the provider's payload and signature. If the provider issues control numbers, replace the generated number in `BillingService::billAllocation` with a call to the provider.

## 10. Security checklist before go-live

- [ ] HTTPS only; HTTP redirects to HTTPS
- [ ] `APP_DEBUG=false`
- [ ] Demo seeder never run; no `@gememis.test` or `@exhibitor.test` accounts exist
- [ ] First administrator has changed the one-time password
- [ ] Database user limited to the GeMEMIS database
- [ ] API not reachable from the internet except the callback path
- [ ] Audit-trail triggers installed
- [ ] Backups running and a restore tested
- [ ] Cron entry for the scheduler installed
- [ ] `PAYMENT_WEBHOOK_SECRET` set to a long random value, or left empty if unused
