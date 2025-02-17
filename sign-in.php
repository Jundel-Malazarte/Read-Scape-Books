    <?php 
    //     @include 'db_connect.php';

    //     session_start();

    //     if(isset($_POST['submit'])){
    //         $email = mysqli_real_escape_string($conn, $_POST['email']);
    //           $pass = $_POST['pass'];

    //         $select = "SELECT * FROM users WHERE email = '$email' && pass ='$pass' ";  

    //         $result = mysqli_query($conn, $select);

    //         if(mysqli_num_rows($result) > 0){

    //             $row = mysqli_fetch_array($result);

    //     //         if (mysqli_num_rows($result) > 0) {
    //     //           if($pass == $row ["pass"]){
    //     //               $_SESSION["login"] = true;
    //     //               $_SESSION["id"] = $row ["id"];
    //     //               header("Location: dashboard.php");
    //     //           } else {
    //     //               $error[] = 'incorrect email or password!';
    //     //           }
    //     // }

    //     if (password_verify($pass, $row["pass"])) {
    //         $_SESSION["login"] = true;
    //         $_SESSION["id"] = $row["id"];
    //         $_SESSION["fname"] = $row["fname"];

    //         header("Location: dashboard.php");
    //         exit();
    //     } 
        
    //     else {
    //         echo "<script>alert('Incorrect email or password!');</script>";
    //     }
    // } else {
    //     echo "<script>alert('User not found!');</script>";
    // }
    //   }
    

    @include 'db_connect.php';
    session_start();

    if(isset($_POST['submit'])){
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $pass = $_POST['pass'];

        // Check if user exists
        $select = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $select);

        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_array($result);

            // Verify hashed password
            if (password_verify($pass, $row["pass"])) {
                $_SESSION["login"] = true;
                $_SESSION["id"] = $row["id"];
                $_SESSION["fname"] = $row["fname"];

                header("Location: dashboard.php");
                exit();
            } else {
                echo "<script>alert('Incorrect email or password!');</script>";
            }
        } else {
            echo "<script>alert('User not found!');</script>";
        }
    }



    
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Form</title>
    <link rel="stylesheet" href="" />
    <style>
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
      .signup a{
        padding: 10px;
        color: #212121;
        text-decoration: none;
      }

    </style>
  </head>
  <body>
    <!-- Container Box -->
    <div id="container">
      <form id="form-box" action="" method="post" autocomplete="off">
        <h1>Login Form</h1>
          
        <div class="input-text">
          <input type="text" id="email" name="email" placeholder="Email" required/><br />
          <input type="password" id="pass" name="pass" placeholder="Password" required/><br />
          <!-- Remember Me and Forgot Password -->
          <div class="options">
            <div class="remember-me">
              <input type="checkbox" id="checkbox" name="remember" />
              <label for="checkbox">Remember Me</label>
            </div>
            <div class="forgot-password">
              <a href="#">Forgot Password?</a>
            </div>
          </div>
          <div id="button-login">
            <input type="submit" id="submit" name="submit" value="Login" />
          </div>          
        </div>
          <div class="signup">
            <a href="index.php">Haven't account yet? Sign up</a>
          </div>
      </form>
    </div>
  </body>
</html>
