<?php
/*
 * session_bootstrap.php
 * ---------------------
 * Hardened session settings. Included by every page that uses sessions,
 * BEFORE any call to session_start(). It addresses the conditions that
 * make Weak Session IDs exploitable (see report Section 7.3):
 *
 *   - HttpOnly      -> JavaScript cannot read PHPSESSID from document.cookie
 *   - Secure        -> the cookie is only sent over HTTPS
 *   - SameSite      -> the cookie is not sent on cross-site requests
 *   - use_strict_mode  -> PHP refuses session ids it did not issue
 *   - use_only_cookies -> PHP never reads the id from the URL
 *   - gc_maxlifetime + idle check -> 15-minute inactivity timeout
 *   - session_regenerate_id(true) on every privilege change
 *   - fingerprint binding to (User-Agent + Remote IP)
 */

// Apply settings BEFORE session_start().
ini_set('session.use_strict_mode',   '1');
ini_set('session.use_only_cookies',  '1');
ini_set('session.cookie_httponly',   '1');
// Set 'session.cookie_secure' to '1' when running over HTTPS.
// On localhost/XAMPP without TLS we leave it at '0' so the demo still works.
ini_set('session.cookie_secure',     '0');
ini_set('session.cookie_samesite',   'Strict');
ini_set('session.gc_maxlifetime',    '900');     // 15 minutes

// Idle-timeout window in seconds (kept in sync with gc_maxlifetime).
if (!defined('SESSION_IDLE_LIMIT')) {
    define('SESSION_IDLE_LIMIT', 900);
}

session_start();

// --- Idle timeout -----------------------------------------------------
if (isset($_SESSION['last_activity']) &&
    (time() - $_SESSION['last_activity'] > SESSION_IDLE_LIMIT)) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit();
}
$_SESSION['last_activity'] = time();

// --- Fingerprint binding ---------------------------------------------
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$ip = $_SERVER['REMOTE_ADDR']     ?? '';
$fingerprint = hash('sha256', $ua . '|' . $ip);

if (!empty($_SESSION['logged_in'])) {
    if (!isset($_SESSION['fp'])) {
        $_SESSION['fp'] = $fingerprint;
    } elseif (!hash_equals($_SESSION['fp'], $fingerprint)) {
        // Same PHPSESSID arriving from a different browser / IP:
        // treat as a hijack attempt and kill the session.
        session_unset();
        session_destroy();
        header('Location: login.php?hijack=1');
        exit();
    }
}
