<?php
header('Cache-Control: no-cache, must-revalidate');
require_once($_SERVER['DOCUMENT_ROOT'] . '/template/head.php');


$remark = ['status' => true];
$action = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);


function maskEmail($email)
{
    $parts = explode('@', $email);
    // if(count($parts) != 2) {
    // return false; // Invalid email format
    // }

    $username = $parts[0];
    $domain = $parts[1];
    $lengthToMask = ceil(strlen($username) / 2);
    $maskedUsername = substr($username, 0, $lengthToMask) . str_repeat('*', $lengthToMask);
    return $maskedUsername . '@' . $domain;
}


function renderFirstScreen()
{
    global $action;
    echo <<<HTML
    <div class="card-body">
        <h4 class="mb-2">Forgot Password? 🔒</h4>
        <p class="mb-4">Please provide your email to receive a one-time password (OTP) for resetting your password.</p>
        <form id="formAuthentication" class="mb-3" action="$action" method="POST" role="form" onsubmit="return false">
            <div class="form-floating form-floating-outline mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" autofocus required autocomplete="false">
                <label>Email</label>
            </div>
            <!-- captcha -->
            <div class="mb-3">
                <label for="captcha" class="fw-bold">Captcha Image</label>
                <div class="d-flex align-items-center">
                    <img src="/captcha" alt="Captcha" id="captchaImage" class="me-2">
                    <button type="button" onclick="reloadCaptcha()" class="btn btn-link">
                    <i class="mdi mdi-reload"></i>
                    </button>
                </div>
            </div>
            <div class="form-floating form-floating-outline mb-3">
                <input type="text" class="form-control" id="captcha" name="captcha" placeholder="Enter the captcha" autocomplete="off" required>
                <label for="captcha">Captcha</label>
            </div>
            <!-- captcha -->
            <button class="btn btn-primary d-grid w-100">Send OTP</button>
        </form>
        <div class="text-center">
            <a href="/login" class="d-flex align-items-center justify-content-center">
            <i class="mdi mdi-chevron-left scaleX-n1-rtl mdi-24px"></i>
            Back to login
            </a>
        </div>
    </div>
HTML;
}

?>

