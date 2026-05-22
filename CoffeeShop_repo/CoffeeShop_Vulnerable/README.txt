SaudiCoffeeShop — Vulnerable build
==================================

This build is the BEFORE state used in Section 6 of the report.
It is deliberately weak against Weak Session IDs (CWE-384 / CWE-330).

What is missing on purpose
--------------------------
- No HttpOnly / Secure / SameSite flags on the PHPSESSID cookie
  (PHP defaults are used as-is).
- No session_regenerate_id() after a successful login,
  so the pre-login session id stays valid afterwards.
- No use_strict_mode, so PHP accepts any PHPSESSID sent by the client.
- No idle / absolute timeout, so a captured id stays valid for hours.
- No fingerprint binding (User-Agent / IP).

What is still secure
--------------------
The project requires these regardless of the chosen vulnerability,
so they are present in both builds:
- bcrypt password hashing (password_hash / password_verify).
- MySQLi prepared statements on every query.
- Client-side validation (HTML5 + small JS) + server-side validation
  and sanitization (filter_var, htmlspecialchars, whitelist checks).

How to run on XAMPP (Windows)
-----------------------------
1. Copy this folder into  C:\xampp\htdocs\SaudiCoffeeShop_Vulnerable
2. Start Apache and MySQL from the XAMPP control panel.
3. Open http://localhost/phpmyadmin , create a database
   named  coffeeshop_db , and import  coffeeshop_db.sql .
4. Open http://localhost/SaudiCoffeeShop_Vulnerable/login.php
5. Default accounts (from the SQL dump):
       admin / admin123
       user1 / user123
       sara11 / sara123
   (If these passwords no longer work, re-register from register.php
    or update the hashes manually.)

How to demonstrate the session-hijacking attack
-----------------------------------------------
1. Log in as admin in browser A.
2. Open DevTools (F12) -> Console, run:   document.cookie
   Copy the PHPSESSID value.
3. In browser B (a different browser, or Incognito), open the site,
   then in its Console run:
       document.cookie = "PHPSESSID=<paste value>; path=/";
4. Visit  admin.php  in browser B. The admin panel loads without a login.

Switch to the hardened build to verify the same attack now fails.
