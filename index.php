<?php
@include 'db_connect.php';

if (isset($_POST["submit"])) {
    $fname = trim($_POST["fname"]);
    $lname = trim($_POST["lname"]);
    $email = trim($_POST["email"]);
    $pass = $_POST["pass"];
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);

    // Handle image upload
    $profile_image = "uploads/default.jpg"; // Default profile picture

    if (!empty($_FILES["profile_image"]["name"])) {
        $image_name = basename($_FILES["profile_image"]["name"]);
        $target_dir = "uploads/";
        $target_file = $target_dir . uniqid() . "_" . $image_name;

        // Move file to uploads folder
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = $target_file;
        }
    }

    // Check if email already exists
    $check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    // If email already exists, show an alert
    if ($check_email->num_rows > 0) {
        echo "<script>alert('Email already exists! Please use a different email.');</script>";
    } else {
        // Hash the password before storing it
        $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);

        // Insert user into the database
        $stmt = $conn->prepare("INSERT INTO users (fname, lname, email, pass, phone, address, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("sssssss", $fname, $lname, $email, $hashed_pass, $phone, $address, $profile_image);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href='sign-in.php';</script>";
        } else {
            die("Error executing query: " . $stmt->error);
        }

        $stmt->close(); // Only close if it was initialized
    }

    $check_email->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="./images/Readscape.png">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 450px;
            width: 100%;
            padding: 2rem;
        }

        .form-container {
            background-color: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .preview-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        #image_preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #dee2e6;
            padding: 3px;
            margin-bottom: 1rem;
        }

        .form-control {
            margin-bottom: 1rem;
            padding: 0.75rem;
        }

        .btn-signup {
            background-color: #212121;
            color: white;
            padding: 0.75rem;
            width: 100%;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .btn-signup:hover {
            background-color: #424242;
            color: white;
        }

        .signin-link {
            text-align: center;
        }

        .signin-link a {
            color: #212121;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .signin-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4">User Registration</h2>

            <form action="" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                <div class="preview-container">
                    <img id="image_preview" src="uploads/default.jpg" alt="Profile Preview">
                    <input type="file" class="form-control" id="profile_image" name="profile_image"
                        accept="image/*" onchange="previewImage(event)">
                </div>

                <input type="text" class="form-control" id="fname" name="fname" placeholder="First Name" required>
                <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" required>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                <input type="password" class="form-control" id="pass" name="pass" placeholder="Password" required>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" required>
                <input type="text" class="form-control" id="address" name="address" placeholder="Address" required>

                <button type="submit" name="submit" class="btn btn-signup">Sign up</button>

                <div class="signin-link">
                    <a href="./sign-in.php">Already have an account? Sign in</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(event) {
            var input = event.target;
            var preview = document.getElementById("image_preview");

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = "block"; // Show the preview image
                }

                reader.readAsDataURL(input.files[0]); // Convert image file to base64
            }
        }

        function validateForm() {
            var fname = document.getElementById("fname").value;
            var lname = document.getElementById("lname").value;
            var email = document.getElementById("email").value;
            var pass = document.getElementById("pass").value;
            var phone = document.getElementById("phone").value;
            var address = document.getElementById("address").value;

            // Validate first name and last name (only letters)
            var namePattern = /^[A-Za-z]+$/;
            if (!namePattern.test(fname) || !namePattern.test(lname)) {
                alert("First name and last name can only contain letters.");
                return false;
            }

            // Validate email (must end with @gmail.com, @outlook.com, @yahoo.com, etc.)
            var emailPattern = /^[a-zA-Z0-9._%+-]+@(gmail\.com|outlook\.com|yahoo\.com)$/;
            if (!emailPattern.test(email)) {
                alert("Please enter a valid email address (e.g., example@gmail.com, example@outlook.com, example@yahoo.com).");
                return false;
            }

            // Validate password (must be at least 8 characters and contain at least one symbol)
            var passPattern = /^(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;
            if (!passPattern.test(pass)) {
                alert("Password must be at least 8 characters long and contain at least one symbol.");
                return false;
            }

            // Validate phone number (must be 11 digits and start with 09)
            var phonePattern = /^09\d{9}$/;
            if (!phonePattern.test(phone)) {
                alert("Please enter a valid 11-digit Philippine phone number starting with 09.");
                return false;
            }

            // Validate address (must contain letters and numbers)
            var addressPattern = /^[A-Za-z0-9\s,.-]+$/;
            if (!addressPattern.test(address)) {
                alert("Please enter a valid address (e.g., 123 Main St, City).");
                return false;
            }

            return true;
        }
    </script>
</body>

</html>