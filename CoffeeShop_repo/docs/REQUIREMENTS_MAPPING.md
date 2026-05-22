# Project Requirements Mapping

Mapping the CYS 538 project rubric (15 marks total) to the artefacts in this
repository. Use this file when reviewing the submission to confirm every
graded item has a concrete piece of evidence.

## Rubric coverage

| # | Rubric item | Marks | Where it lives |
|---|-------------|-------|----------------|
| 1 | Login page (maintain the session for the logged user) | 1.00 | `SaudiCoffeeShop_Hardened/login.php` + `screenshots/figures/fig01_login.png` |
| 2 | Pages that contain data viewed from a database | 1.00 | `index.php` (drinks), `food.php` (food) + `fig02`, `fig03` |
| 3 | Pages that send data to the database | 1.00 | `add_product.php`, `register.php`, `order_process.php` + `fig06` |
| 4 | Input validation / sanitization (client + server) | 1.75 | HTML5 + JS in forms; `filter_var`, `preg_match`, `htmlspecialchars` in PHP. Effectiveness shown by `fig14_hardened_xss_escaped.png` |
| 5 | Secure data storage (password storage) | 0.75 | `register.php` uses `password_hash(PASSWORD_DEFAULT)`; `login.php` uses `password_verify()`. Stored hashes shown in `fig08_bcrypt_hashes.png` |
| 6 | Test the website against the chosen attack (Weak Session IDs) | 2.00 | Vulnerable build + `fig09_vuln_document_cookie.png`, `fig10_vuln_hijack_success.png` |
| 7 | Identify and explain the countermeasures | 1.50 | Project report, Section 7 |
| 8 | Practically implement the countermeasures | 1.00 | `SaudiCoffeeShop_Hardened/session_bootstrap.php` + `fig11`–`fig14` |
| 9 | Presentation in class | 3.00 | `report/Presentation.pptx` (final deliverable) |
| 10 | Clear report structure | 2.00 | `report/CYS_538_Report.pdf` (final deliverable) |
| | **Total** | **15.00** | |

## Mandatory features checklist

| Feature | File(s) | Notes |
|---------|---------|-------|
| Login page maintaining a PHP session | `login.php` | `session_regenerate_id(true)` after authentication in the Hardened build |
| Admin page restricted to admins | `admin.php` | Checks `$_SESSION['role'] === 'admin'` |
| Pages that read from the database | `index.php`, `food.php` | Use prepared statements with bound parameter for category |
| Pages that write to the database | `register.php`, `add_product.php`, `order_process.php` | Prepared statements with bound parameters |
| Secure password storage | `register.php`, `login.php` | bcrypt via `password_hash` / `password_verify` |
| Database + connection | `coffeeshop_db.sql`, `connection.php` | MySQLi; `$conn->set_charset('utf8mb4')` |
| Prepared statements on every query | every PHP file with SQL | Verified by grep: no string concatenation into SQL |
| Client-side input validation | HTML5 (`required`, `pattern`, `min`, `max`, `accept`) + JS helpers | See `add_product.php` `<script>` block |
| Server-side input validation | PHP | `filter_var(... FILTER_VALIDATE_INT/FLOAT)`, `preg_match`, `htmlspecialchars`, MIME-type whitelist for uploads |

## LAMP / Ubuntu requirement

The brief requires Apache2 on Ubuntu. All screenshots were captured against
`http://192.168.1.52/...` which is the team's Ubuntu LAMP VM. The Hardened
build is deployed there via `docs/Hardened_Screenshots_Instructions.docx`.

## Countermeasures actually implemented (Weak Session IDs)

All settings live in `SaudiCoffeeShop_Hardened/session_bootstrap.php`:

- `session.use_strict_mode = 1` — PHP rejects session ids it did not issue.
- `session.use_only_cookies = 1` — the id is never read from the URL.
- `session.cookie_httponly = 1` — JavaScript cannot read `PHPSESSID`.
- `session.cookie_samesite = Strict` — the cookie is not sent on cross-site requests.
- `session.cookie_secure = 1` — switched on for HTTPS deployments.
- `session.gc_maxlifetime = 900` + per-request idle check (15-minute timeout).
- `session_regenerate_id(true)` after every privilege change (login).
- SHA-256 fingerprint of `User-Agent` + `REMOTE_ADDR`, verified per request.
