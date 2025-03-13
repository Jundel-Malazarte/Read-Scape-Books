<?php
@include 'db_connect.php';
session_start();

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['pass'];

    // Check if user exists
    $select = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);

        // Verify hashed password
        if (password_verify($pass, $row["pass"])) {
            $_SESSION["login"] = true;
            $_SESSION["id"] = $row["id"];
            $_SESSION["fname"] = $row["fname"];

            // Set cookies if "Remember Me" is checked
            if (isset($_POST['remember'])) {
                setcookie('email', $email, time() + (86400 * 30), "/"); // 30 days
                setcookie('pass', $pass, time() + (86400 * 30), "/"); // 30 days
            } else {
                // Clear cookies if "Remember Me" is not checked
                setcookie('email', '', time() - 3600, "/");
                setcookie('pass', '', time() - 3600, "/");
            }

            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Incorrect email or password!');</script>";
        }
    } else {
        echo "<script>alert('User not found!');</script>";
    }
}

// Retrieve email and password from cookies if they exist
$email = isset($_COOKIE['email']) ? $_COOKIE['email'] : '';
$pass = isset($_COOKIE['pass']) ? $_COOKIE['pass'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="icon" href="./images/Readscape.png">
    <style>

    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100&display=swap');
        /** Google fonts */
        .poppins-thin {
                font-family: "Poppins", sans-serif;
                font-weight: 100;
                font-style: normal;
            }
        body {
            margin: 0;
            background-color: #cfd8dc;
            position: relative;
            display: flex;
            justify-content: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }

        #container {
            background-color: white;
            border-radius: 20px;
            width: 450px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        #form-box {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        #logo {
            padding: 5px;
            border-radius: 50%;
        }

        .input-text {
            width: 100%;
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .input-text input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        /* Added styles for Remember Me and Forgot Password */
        .options {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            margin-bottom: 15px;
            margin-left: 70px;
            margin-right: 70px;
        }

        .remember-me {
            display: flex;
            color: #212121;
            font-size: 0.9rem;
            align-items: center;
            justify-content: start;
            cursor: pointer;
            gap: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-left: 10px;
        }

        .forgot-password {
            font-size: 0.9rem;
            padding-right: 10px;
        }

        .forgot-password a {
            color: #212121;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        #button-submit {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        #button-login #submit {
            width: calc(150% - 20px);
            padding: 10px;
            background-color: #212121;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        #button-login #submit:hover {
            background-color: #212121;
            opacity: 0.8;
        }

        /* havent acc*/
        .signup a {
            padding: 10px;
            color: #212121;
            text-decoration: none;
        }

        /* switch to admin */
        .admin a {
            padding: 10px;
            margin-top: 5px;
            color: #3498db;
            text-decoration: none;        
        }

    </style>
</head>
<body>
    <!-- Container Box -->
    <div id="container">
        <form id="form-box" action="" method="post" autocomplete="off">
        <strong><h1>Login Form</h1></strong>
            <img src="./images/Readscape.png" alt="readscape" id="logo" width="200px" height="200px">            
            <div class="input-text">
                <input type="text" id="email" name="email" placeholder="Email" value="<?php echo $email; ?>" required/><br />
                <input type="password" id="pass" name="pass" placeholder="Password" value="<?php echo $pass; ?>" required/><br />
                <!-- Remember Me and Forgot Password -->
                <div class="options">
                    <div class="remember-me">
                        <input type="checkbox" id="checkbox" name="remember" <?php if ($email && $pass) echo 'checked'; ?> />
                        <label for="checkbox">Remember Me</label>
                    </div>
                    <div class="forgot-password">
                        <a href="forgot_password.php">Forgot Password?</a>
                    </div>
                </div>
                <div id="button-login">
                    <input type="submit" id="submit" name="submit" value="Login" />
                </div>
            </div>
            <div class="signup">
                <a href="index.php">Haven't account yet? Sign up</a>
            </div>
            <div class="admin">
                <a href="./admin/admin.php">Switch to admin</a>
            </div>
        </form>
    </div>
</body>
</html>