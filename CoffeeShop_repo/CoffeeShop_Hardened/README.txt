SaudiCoffeeShop — Hardened build
================================

This is the AFTER state described in Section 7 of the report.
The same session-hijacking attack that succeeds in the Vulnerable
build is blocked in this build by the controls listed below.

What was added
--------------
1. session_bootstrap.php (new file)
   - session.cookie_httponly = 1       (cookie hidden from JavaScript)
   - session.cookie_secure   = 0/1     (1 on HTTPS, 0 on plain localhost)
   - session.cookie_samesite = Strict  (no cross-site sending)
   - session.use_strict_mode = 1       (PHP rejects unknown session ids)
   - session.use_only_cookies= 1       (the id is never read from the URL)
   - 15-minute idle timeout
   - SHA-256 fingerprint binding to User-Agent + Remote IP

2. login.php
   - session_regenerate_id(true) after successful authentication
     (defeats session fixation).
   - All user-controlled messages are echoed through htmlspecialchars().
   - Stricter server-side validation of the username/password input.

3. add_product.php
   - JavaScript onsubmit() helper repeats the HTML5 rules client-side.
   - All inline <script>alert(...)</script> output replaced with
     server-side validation messages and proper redirects.
   - Description is sanitized with htmlspecialchars() before storage.
   - Image upload is validated by real MIME type, not extension.

4. order_process.php
   - Removed inline JavaScript alerts (which could echo user values).
   - All branches end with a clean redirect.

5. register.php / admin.php / navbar.php / index.php / food.php /
   logout.php
   - Every page that touches a session uses session_bootstrap.php
     before any output, so the HttpOnly / SameSite / Secure flags
     are guaranteed to be in effect.

What stayed the same
--------------------
- bcrypt password hashing with password_hash / password_verify.
- MySQLi prepared statements on every SQL query.
- Same database schema (coffeeshop_db.sql).

How to run on XAMPP (Windows)
-----------------------------
1. Copy this folder into  C:\xampp\htdocs\SaudiCoffeeShop_Hardened
2. Start Apache and MySQL.
3. http://localhost/phpmyadmin -> create database coffeeshop_db
   and import coffeeshop_db.sql.
4. Open http://localhost/SaudiCoffeeShop_Hardened/login.php
5. Default accounts (same as the Vulnerable build).

How to verify the countermeasures
---------------------------------
A) HttpOnly: log in, open DevTools -> Console, run document.cookie .
   PHPSESSID is no longer listed.

B) Session fixation: copy a pre-login PHPSESSID, log in, copy the
   new PHPSESSID. The two values are different (session was rotated).

C) Hijack from another browser: try the attack from the Vulnerable
   README. In this build PHP rejects the unknown id (use_strict_mode)
   or, if the id is valid but the browser/IP differ, the fingerprint
   check destroys the session and redirects to login.php?hijack=1 .

D) SQL injection on login: type   admin' OR '1'='1   into the
   username field. The prepared statement treats it as a literal
   string and the login is rejected.

E) Stored XSS in product description: try
       <script>alert(1)</script>
   in the description field. It is stored and rendered as plain text
   because of htmlspecialchars().