<body class="spin-lock">
    <!-- Navbar -->
    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/template/navbar.php');
    require_once $_SERVER["DOCUMENT_ROOT"] . "/template/loadingSpinner.php";
    ?>
    <!-- / Navbar -->

    <div class="position-relative" style="height:calc(95vh - 70px)">
        <div class="authentication-wrapper authentication-basic login">
            <div class="authentication-inner">

                <!-- Login -->
                <div class="card">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['passwordResetData'])) {
                        $passResetData = $_SESSION['passwordResetData'];
                        if (isset($passResetData['userFullName']) && isset($passResetData['userEmail']) && isset($passResetData['otp']) && isset($passResetData['exp']) && isset($passResetData['isVerified'])) {
                            $userFullName = $passResetData['userFullName'];
                            $userEmail = $passResetData['userEmail'];
                            $maskedEmail = maskEmail($userEmail);
                            $otp = $passResetData['otp'];
                            $exp = $passResetData['exp'];
                            $isVerified = $passResetData['isVerified'];
                            $currentTimestamp = time();
                            if ($currentTimestamp > $exp) {
                                $remark = ['status' => false, 'message' => 'Expired'];
                                session_unset();
                                renderFirstScreen();
                            } elseif (!$isVerified) {
                                $functionNeed = true;
                                echo <<<HTML
                                    <div class="card-body">
                                        <h4 class="mb-2">OTP Verification</h4>
                                        <p class="text-start mb-4 mt-2">An OTP (One-Time Password) for password reset verification has been sent to the email address provided below:
                                            <span class="d-block mt-1">$maskedEmail</span>
                                        </p>
                                        <p class="mb-0">Type your 6 digit security code</p>
                                        <form id="twoStepsForm" role="form" onsubmit="return false">
                                            <div class="mb-3">
                                                <div class="auth-input-wrapper d-flex align-items-center justify-content-sm-between numeral-mask-wrapper">
                                                    <input type="tel" class="form-control auth-input text-center numeral-mask h-px-50 mx-1 my-2" maxlength="1" autofocus>
                                                    <input type="tel" class="form-control auth-input text-center numeral-mask h-px-50 mx-1 my-2" maxlength="1">
                                                    <input type="tel" class="form-control auth-input text-center numeral-mask h-px-50 mx-1 my-2" maxlength="1">
                                                    <input type="tel" class="form-control auth-input text-center numeral-mask h-px-50 mx-1 my-2" maxlength="1">
                                                    <input type="tel" class="form-control auth-input text-center numeral-mask h-px-50 mx-1 my-2" maxlength="1">
                                                    <input type="tel" class="form-control auth-input text-center numeral-mask h-px-50 mx-1 my-2" maxlength="1">
                                                </div>
                                                <input type="hidden" name="otp" />
                                            </div>
                                            <button class="btn btn-primary d-grid w-100 mb-3" onclick="checkOTP(document.querySelector('[name=\\'otp\\']').value)">
                                            Verify OTP
                                            </button>
                                            <div class="text-center">Didn't get the code?
                                                <span id="resendSpan"></span>
                                            </div>
                                        </form>
                                    </div>
                                HTML;
                            } elseif ($isVerified) {
                                $functionNeed = false;
                                echo <<<HTML
                                    <div class="card-body">
                                        <h4 class="mb-2">Reset Password</h4>
                                        <p class="mb-4">Your new password must be different from previously used passwords</p>
                                        <form id="formChangePass" class="mb-3" action="$action" method="POST">
                                            <div class="mb-3 form-password-toggle">
                                                <div class="input-group input-group-merge">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" autocomplete="false" required />
                                                        <label for="password">New Password</label>
                                                    </div>
                                                    <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
                                                </div>
                                            </div>
                                            <div class="mb-3 form-password-toggle">
                                                <div class="input-group input-group-merge">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="password" id="confirm-password" class="form-control" name="confirm-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" autocomplete="false" required />
                                                        <label for="confirm-password">Confirm Password</label>
                                                    </div>
                                                    <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
                                                </div>
                                            </div>
                                            <button class="btn btn-primary d-grid w-100 mb-3" onclick="updatePassword()">
                                            Set new password
                                            </button>
                                            <div class="text-center">
                                                <a href="/login" class="d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-chevron-left scaleX-n1-rtl mdi-24px"></i>
                                                Back to login
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                HTML;
                            }
                        } else {
                            session_unset();
                            renderFirstScreen();
                        }
                    } else {
                        renderFirstScreen();
                    }
                    ?>
                </div>
                <img src="/assets/img/CDAC-CTF.png" alt="cdacCTF-Logo" class="authentication-image-object-left d-none d-lg-block login-logo-container-img">
            </div>
        </div>
    </div>

    <!-- toast & modal-->
    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/template/toast.php');
    ?>


    <!-- Footer -->
    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/template/footer.php');
    ?>
    <!-- / Footer -->

    <!-- scripts -->
    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/template/scripts-section.php');
    if (isset($currentTimestamp) && isset($exp) && isset($functionNeed)) {
        if ($currentTimestamp < $exp) {
            echo "<script type='text/javascript'>";
            echo 'let exp = ' . $exp * 1000 . ';';
            echo 'let currentTimestamp = ' . $currentTimestamp . ';';
            if ($functionNeed) {
                echo 'let intervalID = setInterval(checkTimeValidity, 1000);';
            }
            echo "</script>";
        }
        if (!$functionNeed) {
            echo "<script type='text/javascript'>";
            echo 'try{clearInterval(intervalID);} catch (error) {}';
            echo "</script>";
        }
    }

    if (!$remark['status']) {
        if ($remark['message'] === 'Expired') {
            echo "<script type='text/javascript'>showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'OTP Expired!!', 'OTP Expired, Please Try Again');</script>";
        }
    }
    ?>
</body>