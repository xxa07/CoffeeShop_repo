# Screenshots

Numbered to match the figures used in the project report.

| File | What it shows | Build |
|------|---------------|-------|
| `figures/fig01_login.png` | CoffeeShop login page (admin credentials about to be submitted) | Either |
| `figures/fig02_drinks_menu.png` | Public Drinks menu rendered from the `products` table | Either |
| `figures/fig03_food_menu.png` | Public Food menu rendered from the `products` table | Either |
| `figures/fig04_admin_products.png` | Admin Panel: current products list with Delete actions | Either |
| `figures/fig05_admin_orders.png` | Admin Panel: Customer Orders table populated from `orders` | Either |
| `figures/fig06_add_product_form.png` | The "Add New Product" form (page that sends data to the DB) | Either |
| `figures/fig07_user_view.png` | A normal user's view of the menu after logging in | Either |
| `figures/fig08_bcrypt_hashes.png` | phpMyAdmin showing the `users` table — passwords stored as bcrypt hashes only | Either |
| `figures/fig09_vuln_document_cookie.png` | `document.cookie` in the browser console exposes `PHPSESSID` (no HttpOnly) | **Vulnerable** |
| `figures/fig10_vuln_hijack_success.png` | Admin panel reached in a second browser using the captured `PHPSESSID` | **Vulnerable** |
| `figures/fig11_hardened_cookie_empty.png` | After the fix: `document.cookie` no longer exposes `PHPSESSID` | **Hardened** *(pending)* |
| `figures/fig12_hardened_hijack_failed.png` | The same hijack attempt now redirects to `login.php?hijack=1` | **Hardened** *(pending)* |
| `figures/fig13_hardened_sqli_blocked.png` | `admin' OR '1'='1` rejected by prepared statement | **Hardened** *(pending)* |
| `figures/fig14_hardened_xss_escaped.png` | `<script>alert('xss')</script>` rendered as literal text (htmlspecialchars) | **Hardened** *(pending)* |

The raw, unrenamed exports are kept in `raw_originals/` for traceability.

## Pending screenshots

`fig11`–`fig14` are produced by deploying `SaudiCoffeeShop_Hardened/` on the same
Ubuntu LAMP host (`192.168.1.52`) and following the procedure in
`docs/Hardened_Screenshots_Instructions.docx`.
