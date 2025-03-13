<?php
session_start();
@include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $code = rand(100000, 999999);
    $_SESSION['reset_code'] = $code;
    $_SESSION['reset_email'] = $email;

    // Send code to email (pseudo-code, replace with actual email sending logic)
    mail($email, "Password Reset Code", "Your password reset code is: $code");

    echo "<script>
        alert('A 6-digit code has been sent to your email.');
        var code = prompt('Enter the 6-digit code sent to your email:');
        if (code) {
            document.getElementById('code').value = code;
            document.forms[0].submit();
        }
    </script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Reset Password</title>
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
        <h1 style="font-size: 24px; font-weight: bold;">Reset Your Password</h1>
        <form action="" method="post">
            <input type="hidden" id="code" name="code">
            <label>Enter the email associated with your account and we'll send an email with instruction to reset your password</label>
            <input type="email" id="email" name="email" maxlength="50" required>
            <div id="code-verified" style="display: none; color: green;">
            <i class="fas fa-check-circle"></i> Sent code to email
            </div>
            <hr style="margin: 10px 0px;">
            <div style="position: relative;">
            </div>
            <button type="submit" class="btn">Send Code</button>
        </form>
    </div>
</body>
</html> 