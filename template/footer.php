<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(404);
    include($_SERVER["DOCUMENT_ROOT"] . "/404.html");
    exit();
}
?>

<footer class="content-footer footer bg-footer-theme mt-0 pt-0">
    <div class="container-xxl">
        <div class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
            <div class="text-body fw-light">
                Developed By CDAC Kolkata ISS Team
            </div>
            <div class="d-none d-lg-inline-block">
                <a href="https://github.com/ISS-CDACK/CCTF" target="_blank" class="custom-link">
                    <span class="default-text">CDAC CTF Platform v2.0</span> <i class="mdi mdi-github"></i>
                </a>
            </div>
        </div>
    </div>
</footer>
