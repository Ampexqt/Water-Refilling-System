<?php
// Start session FIRST before any output
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$allTestsPassed = true;
$errors = [];
$warnings = [];

// Handle Actions BEFORE any HTML output
if (isset($_GET['action'])) {
    $conn = @new mysqli('localhost', 'root', '', 'water_refilling_system');

    if ($_GET['action'] == 'create_users') {
        $adminHash = password_hash('password123', PASSWORD_DEFAULT);
        $cashierHash = password_hash('password123', PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password, role, status) VALUES
                ('Admin User', 'admin@aquaflow.com', '$adminHash', 'admin', 'active'),
                ('Cashier User', 'cashier@aquaflow.com', '$cashierHash', 'cashier', 'active')";

        $conn->query($sql);
        $conn->close();
        header('Location: setup.php');
        exit;
    } elseif ($_GET['action'] == 'reset_password') {
        $newHash = password_hash('password123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = 'admin@aquaflow.com'");
        $stmt->bind_param("s", $newHash);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        header('Location: setup.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - AquaFlow Water Refilling System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #7FA489;
            border-bottom: 3px solid #7FA489;
            padding-bottom: 10px;
        }

        h2 {
            color: #6B8F76;
            margin-top: 30px;
        }

        .success {
            background: #dcfce7;
            color: #15803d;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #15803d;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #b91c1c;
        }

        .warning {
            background: #fef3c7;
            color: #a16207;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #a16207;
        }

        .info {
            background: #E6EFE7;
            color: #577A63;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #7FA489;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background: #7FA489;
            color: white;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #7FA489;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px 10px 0;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background: #6B8F76;
        }

        .step {
            margin: 20px 0;
            padding: 20px;
            background: #fafafa;
            border-radius: 5px;
            border-left: 4px solid #7FA489;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🌊 AquaFlow Setup & Diagnostic Tool</h1>
        <p>This tool will help you set up and troubleshoot your Water Refilling System.</p>

        <?php
        // Test 1: Database Connection
        echo "<div class='step'>";
        echo "<h2>Step 1: Database Connection</h2>";
        $conn = @new mysqli('localhost', 'root', '', 'water_refilling_system');

        if ($conn->connect_error) {
            $allTestsPassed = false;
            echo "<div class='error'><strong>❌ Connection Failed:</strong> " . $conn->connect_error . "</div>";
            echo "<p>Make sure WAMP is running and create the 'water_refilling_system' database.</p>";
            echo "</div></div></body></html>";
            exit;
        } else {
            echo "<div class='success'>✅ Database connection successful!</div>";
        }
        echo "</div>";

        // Test 2: Check Users
        echo "<div class='step'>";
        echo "<h2>Step 2: User Accounts</h2>";
        $result = $conn->query("SELECT id, name, email, role, status FROM users");

        if ($result->num_rows == 0) {
            $allTestsPassed = false;
            echo "<div class='error'><strong>❌ No users found!</strong>";
            echo "<form method='GET' action=''><input type='hidden' name='action' value='create_users'>";
            echo "<button type='submit' class='btn'>Create Default Users</button></form></div>";
        } else {
            echo "<div class='success'>✅ Found " . $result->num_rows . " user(s)</div>";
            echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>";
            while ($user = $result->fetch_assoc()) {
                echo "<tr><td>" . $user['id'] . "</td><td>" . htmlspecialchars($user['name']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td><td><strong>" . ucfirst($user['role']) . "</strong></td>";
                echo "<td>" . ucfirst($user['status']) . "</td></tr>";
            }
            echo "</table>";
        }
        echo "</div>";

        // Test 3: Password Verification
        if ($result->num_rows > 0) {
            echo "<div class='step'>";
            echo "<h2>Step 3: Password Verification</h2>";
            $stmt = $conn->prepare("SELECT password FROM users WHERE email = 'admin@aquaflow.com'");
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (password_verify('password123', $user['password'])) {
                    echo "<div class='success'><strong>✅ Password verification successful!</strong>";
                    echo "<p>Login with: admin@aquaflow.com / password123</p></div>";
                } else {
                    $warnings[] = "Password mismatch";
                    echo "<div class='warning'><strong>⚠️ Password verification failed!</strong>";
                    echo "<form method='GET' action=''><input type='hidden' name='action' value='reset_password'>";
                    echo "<button type='submit' class='btn'>Reset Admin Password to 'password123'</button></form></div>";
                }
            }
            echo "</div>";
        }

        // Test 4: Session Check
        echo "<div class='step'>";
        echo "<h2>Step 4: PHP Session</h2>";
        if (session_status() === PHP_SESSION_ACTIVE) {
            echo "<div class='success'>✅ PHP sessions are working!</div>";
        } else {
            $allTestsPassed = false;
            echo "<div class='error'>❌ PHP sessions are not working!</div>";
        }
        echo "</div>";

        // Summary
        echo "<div class='step'>";
        echo "<h2>Summary</h2>";
        if ($allTestsPassed && empty($warnings)) {
            echo "<div class='success'><strong>🎉 All tests passed! Your system is ready to use.</strong></div>";
            echo "<a href='auth/login.php' class='btn'>Go to Login Page</a>";
        } else {
            echo "<p>Please fix the issues above and refresh this page.</p>";
        }
        echo "</div>";

        $conn->close();
        ?>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd;">
            <h3>Additional Tools:</h3>
            <a href="diagnostic.php" class="btn">Run Diagnostic</a>
            <a href="reset-password.php" class="btn">Password Reset Tool</a>
            <a href="auth/login.php" class="btn">Login Page</a>
        </div>
    </div>
</body>

</html>