<?php
// Application Constants
define('APP_NAME', 'AquaFlow');
define('APP_DESCRIPTION', 'Water Refilling System');
define('APP_VERSION', '1.0.0');

// Base URL - Adjust according to your setup
define('BASE_URL', '/Water-Refilling-System');

// Container Sizes and Prices
define('CONTAINER_PRICES', [
    '5-gallon' => 25.00,
    '3-gallon' => 15.00,
    '1-gallon' => 8.00
]);

// Tax Rate (12%)
define('TAX_RATE', 0.12);

// Date/Time Formats
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'M d, Y');
define('DISPLAY_TIME_FORMAT', 'h:i A');

// Pagination
define('ITEMS_PER_PAGE', 10);
