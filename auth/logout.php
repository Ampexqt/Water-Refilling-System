<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/session.php';

destroyUserSession();

header('Location: ' . BASE_URL . '/auth/login.php');
exit;
