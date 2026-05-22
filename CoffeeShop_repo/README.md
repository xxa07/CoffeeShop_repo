# SaudiCoffeeShop — CYS 538 Project

A small PHP/MySQL web application built for **CYS 538: Web Technology and Security**
(Imam Abdulrahman Bin Faisal University, College of Computer Science and Information
Technology — Section 10FY02, Group 1).

The project demonstrates a chosen web vulnerability and the security countermeasures
that defeat it.

## Chosen vulnerability

**Weak Session IDs** — OWASP Top 10 A07: Identification and Authentication Failures,
CWE-384 (Session Fixation), CWE-330 (Use of Insufficiently Random Values).

## Repository layout

```
SaudiCoffeeShop_Vulnerable/   The "before" state. Session management uses PHP defaults:
                              no HttpOnly, no SameSite, no use_strict_mode, no
                              session_regenerate_id() after login, no fingerprint
                              binding. A session-hijacking attack succeeds.

SaudiCoffeeShop_Hardened/     The "after" state. session_bootstrap.php enables
                              HttpOnly, SameSite=Strict, use_strict_mode,
                              use_only_cookies, a 15-minute idle timeout, and
                              SHA-256 fingerprint binding to User-Agent + IP.
                              The login handler rotates the session id after
                              authentication. The same attack now fails.

docs/                         Project documentation and screenshot capture guide.
```

Each build contains its own `README.txt` with deployment and demo instructions.

## Stack

- HTML5 + CSS + a small amount of JavaScript on the client side
- PHP 8 on the server side, using MySQLi prepared statements throughout
- MySQL / MariaDB (database `coffeeshop_db`, schema in `coffeeshop_db.sql`)
- Apache2 on Ubuntu (LAMP), tested on `192.168.1.52`

## Quick start

1. Install LAMP on Ubuntu (or use XAMPP on Windows / MAMP on macOS).
2. Copy the contents of either build into the web root (e.g. `/var/www/html/`).
3. Open phpMyAdmin, create a database named `coffeeshop_db`, and import
   `coffeeshop_db.sql`.
4. Open `http://<server>/login.php` in the browser.

## Features

- Login page with PHP session and bcrypt password verification.
- Public pages that read from the database (`index.php` for drinks, `food.php` for food).
- Authenticated pages that write to the database (`register.php`, `add_product.php`,
  `order_process.php`).
- Admin-only page (`admin.php`) with product management and order review.
- Secure password storage via `password_hash()` / `password_verify()` (bcrypt).
- Server-side validation with `filter_var`, `preg_match`, `htmlspecialchars`, and
  MIME-type checks on uploads; client-side validation with HTML5 attributes and a
  small JavaScript helper.
- Prepared statements on every SQL query that touches user input.

## Countermeasures applied (Hardened build only)

| Control | Implementation |
|---------|----------------|
| HttpOnly cookie | `session.cookie_httponly = 1` |
| SameSite cookie | `session.cookie_samesite = Strict` |
| Secure cookie  | `session.cookie_secure = 1` on HTTPS deployments |
| Strict session ids | `session.use_strict_mode = 1` |
| Cookie-only sessions | `session.use_only_cookies = 1` |
| Idle timeout | 15 minutes (`gc_maxlifetime` + per-request check) |
| Privilege rotation | `session_regenerate_id(true)` on login |
| Fingerprint binding | SHA-256 of `User-Agent` + `REMOTE_ADDR`, checked per request |

See `SaudiCoffeeShop_Hardened/session_bootstrap.php`.

## Team

Group 1, Section 10FY02:

- Albatool Alwadai — 2210002412
- Sughiah Naqi — 2220001824
- Nouf Alshuaibi — 2210003117
- Aryam Hadi Alyami — 2220001144
- Sarah Alotaibi — 2220006836

## License

Submitted for academic evaluation in CYS 538 (2025-2026, 2nd semester).
Course code samples and PHP / OWASP examples are credited in the project report.
