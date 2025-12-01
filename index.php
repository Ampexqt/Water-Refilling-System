<?php
require_once __DIR__ . '/config/constants.php';

// Redirect to login page
header('Location: ' . BASE_URL . '/auth/login.php');
exit;
