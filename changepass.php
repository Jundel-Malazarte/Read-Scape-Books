<?php
ob_start(); // Start output buffering
session_start();
@include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    header("Location: sign-in.php");
    exit(); // Stop further execution
}

$user_id = $_SESSION['id'];

$sql = "SELECT fname, lname, profile_image FROM `users` WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $fname = htmlspecialchars($row['fname']);
    $lname = htmlspecialchars($row['lname']);
    $profile_image = htmlspecialchars($row['profile_image']);
} else {
    echo "User not found.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
ob_end_flush(); // End buffering
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100&display=swap" rel="stylesheet">
    <link rel="icon" href="./images/Readscape.png">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #212529;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
        }

        .navbar .fw-bold {
            font-size: 1.2rem;
        }

        /* Sidenav Styles */
        .sidenav {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1100;
            top: 0;
            left: 0;
            background-color: #212529;
            overflow-x: hidden;
            transition: 0.3s;
            padding-top: 60px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, .2);
        }

        .sidenav a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 18px;
            color: #f8f9fa;
            display: block;
            transition: 0.3s;
        }

        .sidenav a:hover {
            background-color: #343a40;
            color: #fff;
        }

        .sidenav .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }

        /* Password Change Form */
        .password-container {
            max-width: 500px;
            margin: 80px auto;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .15);
        }

        .password-container h1 {
            color: #212529;
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .form-control {
            padding: 0.75rem;
            border-radius: 8px;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, .25);
        }

        .btn-update {
            width: 100%;
            padding: 0.75rem;
            font-size: 1.1rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(13, 110, 253, .15);
        }

        .alert {
            border-radius: 8px;
            font-weight: 500;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <span class="navbar-toggler-icon" onclick="openNav()" style="cursor: pointer; margin-right: 1rem;"></span>
                <img src="./images/Readscape.png" alt="ReadScape" class="rounded-circle" width="40" height="40">
                <span class="ms-2 text-white fw-bold">ReadScape</span>
            </div>
            <div class="d-flex align-items-center">
                <div class="position-relative me-3">
                    <a href="cart.php" class="btn btn-outline-light">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge" id="cart-counter">0</span>
                    </a>
                </div>
                <div class="d-flex align-items-center">
                    <img src="<?php echo $profile_image; ?>" alt="Profile" class="rounded-circle me-2" width="40" height="40">
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown">
                            <?php echo $fname . " " . $lname; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><a class="dropdown-item" href="order.php">My Orders</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidenav -->
    <div id="Sidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="dashboard.php"><i class="fas fa-home me-2"></i>Home</a>
        <a href="profile.php"><i class="fas fa-user me-2"></i>Profile</a>
        <a href="changepass.php"><i class="fas fa-key me-2"></i>Change password</a>
        <a href="cart.php"><i class="fas fa-shopping-cart me-2"></i>Cart</a>
        <a href="order.php"><i class="fas fa-shopping-bag me-2"></i>My Orders</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Log Out</a>
    </div>

    <!-- Password Change Form -->
    <div class="password-container">
        <h1><i class="fas fa-key me-2"></i>Change Password</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label for="oldpwd" class="form-label">Current Password</label>
                <div style="position: relative;">
                    <input type="password" class="form-control" id="oldpwd" name="oldpwd" required oninput="validateOldPassword()">
                    <i class="fas fa-eye" id="toggleOldPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                </div>
                <script>
                    function validateOldPassword() {
                        const oldPassword = document.getElementById('oldpwd').value;
                        const oldPasswordCheck = document.getElementById('oldpassword-check');
                        fetch('validate_old_password.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `oldpwd=${oldPassword}`
                        })
                        .then(response => response.text())
                        .then(isValid => {
                            if (isValid === 'true') {
                                oldPasswordCheck.style.color = 'green';
                                oldPasswordCheck.innerHTML = '<i class="fas fa-check-circle"></i> Password correct';
                            } else {
                                oldPasswordCheck.style.color = 'red';
                                oldPasswordCheck.innerHTML = '<i class="fas fa-times-circle"></i> Password is incorrect';
                            }
                        })
                        .catch(error => console.error('Error:', error));
                    }
                </script>
            </div>
            <div class="mb-3">
                <label for="newpwd" class="form-label">New Password</label>
                <div style="position: relative;">
                    <input type="password" class="form-control" id="newpwd" name="newpwd" required oninput="validatePassword()">
                    <i class="fas fa-eye" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                </div>
            </div>
            <div class="mb-3">
                <label for="conpwd" class="form-label">Confirm New Password</label>
                <div style="position: relative;">
                    <input type="password" class="form-control" id="conpwd" name="conpwd" required>
                    <i class="fas fa-eye" id="toggleConfirmPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                </div>
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
            <hr>
            <button type="submit" class="btn btn-primary btn-update">
                <i class="fas fa-save me-2"></i>Update Password
            </button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            @include 'db_connect.php';

            $old_password = $_POST['oldpwd'];
            $new_password = $_POST['newpwd'];
            $confirm_password = $_POST['conpwd'];

            if ($new_password !== $confirm_password) {
                echo "<p style='color: red;'>New passwords do not match.</p>";
            } else {
                $sql = "SELECT pass FROM `users` WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($row = mysqli_fetch_assoc($result)) {
                    if (password_verify($old_password, $row['pass'])) {
                        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                        $sql = "UPDATE `users` SET pass = ? WHERE id = ?";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "si", $new_password_hashed, $user_id);
                        if (mysqli_stmt_execute($stmt)) {
                            echo "<script>alert('Password Updated Successfully!'); window.location.href='dashboard.php';</script>";
                        } else {
                            echo "<p style='color: red;'>Error updating password.</p>";
                        }
                    } else {
                        echo "<p style='color: red;'>Old password is incorrect.</p>";
                    }
                } else {
                    echo "<p style='color: red;'>User not found.</p>";
                }

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
            }
        }
        ?>
    </div>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openNav() {
            document.getElementById("Sidenav").style.width = "240px";
        }

        function closeNav() {
            document.getElementById("Sidenav").style.width = "0";
        }

        // Cart counter update
        function updateCartCounter() {
            fetch('cart_counter.php')
                .then(response => response.text())
                .then(count => {
                    document.getElementById("cart-counter").innerText = count;
                })
                .catch(error => console.error('Error:', error));
        }

        document.addEventListener("DOMContentLoaded", updateCartCounter);

        // for password validation
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
        }

        // Toggle password visibility
        document.getElementById('toggleOldPassword').addEventListener('click', function () {
            const oldPassword = document.getElementById('oldpwd');
            const type = oldPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            oldPassword.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        document.getElementById('togglePassword').addEventListener('click', function () {
            const newPassword = document.getElementById('newpwd');
            const type = newPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            newPassword.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
            const confirmPassword = document.getElementById('conpwd');
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>