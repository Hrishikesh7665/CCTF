<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(404);
    include($_SERVER["DOCUMENT_ROOT"] . "/404.html");
    exit();
}
?>

<div class="position-relative">
    <div class="authentication-wrapper authentication-basic login">
        <div class="authentication-inner">

            <!-- Login -->
            <div class="card">

                <div class="card-body mt-2">
                    <h4 class="mb-2">LDAP Verification 🛡️</h4>
                    <p class="mb-4">Please provide your LDAP/CDAC Single Sign-On password for verification to complete your registration</p>
                    <form id="formAuthenticationLdap" class="mb-3" action="<?php echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME); ?>" method="POST" role="form">
                        <div class="form-floating form-floating-outline mb-3">
                            <input type="text" class="form-control" value="<?php echo $_SESSION['registrationData']['userEmail'] ?>" autocomplete="nope" readonly />
                            <label for="email">Email</label>
                        </div>
                        <div class="mb-3">
                            <div class="form-password-toggle">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" autocomplete="false" required />
                                        <label for="password">LDAP Password</label>
                                    </div>
                                    <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
                                </div>
                            </div>
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

                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit">Authenticate</button>
                        </div>
                    </form>
                </div>
            </div>
            <img src="/assets/img/CDAC-CTF.png" alt="cdacCTF-Logo" class="authentication-image-object-left d-none d-lg-block login-logo-container-img">
        </div>
    </div>
</div>

<!-- toast & modal-->
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/template/toast.php');
?>

<?php
$requiredJs = 2;
require_once($_SERVER['DOCUMENT_ROOT'] . '/template/scripts-section.php');
?>