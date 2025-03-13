<?php
session_start();
@include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $newpwd = $_POST['newpwd'];

    if ($code == $_SESSION['reset_code']) {
        $email = $_SESSION['reset_email'];
        // Update password in the database (pseudo-code, replace with actual update logic)
        $hashedPwd = password_hash($newpwd, PASSWORD_DEFAULT);
        $update = "UPDATE users SET pass='$hashedPwd' WHERE email='$email'";
        mysqli_query($conn, $update);

        echo "<script>alert('Password has been reset successfully.');</script>";
        header("Location: sign-in.php");
        exit();
    } else {
        echo "<script>alert('Invalid code.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100&display=swap" rel="stylesheet">
    <link rel="icon" href="./images/Readscape.png" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eceff1;
        }
        .container {
            background: white;
            padding: 30px;
            margin-top: 90px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: left;
            margin: 90px auto 0;
        }
        h1 {
            margin-bottom: 20px;
            font-size: 22px;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 6px;
            background: #1e88e5;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="font-size: 24px; font-weight: bold;">Reset Password</h1>
        <form action="" method="post">       
            <label>Enter the code sent to your email to reset password.</label>
            <input type="text" id="code" name="code" maxlength="6" required>
            <div id="code-verified" style="display: none; color: green;">
            <i class="fas fa-check-circle"></i> Code Verified
            </div>
            <hr style="margin: 10px 0px;">
            <label for="newpwd">New Password</label>
            <div style="position: relative;">
                <input type="password" id="newpwd" name="newpwd" required oninput="validatePassword()">
                <i class="fas fa-eye" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
            </div>
            <div id="uppercase-check" style="color: red;">
                <i class="fas fa-times-circle"></i> At least 1 uppercase letter
            </div>
            <div id="length-check" style="color: red;">
                <i class="fas fa-times-circle"></i> Minimum 8 characters
            </div>
            <div id="special-check" style="color: red;">
                <i class="fas fa-times-circle"></i> At least 1 special character
            </div>   
            <div id="number-check" style="color: red;">
                <i class="fas fa-times-circle"></i> At least 1 number
            </div>
            <button type="submit" class="btn">Reset Password</button>
        </form>
    </div>
    <script>
        function validatePassword() {
            const password = document.getElementById('newpwd').value;
            
            const uppercaseCheck = document.getElementById('uppercase-check');
            if (password.match(/[A-Z]/)) {
                uppercaseCheck.style.color = 'green';
                uppercaseCheck.innerHTML = '<i class="fas fa-check-circle"></i> At least 1 uppercase letter';
            } else {
                uppercaseCheck.style.color = 'red';
                uppercaseCheck.innerHTML = '<i class="fas fa-times-circle"></i> At least 1 uppercase letter';
            }

            const lengthCheck = document.getElementById('length-check');
            if (password.length >= 8) {
                lengthCheck.style.color = 'green';
                lengthCheck.innerHTML = '<i class="fas fa-check-circle"></i> Minimum 8 characters';
            } else {
                lengthCheck.style.color = 'red';
                lengthCheck.innerHTML = '<i class="fas fa-times-circle"></i> Minimum 8 characters';
            }
            const specialCheck = document.getElementById('special-check');
            if (password.match(/[!@#$%^&*?"{}|<>]/)) {
                specialCheck.style.color = 'green';
                specialCheck.innerHTML = '<i class="fas fa-check-circle"></i> At least 1 special character';
            } else {
                specialCheck.style.color = 'red';
                specialCheck.innerHTML = '<i class="fas fa-times-circle"></i> At least 1 special character';
            }
            const numberCheck = document.getElementById('number-check');
            if (password.match(/[0-9]/)) {
                numberCheck.style.color = 'green';
                numberCheck.innerHTML = '<i class="fas fa-check-circle"></i> At least 1 number';
            } else {
                numberCheck.style.color = 'red';
                numberCheck.innerHTML = '<i class="fas fa-times-circle"></i> At least 1 number';
            }

            document.getElementById('togglePassword').addEventListener('click', function (e) {
                const password = document.getElementById('newpwd');
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });
        }

        // Prompt for the code when the page loads
        window.onload = function() {
            var code = prompt('Enter the 6-digit code sent to your email:');
            if (code) {
                document.getElementById('code').value = code;
            }
        };
    </script>
</body>
</html>