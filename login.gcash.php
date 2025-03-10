<!DOCTYPE html>
<html lang="en">

<head>
    <meta charSet="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>GCash</title>
    <meta name="next-head-count" content="3" />
    <link rel="preload" href=".css" as="style" />
    <link rel="stylesheet" href="./assets/css/main.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100&display=swap" rel="stylesheet">
    <link rel="icon" href="./images/gcash.png" type="image/x-icon" style="border-radius: 50%;"/>
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
        <div class="w-screen h-screen bg-gcash-blue px-6">
            <div class="flex flex-col w-full "><img src="./images/gcash.png" alt="gcash" class="h-60 object-contain" />
                <div class="flex flex-col gap-1 py-2"><span class="text-white">Enter your mobile number</span>
                    <div class="flex flex-row gap-1 border-b border-gcash-secondary-blue p-1"><span class="font-medium text-base text-white border-r border-gcash-secondary-blue pr-4">+63</span><input pattern="[0-9]*" type="tel" maxLength="12" class="w-full outline-none transition-all appearance-none bg-transparent text-white text-base font-medium" /></div><span class="text-white text-sm">Available for all networks!</span>
                </div>
                <div class="mt-16 flex flex-col gap-4">
                    <p class="text-center text-sm text-white"> By tapping next, we&#x27;ll collect your mobile number&#x27;s network information to be able to send you a One-Time Password (OTP).</p><a class="bg-white px-3 py-2 rounded-full text-gcash-blue text-base text-center tracking-wide" href="/auth/otp">Next</a>
                </div>
            </div>
            <div class="fixed bottom-0 w-full left-0 px-6 py-2">
                <div class="flex justify-between items-center"><a class="text-white text-xs" href="/#">Help Center</a><span class="text-gcash-secondary-blue text-xs">v5.56.0:595</span></div>
            </div>
        </div>
    </div>
    <script id="__NEXT_DATA__" type="application/json">
        {
            "props": {
                "pageProps": {}
            },
            "page": "/",
            "query": {},
            "buildId": "m5dQWGNQA5QRC9Qhz_7xu",
            "nextExport": true,
            "autoExport": true,
            "isFallback": false,
            "scriptLoader": []
        }
    </script>
</body>

</html>