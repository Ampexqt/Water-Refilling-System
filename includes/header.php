<?php
if (!function_exists('renderHeader')) {
    function renderHeader($title = '')
    {
        $pageTitle = $title ? $title . ' - ' . APP_NAME : APP_NAME;
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo htmlspecialchars($pageTitle); ?></title>
            <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/reset.css">
            <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/variables.css">
            <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/components.css">
            <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
        </head>

        <body>
    <?php
    }
}
    ?>