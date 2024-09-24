<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(404);
    include($_SERVER["DOCUMENT_ROOT"] . "/404.html");
    exit();
}

// require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

if ($page == 'registration' || $page == 'reset-password') {
    if ($_loginInfo != false) {
        header("Location: /login");
        die();
    }
} elseif ($_loginInfo or $page == 'login') {
    if ($_loginInfo) {
        $role = $_loginInfo['role'];
    }
} else {
    header("Location: /");
    die();
}
?>

<?php if ($page != 'login' && $page != 'registration' && $page != 'reset-password') : ?>
    <script type='text/javascript'>
        const ctf_start_js = <?= $start_time ?> * 1000;
        const ctf_end_js = <?= $end_time ?> * 1000;
        const php_server_time = <?= (time() * 1000) ?>;
        const ctf_state = '<?= $comp_state ?>';
        const user_id = <?= $_loginInfo['uid'] ?>;
    </script>
<?php endif; ?>

<?php if ($page == 'dashboard' || $page == 'account' || $page == 'activity') : ?>
    <script type='text/javascript'>
        <?php
        if ($comp_state == 'upcoming') {
            echo 'window.onload = function () {
                const x = setInterval(updateCountdown, 1000);
            }';
        }
        if ($comp_state == 'going') {
            echo 'window.onload = function () {
                const y = setInterval(countdown, 1000);
            }';
        }
        ?>
    </script>
<?php endif; ?>


<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="/assets/vendor/libs/jquery/jquery.js"></script>
<!-- <script src="../../assets/vendor/libs/popper/popper.js"></script> -->
<script src="/assets/vendor/js/bootstrap.js"></script>
<!-- <script src="../../assets/vendor/libs/node-waves/node-waves.js"></script> -->
<script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<!-- <script src="../../assets/vendor/libs/hammer/hammer.js"></script> -->
<!-- <script src="../../assets/vendor/libs/i18n/i18n.js"></script> -->
<!-- <script src="../../assets/vendor/libs/typeahead-js/typeahead.js"></script> -->
<script src="/assets/vendor/js/menu.js"></script>

<!-- endbuild -->
<!-- Vendors JS -->
<?php if ($page == 'login' || $page == 'reset-password') {
    echo
    <<<HTML
<script src="/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js"></script>
<script src="/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js"></script>
<script src="/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js"></script>
HTML;
} ?>

<?php if ($page == 'registration' && $requiredJs === 1) {
    echo
    <<<HTML
<script src="/assets/vendor/libs/cleavejs/cleave.js"></script>
<script src="/assets/vendor/libs/cleavejs/cleave-phone.js"></script>
<script src="/assets/vendor/libs/bs-stepper/bs-stepper.js"></script>
<script src="/assets/vendor/libs/select2/select2.js"></script>
<script src="/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js"></script>
<script src="/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js"></script>
<script src="/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js"></script>
HTML;
} ?>

<?php if ($page == 'account') {
    echo
    <<<HTML
<script src="/assets/vendor/libs/select2/select2.js"></script>
HTML;
} ?>

<?php if ($page == 'feedback') {
    echo
    <<<HTML
<script src="/assets/vendor/libs/rateyo/rateyo.js"></script>
HTML;
} ?>

<?php if ($page == 'admin-dashboard') {
    echo
    <<<HTML
<script src="/assets/vendor/libs/chartjs/chartjs.js"></script>
<script src="/assets/vendor/libs/flatpickr/flatpickr.js"></script>
HTML;
} ?>

<?php if ($page == 'all-users') {
    echo
    <<<HTML
<script src="/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="/assets/vendor/libs/cleavejs/cleave.js"></script>
<script src="/assets/vendor/libs/cleavejs/cleave-phone.js"></script>
<script src="/assets/vendor/libs/select2/select2.js"></script>
HTML;
} ?>

<?php if ($page == 'all-questions') {
    echo
    <<<HTML
<script src="/assets/vendor/libs/quill/katex.js"></script>
<script src="/assets/vendor/libs/quill/quill.js"></script>
HTML;
} ?>

<?php if ($page == 'all-feedback') {
    echo
    <<<HTML
<script src="/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="/assets/vendor/libs/rateyo/rateyo.js"></script>
HTML;
} ?>

<?php if ($page == 'notification-manager') {
    echo
    <<<HTML
    <script src="/assets/vendor/libs/select2/select2.js"></script>
    <script src="/assets/vendor/libs/flatpickr/flatpickr.js"></script>
<script src="/assets/vendor/libs/quill/katex.js"></script>
<script src="/assets/vendor/libs/quill/quill.js"></script>
<script src="/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js"></script>
<script src="/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js"></script>
<script src="/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js"></script>
HTML;
} ?>

<!-- Main JS -->
<script src="/assets/js/main.js"></script>

<!-- Page JS -->
<?php if ($page == 'login') {
    echo
    <<<HTML
<script src="/assets/js/login.js"></script>
HTML;
} ?>

<?php if ($page == 'reset-password') {
    echo
    <<<HTML
<script src="/assets/js/reset-password.js"></script>
HTML;
} ?>

<?php if ($page == 'registration') {
    if ($requiredJs === 1) {
        echo <<<HTML
            <script src="/assets/js/registration.js"></script>
        HTML;
    } elseif ($requiredJs === 2) {
        echo <<<HTML
            <script src="/assets/js/registration2.js"></script>
        HTML;
    }
} ?>

<?php if ($page == 'dashboard') {
    echo
    <<<HTML
<script src="/assets/js/leaf-animation.js"></script>
<script src="/assets/js/user-dashboard.js"></script>
<script src="/assets/js/remainingTime.js"></script>
HTML;
} ?>

<?php if ($page == 'account') {
    echo
    <<<HTML
<script src="/assets/js/remainingTime.js"></script>
<script src="/assets/js/account.js"></script>
HTML;
} ?>

<?php if ($page == 'admin-dashboard') {
    echo
    <<<HTML
<script src="/assets/js/admin-dashboard.js"></script>
HTML;
} ?>

<?php if ($page == 'activity') {
    echo
    <<<HTML
<script src="/assets/js/remainingTime.js"></script>
HTML;
} ?>

<?php if ($page == 'all-center') {
    echo
    <<<HTML
<script src="/assets/js/all-center.js"></script>
HTML;
} ?>

<?php if ($page == 'all-positions') {
    echo
    <<<HTML
<script src="/assets/js/all-positions.js"></script>
HTML;
} ?>

<?php if ($page == 'all-users') {
    echo
    <<<HTML
<script src="/assets/js/all-users.js"></script>
HTML;
} ?>

<?php if ($page == 'all-category') {
    echo
    <<<HTML
<script src="/assets/js/all-category.js"></script>
HTML;
} ?>

<?php if ($page == 'all-questions') {
    echo
    <<<HTML
<script src="/assets/js/all-questions.js"></script>
HTML;
} ?>

<?php if ($page == 'all-feedback') {
    echo
    <<<HTML
<script src="/assets/js/viewFeedback.js"></script>
HTML;
} ?>

<?php if ($page == 'feedback') {
    echo
    <<<HTML
<script src="/assets/js/feedback.js"></script>
HTML;
} ?>

<?php if ($page == 'manage-certificates') {
    echo
    <<<HTML
<script src="/assets/js/manage-certificates.js"></script>
HTML;
} ?>

<?php if ($page == 'notification-manager') {
    echo
    <<<HTML
<script src="/assets/js/notification-manager.js"></script>
HTML;
} ?>