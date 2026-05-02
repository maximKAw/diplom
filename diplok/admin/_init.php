<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('prof_admin');
    session_start();
}
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/functions.php';

function admin_require_login(): void
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

function admin_csrf_token(): string
{
    if (empty($_SESSION['admin_csrf'])) {
        $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf'];
}

function admin_verify_csrf(): bool
{
    return isset($_POST['csrf'])
        && isset($_SESSION['admin_csrf'])
        && hash_equals($_SESSION['admin_csrf'], (string) $_POST['csrf']);
}

function admin_user(): ?array
{
    if (empty($_SESSION['admin_id']) || empty($_SESSION['admin_username'])) {
        return null;
    }
    return [
        'id' => (int) $_SESSION['admin_id'],
        'username' => $_SESSION['admin_username'],
    ];
}
