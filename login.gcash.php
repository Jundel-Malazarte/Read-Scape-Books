<?php
session_start();
include 'db_connect.php'; // Ensure this file connects to your database

// Retrieve mobile number and email from session
$mobile_number = $_SESSION['phone'] ?? '';
$email = $_SESSION['email'] ?? '';

// Format the mobile number
if (!empty($mobile_number)) {
    // Remove any leading "+63" or non-digit characters
    $formatted_mobile_number = preg_replace('/[^0-9]/', '', $mobile_number);
    $formatted_mobile_number = preg_replace('/^(\+?63)/', '', $formatted_mobile_number);

    // Ensure it starts with "9" and limit to 10 digits
    if (substr($formatted_mobile_number, 0, 1) !== '9') {
        $formatted_mobile_number = '9' . ltrim($formatted_mobile_number, '0');
    }
    $formatted_mobile_number = substr($formatted_mobile_number, 0, 10);
} else {
    $formatted_mobile_number = '';
}

// Check if mobile number and email are provided
if (empty($mobile_number) || empty($email)) {
    header("Location: checkout.php?error=no_mobile_or_email");
    exit();
}

// Check if the mobile number already exists in the database (gcash_users2)
$sql = "SELECT mobile_number FROM gcash_users2 WHERE mobile_number = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    die('Error preparing statement: ' . htmlspecialchars(mysqli_error($conn)));
}

mysqli_stmt_bind_param($stmt, "s", $formatted_mobile_number);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result === false) {
    die('Error executing statement: ' . htmlspecialchars(mysqli_error($conn)));
}

$existing_user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$existing_user) {
    // Register the new mobile number with a default balance of 1,000,000.00
    $default_balance = 1000000.00;
    $sql = "INSERT INTO gcash_users2 (mobile_number, email, balance) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        die('Error preparing statement: ' . htmlspecialchars(mysqli_error($conn)));
    }

    mysqli_stmt_bind_param($stmt, "ssd", $formatted_mobile_number, $email, $default_balance);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Store the formatted mobile number in session for the next page
$_SESSION['gcash_mobile'] = $formatted_mobile_number;

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>GCash Login</title>
    <meta name="next-head-count" content="3" />
    <link rel="stylesheet" href="./assets/css/main.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100&display=swap" rel="stylesheet">
    <link rel="icon" href="./images/gcash.png" type="image/x-icon" style="border-radius: 50%;" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #eceff1;
        }

        .container {
            display: grid;
        }

        .w-screen {
            width: 50%;
            justify-self: center;
            align-self: center;
            align-items: center;
            margin: auto;
        }
    </style>
</head>

<body class="overflow-hidden scroll-smooth">
    <div id="__next">
        <style>
            #nprogress {
                pointer-events: none;
            }

            #nprogress .bar {
                background: #007cff;
                position: fixed;
                z-index: 9999;
                top: 0;
                left: 0;
                width: 100%;
                height: 3px;
            }

            #nprogress .peg {
                display: block;
                position: absolute;
                right: 0px;
                width: 100px;
                height: 100%;
                box-shadow: 0 0 10px #007cff, 0 0 5px #007cff;
                opacity: 1;
                -webkit-transform: rotate(3deg) translate(0px, -4px);
                -ms-transform: rotate(3deg) translate(0px, -4px);
                transform: rotate(3deg) translate(0px, -4px);
            }

            #nprogress .spinner {
                display: block;
                position: fixed;
                z-index: 1031;
                top: 15px;
                right: 15px;
            }

            #nprogress .spinner-icon {
                width: 18px;
                height: 18px;
                box-sizing: border-box;
                border: solid 2px transparent;
                border-top-color: #007cff;
                border-left-color: #007cff;
                border-radius: 50%;
                -webkit-animation: nprogress-spinner 400ms linear infinite;
                animation: nprogress-spinner 400ms linear infinite;
            }

            .nprogress-custom-parent {
                overflow: hidden;
                position: relative;
            }

            .nprogress-custom-parent #nprogress .spinner,
            .nprogress-custom-parent #nprogress .bar {
                position: absolute;
            }

            @-webkit-keyframes nprogress-spinner {
                0% {
                    -webkit-transform: rotate(0deg);
                }

                100% {
                    -webkit-transform: rotate(360deg);
                }
            }

            @keyframes nprogress-spinner {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }
        </style>

        <div class="container">
            <div class="w-screen h-screen bg-gcash-blue px-5">
                <div class="flex flex-col w-full">
                    <img src="./images/gcash.png" alt="gcash" class="h-60 object-contain" />
                    <div class="flex flex-col gap-1 py-2">
                        <span class="text-white">Mobile number registered</span>
                        <div class="flex flex-row gap-1 border-b border-gcash-secondary-blue p-1">
                            <span class="font-medium text-base text-white border-r border-gcash-secondary-blue pr-4">+63</span>
                            <input
                                pattern="[0-9]*"
                                type="tel"
                                maxlength="10"
                                class="w-full outline-none transition-all appearance-none bg-transparent text-white text-base font-medium"
                                value="<?php echo htmlspecialchars($formatted_mobile_number); ?>"
                                readonly />
                        </div>
                        <span class="text-white text-sm">Available for all networks!</span>
                    </div>
                    <div class="mt-16 flex flex-col gap-4">
                        <p class="text-center text-sm text-white">
                            By tapping Next, we'll collect your mobile number's network information to send you a One-Time Password (OTP).
                        </p>
                        <a class="bg-white px-3 py-2 rounded-full text-gcash-blue text-base text-center tracking-wide" href="./payments.gcash.php">Next</a>
                    </div>
                </div>
                <div class="fixed bottom-0 w-full left-0 px-6 py-2">
                    <div class="flex justify-between items-center">
                        <a class="text-white text-xs" href="">Help Center</a>
                        <span class="text-gcash-secondary-blue text-xs">v5.56.0:595</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<?php
include 'db_connect.php';

$mobile_number = '9816650907'; // Example mobile number

$sql = "SELECT * FROM gcash_users2 WHERE mobile_number = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 's', $mobile_number);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $user = mysqli_fetch_assoc($result);
        // ...existing code...
    } else {
        echo "Error fetching user: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing statement: " . mysqli_error($conn);
}

mysqli_close($conn);
?>