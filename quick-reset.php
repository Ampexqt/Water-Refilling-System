<?php
// Quick Password Reset Script
require_once __DIR__ . '/config/database.php';

echo "<h1>Password Reset Tool</h1>";
echo "<hr>";

$conn = getDBConnection();

// Reset Admin Password
$adminEmail = 'admin@aquaflow.com';
$adminPassword = 'password123';
$adminHash = password_hash($adminPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $adminHash, $adminEmail);

if ($stmt->execute()) {
    echo "<p style='color: green;'>✅ Admin password reset successfully!</p>";
    echo "<p>Email: <strong>admin@aquaflow.com</strong></p>";
    echo "<p>Password: <strong>password123</strong></p>";
} else {
    echo "<p style='color: red;'>❌ Failed to reset admin password</p>";
}

echo "<hr>";

// Reset Cashier Password
$cashierEmail = 'cashier@aquaflow.com';
$cashierPassword = 'password123';
$cashierHash = password_hash($cashierPassword, PASSWORD_DEFAULT);

$stmt2 = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt2->bind_param("ss", $cashierHash, $cashierEmail);

if ($stmt2->execute()) {
    echo "<p style='color: green;'>✅ Cashier password reset successfully!</p>";
    echo "<p>Email: <strong>cashier@aquaflow.com</strong></p>";
    echo "<p>Password: <strong>password123</strong></p>";
} else {
    echo "<p style='color: red;'>❌ Failed to reset cashier password</p>";
}

echo "<hr>";

// Verify the passwords
echo "<h2>Verification</h2>";

$verifyStmt = $conn->prepare("SELECT email, password FROM users WHERE email IN ('admin@aquaflow.com', 'cashier@aquaflow.com')");
$verifyStmt->execute();
$result = $verifyStmt->get_result();

while ($user = $result->fetch_assoc()) {
    $testPassword = 'password123';
    $isValid = password_verify($testPassword, $user['password']);

    echo "<p><strong>" . $user['email'] . ":</strong> ";
    if ($isValid) {
        echo "<span style='color: green;'>✅ Password 'password123' works!</span>";
    } else {
        echo "<span style='color: red;'>❌ Password verification failed!</span>";
    }
    echo "</p>";
}

$stmt->close();
$stmt2->close();
$verifyStmt->close();
$conn->close();

echo "<hr>";
echo "<p><a href='auth/login.php' style='padding: 10px 20px; background: #7FA489; color: white; text-decoration: none; border-radius: 5px; display: inline-block;'>Go to Login Page</a></p>";
