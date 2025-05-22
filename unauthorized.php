<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: rgb(255, 255, 255);
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 0px 40px rgba(193, 96, 5, 0.912);
        }
        h1 {
            color: rgb(255, 0, 30);
            margin-bottom: 1rem;
        }
        p {
            color: rgb(139, 138, 142);
            margin-bottom: 2rem;
        }
        .back-btn {
            padding: 10px 20px;
            background-color: rgb(218, 102, 0);
            color: rgb(230, 219, 209);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .back-btn:hover {
            background-color: #f4ddc7;
            color: rgb(184, 80, 0);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Unauthorized Access</h1>
        <p>You don't have permission to access this page.</p>
        <a href="index.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html> 