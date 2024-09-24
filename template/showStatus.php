<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(404);
    include($_SERVER["DOCUMENT_ROOT"]."/404.html");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico" />
    <title><?php if ($status == "failure") : echo 'Link Expired';
            else : echo 'Link Validated';
            endif; ?> | CDAC-K CTF Challenge</title>
    <style>
        body {
            text-align: center;
            padding: 40px 0;
            background: #EBF0F5;
        }

        h1 {
            font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
            font-weight: 900;
            font-size: 40px;
            margin-bottom: 10px;
        }

        p {
            color: #404F5E;
            font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
            font-size: 25px;
            margin: 0;
        }

        .p2 {
            color: #404F5E;
            font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
            font-size: 20px;
            margin-top: 14px;
        }

        i.checkmark {
            color: #9ABC66;
            font-size: 100px;
            line-height: 200px;
            margin-left: -15px;
        }

        i.failed-icon {
            color: #E15252;
            font-size: 100px;
            line-height: 200px;
            margin-left: -15px;
        }

        .card {
            background: white;
            padding: 60px;
            border-radius: 4px;
            box-shadow: 0 2px 3px #C8D0D8;
            display: inline-block;
            margin: 0 auto;
            width: 480px;
        }
    </style>
</head>

<body>
    <?php
    // Check the status and render the appropriate message
    if ($status === "success") {
        echo '
            <div class="card">
                <div style="border-radius:200px; height:200px; width:200px; background: #F8FAF5; margin:0 auto;">
                    <i class="checkmark">✓</i>
                </div>
                <h1 style="color: #88B04B;">Success</h1>
                <p>Your Account Has Been Created Successfully!</p>
                <p class="p2">For security purposes, you will be able to log in after 15 minutes.</p>
            </div>
        ';
    } else {
        echo '
            <div class="card">
                <div style="border-radius:200px; height:200px; width:200px; background: #F8FAF5; margin:0 auto;">
                    <i class="failed-icon">✘</i>
                </div>
                <h1 style="color: #E15252;">Failure</h1>
                <p>This link may be invalid or already used.</p>
                <p class="p2">Please try again, or contact us at <a href="mailto:iss-kol@cdac.in" style="text-decoration: none;">iss-kol@cdac.in</a>.</p>
            </div>
        ';
    }
    ?>
</body>

</html>