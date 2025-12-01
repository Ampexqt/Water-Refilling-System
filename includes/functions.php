<?php
// Helper Functions

function sanitizeInput($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatDate($date, $format = DISPLAY_DATE_FORMAT)
{
    if (empty($date)) return '-';
    return date($format, strtotime($date));
}

function formatTime($time, $format = DISPLAY_TIME_FORMAT)
{
    if (empty($time)) return '-';
    return date($format, strtotime($time));
}

function formatDateTime($datetime, $format = null)
{
    if (empty($datetime)) return '-';
    if ($format === null) {
        $format = DISPLAY_DATE_FORMAT . ' ' . DISPLAY_TIME_FORMAT;
    }
    return date($format, strtotime($datetime));
}

function getStatusBadgeClass($status)
{
    $classes = [
        'active' => 'badge-success',
        'inactive' => 'badge-error',
        'pending' => 'badge-warning',
        'processing' => 'badge-info',
        'completed' => 'badge-success',
        'cancelled' => 'badge-error',
        'in-transit' => 'badge-info'
    ];

    return $classes[$status] ?? 'badge-info';
}

function formatContainerSize($size)
{
    return ucwords(str_replace('-', ' ', $size));
}

function calculatePrice($containerSize, $quantity)
{
    $prices = CONTAINER_PRICES;
    return isset($prices[$containerSize]) ? $prices[$containerSize] * $quantity : 0;
}

function calculateTax($amount)
{
    return $amount * TAX_RATE;
}

function calculateTotal($subtotal, $tax = null)
{
    if ($tax === null) {
        $tax = calculateTax($subtotal);
    }
    return $subtotal + $tax;
}

function formatCurrency($amount)
{
    return '₱' . number_format($amount, 2);
}

function generateCSRFToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function renderFooter()
{
    require_once __DIR__ . '/footer.php';
}
