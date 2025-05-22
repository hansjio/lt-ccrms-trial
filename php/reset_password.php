<?php
require '../config.php';
date_default_timezone_set('Asia/Manila');

$conn->query("SET time_zone = '+08:00';");

$token = $_GET['token'] ?? '';

if (!$token) {
    die("Invalid token.");
}

$stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("This link is invalid or has expired.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        die("Passwords do not match.");
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $email = $result->fetch_assoc()['email'];

    $stmt = $conn->prepare("UPDATE accounts SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    echo "<div style='text-align:center;margin-top:50px;font-family:Poppins,sans-serif;'>
            <h2>Password has been reset successfully!</h2>
            <a href='../authorization.php' style='color:rgb(8,7,106);font-weight:bold;text-decoration:none;'>Login here</a>
          </div>";
    exit;
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to bottom right, rgb(8, 7, 106), #2c3e50);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: #ffffff;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            margin-bottom: 25px;
            color: #333;
            font-weight: 600;
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="password"]:focus {
            outline: none;
            border-color: rgb(8, 7, 106);
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            background-color: rgb(8, 7, 106);
            color: #fff;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s;
        }

        button:hover {
            background-color: #1a5dd1;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Your Password</h2>
        <form method="POST">
            <label for="password">New Password:</label>
            <input type="password" name="password" id="password" required autocomplete="off">

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required autocomplete="off">

            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
