<?php
// Load constants first
require_once __DIR__ . '/../config/constants.php';

session_start();

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit;
    }
}

function requireRole($role)
{
    requireLogin();
    if ($_SESSION['user_role'] !== $role) {
        header('Location: ' . BASE_URL . '/' . $_SESSION['user_role'] . '/index.php');
        exit;
    }
}

function getUserRole()
{
    return $_SESSION['user_role'] ?? null;
}

function getUserId()
{
    return $_SESSION['user_id'] ?? null;
}

function getUserName()
{
    return $_SESSION['user_name'] ?? null;
}

function setUserSession($user)
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
}

function destroyUserSession()
{
    session_unset();
    session_destroy();
}
