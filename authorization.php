<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authorization</title>
</head>

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

img {
    width: 5em;
    height: 5em;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.badac-container {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    max-width: 900px;
    padding: 10px;
}

.badac-card {
    display: flex;
    flex-direction: row;
    width: 100%;
    background-color: rgb(255, 255, 255);
    border-radius: 10px;
    box-shadow: 0px 0px 40px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

.card-left {
    width: 50%;
    overflow: hidden;
}

.card-left .image-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.card-right {
    width: 50%;
    padding: 50px 72px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.authorization-required {
    margin-top: 15px;
    margin-bottom: 15px;
    font-size: 20px;
    color: rgb(255, 0, 30);
    text-align: center;
}

.input-group {
    display: flex;
    flex-direction: column;
    gap: 7px;
    position: relative;
    color: rgb(139, 138, 142);
    margin-bottom: 20px;
}

.input-group .label {
    font-size: 12px;
    font-weight: 600;
    padding-left: 10px;
    position: absolute;
    top: 16px;
    transition: 0.3s;
    pointer-events: none;
}

.input-box {
    width: 100%;
    height: 45px;
    border: none;
    outline: none;
    padding: 0px 7px;
    border-radius: 6px;
    color: rgb(18, 8, 171);
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 16px;
    background-color: transparent;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    border: 1px solid black;
}

.input-box:focus {
    border: 2px solid transparent;
    color: rgb(7, 50, 178);
}

.input-group .input-box:valid ~ .label,
.input-group .input-box:focus ~ .label {
    transition: 0.3s;
    padding-left: 2px;
    transform: translateY(-35px);
}

.input-group .input-box:valid,
.input-group .input-box:focus {
    border: 1px solid black;
}

.input-box[type="password"] {
    -webkit-text-security: disc;
}

.actions {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 20px;
}

.actions .enter-pw {
    padding: 10px 20px;
    background-color: rgb(23, 3, 96);
    color: rgb(211, 209, 230);
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    transition: background-color 0.3s ease, transform 0.3s ease;
    width: 100%;
}

.actions .enter-pw:hover {
    background-color:rgb(198, 216, 238);
    color: rgb(23, 3, 180);
}

@media (max-width: 768px) {
    .badac-card {
        flex-direction: column;
    }

    .card-left {
        display: none;
    }

    .card-right {
        width: 100%;
        padding: 30px 72px;
    }

    .authorization-required {
        font-size: 28px;
    }

    .input-box {
        padding: 8px;
    }

    .actions .enter-pw {
        padding: 10px;
        font-size: 14px;
    }
}
</style>
<body>
    <div class="badac-container">
        <div class="badac-card">
            <div class="card-left">
                <div class="image-cover">
                    <img src="login.png" alt="LT System">
                </div>
            </div>

            <div class="card-right">

                <img src="key.png" alt="Authorization Symbol">
                <h1 class="authorization-required"><a style="color:rgb(109, 66, 10);">Authorization</a> Required</h1><br>

                <form method="POST" action="php/login.php">

                    <div class="input-group">
                        <input type="text" name="username" class="input-box" required autocomplete="off"> 
                        <label class="label">USERNAME</label>
                    </div>

                    <div class="input-group">
                        <input type="password" name="password" class="input-box" required autocomplete="off">
                        <label class="label">PASSWORD</label>
                    </div>
                    <div style="margin-top: -7px; margin-bottom: 6px; font-size: 13px; text-align: center;">
                    <a href="php/forgot_password.php">Forgot Password?</a>
 
                    </div>

                    <div class="actions">
                        <button type="submit" class="enter-pw">ENTER</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
<script>
    alert("Invalid Login. Please try again.");
</script>
<?php endif; ?>
</body>

</html>
