<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
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
            overflow: hidden;
        }

        .container {
            background: #ffffff;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: floatUp 0.6s ease-out;
        }

        @keyframes floatUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon {
            font-size: 48px;
            color: rgb(8, 7, 106);
            margin-bottom: 15px;
        }

        h2 {
            margin-bottom: 20px;
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

        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="email"]:focus {
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

        .note {
            margin-top: 15px;
            font-size: 14px;
            color: #777;
        }
    </style>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div class="icon">
            <i class="fas fa-lock"></i>
        </div>
        <h2>Forgot Your Password?</h2>
        <form method="POST" action="send_reset_link.php">
            <label for="email">Enter your email address:</label>
            <input type="email" id="email" name="email" required autocomplete="off">
            <button type="submit">Send Reset Link</button>
        </form>
        <div class="note">We’ll send you a link to reset your password.</div>
    </div>
</body>
</html>
