<?php 
@include 'db_connect.php';

if (isset($_POST["submit"])) {
    $fname = trim($_POST["fname"]);
    $lname = trim($_POST["lname"]);
    $email = trim($_POST["email"]);
    $pass = $_POST["pass"];
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);

    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $profile_image = $target_dir . basename($_FILES["profile_image"]["name"]);
    $imageFileType = strtolower(pathinfo($profile_image, PATHINFO_EXTENSION));

    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $profile_image)) {
        // File successfully uploaded
    } else {
        echo "<script>alert('Error uploading image.');</script>";
        $profile_image = "default.jpg"; // Set default if upload fails
    }

    // Check if email already exists
    $check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        echo "<script>alert('Email already exists! Please use a different email.');</script>";
    } else {
        // Hash the password before storing it
        $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);

        // Insert user into the database
        $stmt = $conn->prepare("INSERT INTO users (fname, lname, email, pass, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
        
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("ssssss", $fname, $lname, $email, $hashed_pass, $phone, $address);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href='sign-in.php';</script>";
        } else {
            die("Error executing query: " . $stmt->error);
        }
    }
    $check_email->close();
    $stmt->close();
    $conn->close();
}
?>
   


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Sign up</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<style>
    body {
    margin: auto;
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

.input-text {
    width: 85%;
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
    
#button-submit {
    width: 100%;
    display: flex;
    justify-content: center;
}

#button-add #sign-up {
    width: calc(150% - 20px);
    padding: 10px;
    background-color: #212121;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
}

#button-add #sign-up:hover {
    background-color: #212121;
    opacity: 0.8;
}
.sign-in a{
    margin-top: 10px;
    color: #212121;
    text-decoration: none;
}

</style>
    <body>
    
    <!-- Container box -->
        <div id="container">
            <form action="" id="form-box" method="post">
                <h1>Users Registration</h1>
                <label for="profile_image"><strong>Profile Picture</strong></label>
                    <input type="file" id="profile_image" name="profile_image" accept="upload/*" onchange="previewImage(event)" required><br>
                    <img id="image_preview" src="./upload/default.jpg" alt="Profile Preview" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; display: none;">
                <div class="input-text">
                    
                    <input type="text"  id="fname" name="fname" placeholder="First Name" required><br>
                    <input type="text"  id="lname" name="lname" placeholder="Last Name" required><br>
                    <input type="email" id="email" name="email" placeholder="Email" required><br>
                    <input type="password" id="pass" name="pass" placeholder="Password" required><br>
                    <input type="text" id="phone" name="phone" placeholder="Phone" required><br>
                    <input type="text" id="address" name="address" placeholder="Address" required><br>       
                    <div id="button-add">
                            <input type="submit" id="sign-up" name="submit" value="Sign up">                         
                    </div>
                    <div class="sign-in">
                            <a href="./sign-in.php">Have already an account? Sign in</a>
                    </div>   
                </form>
        </div>       
    </body>
</html>