<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>GCash Receipt</title>
    <meta name="next-head-count" content="3" />
    <link rel="preload" href=".css" as="style" />
    <link rel="stylesheet" href="./assets/css/main.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100&display=swap" rel="stylesheet">
    <link rel="icon" href="./images/gcash.png" type="image/x-icon" style="border-radius: 50%;" />
</head>
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color:  #007cff;;
    }

    .container {
        display: grid;
    }

    .w-screen {
        width: 40%;
        height: auto;
        justify-self: center;
        align-self: center;
        align-items: center;
        margin: auto;
    }

    .flex-col-w-full {
        display: flex;
        flex-direction: column;
        width: 100%;
        height: 500px;
        min-height: 400px;
    }

    .container-box {
        width: 100%;
        height: 900px;
        background-color: #fff;
        color: #fff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 100px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .pay-button {
        background-color: #007cff;
        color: white;
        font-weight: bold;
        padding: 8px 16px;
        border-radius: 20px;
        text-align: center;
        text-decoration: none;
        border: none;
        display: inline-block;
        margin-top: 10px;
        width: 50%;
    }

    .pay-with-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .pay-with-left {
        font-size: 2xl;
        font-weight: bold;
        color: #000;
    }

    .pay-with-right {
        font-size: 2xl;
        font-weight: bold;
        color: #000;
    }

    #nprogress {
        pointer-events: none;
    }

    #nprogress .bar {
        background: #007cff;
        position: fixed;
        z-index: 9999;
        top: 0;
        left: 0;
        width: 50%;
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
        -webkit-animation: nprogresss-spinner 400ms linear infinite;
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

<body class="overflow-hidden scroll-smooth">
    <div id="__next">
        <div class="container" style="background-color: #007cff; width:100%; height:380px;">
            <div class="w-screen h-screen px-5">
                <div class="flex-col-w-full">
                    <img src="./images/gcash.png" alt="gcash" class="h-60 object-contain" />
                    <div class="container-box">
                        <div class="flex flex-col gap-4">
                            <div class="text-center">
                                <i class="fas fa-check-circle" style="color: blue; font-size: 3rem; margin-bottom: 10px;"></i>
                            </div>
                            <span class="text-black font-bold text-xl text-center" style="color: #283593;">ReadScape Payment</span>
                            <span class="text-black font-xl text-xl text-center" style="background-color: #e0f2f1; color: #283593; font-weight: bold; border-radius: 10px; display: inline-block; padding: 2px;">+63 123 456 789</span>
                            <span class="text-black font-2xl text-center">Sent via GCash</span>
                            <div class="flex justify-between items-center py-2">
                                <span class="font-bold" style="color: #283593;">Amount</span>
                                <span class="font-bold text-black" style="color: #283593;">PHP 1,000</span>
                            </div>
                            <!-- break; -->
                            <hr style="margin: 10px 0px;">
                            <div class="flex flex-col items-center py-1">
                                <div class="flex justify-between w-full">
                                    <span class="text-black font-bold" style="color: #283593;">Total Amount Sent</span>
                                    <span class="text-black font-bold text-xl" style="color: #283593;">PHP 1,000</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; width: 100%;">
                                    <span class="text-black font-20px text-1xl left-0" style="color: #283593; padding: 10px; margin: 2px; text-align: left; width: 50%;">Reference ID: 123456789</span>
                                    <span style="color: #283593; padding: 10px; margin: 2px; text-align: left; width: 50%;">Mar 10, 2025 7:00pm</span>
                                </div>
                            </div>
                        </div>
                        <div class="fixed bottom-0 w-full left-0 px-6 py-2">
                            <div class="flex justify-between items-center">
                                <a class="text-black text-xs" href="">Help Center</a>
                                <span class="text-gcash-secondary-blue text-xs">v5.56.0:595</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>