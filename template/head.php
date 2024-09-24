<?php
// security headers
// header("X-Frame-Options: DENY");
// header("X-Frame-Options: SAMEORIGIN");
// header("Content-Security-Policy: frame-ancestors 'none'; default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' https://fonts.googleapis.com 'unsafe-inline'; img-src 'self' data: 'self'; font-src 'self' https://fonts.gstatic.com;");
// header("X-Powered-By: Test");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Feature-Policy: geolocation 'none'; midi 'none'; camera 'none'; usb 'none'");
header("Access-Control-Allow-Origin: https://ctf.cdac.in");
// header("Server: Test");
// header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");


require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

if ($page != 'reset-password' && isset($_SESSION['passwordResetData'])) {
	unset($_SESSION['passwordResetData']);
}

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
	header("Location: /login");
	die();
}

$class = "light-style layout-navbar-fixed layout-menu-fixed layout-compact";
if ($page == 'login') {
	$class = "light-style layout-wide customizer-hide";
	$title_info = 'Login';
} elseif ($page == 'reset-password') {
	$class = "light-style layout-wide customizer-hide";
	$title_info = 'Reset Password';
} elseif ($page == 'registration') {
	$class = "light-style layout-wide customizer-hide";
	$title_info = 'New User Registration';
} elseif ($page == 'dashboard' && $role == 'User') {
	$title_info = $role . ' Dashboard';
} elseif ($page == 'account') {
	$title_info = 'Account Settings';
} elseif ($page == 'activity') {
	$title_info = 'Account Activity';
} elseif ($page == 'scoreboard') {
	$title_info = 'Leaderboard';
} elseif ($page == 'faq') {
	$title_info = 'FAQ';
} elseif ($page == 'feedback' && $role == 'User') {
	$title_info = 'Feedback';
} elseif ($page == 'certificate' && $role == 'User') {
	$title_info = 'Certificate';
} elseif ($page == 'admin-dashboard' && $role == 'Admin') {
	$title_info = 'Admin Dashboard';
} elseif ($page == 'all-center' && $role == 'Admin') {
	$title_info = 'CDAC Center Management';
} elseif ($page == 'all-positions' && $role == 'Admin') {
	$title_info = 'Designation Management';
} elseif (($page == 'all-users' || $page == 'user-activity') && $role == 'Admin') {
	$title_info = 'Users Management';
} elseif ($page == 'all-category' && $role == 'Admin') {
	$title_info = 'Category Management';
} elseif ($page == 'all-questions' && $role == 'Admin') {
	$title_info = 'Questions Management';
} elseif ($page == 'all-feedback' && $role == 'Admin') {
	$title_info = 'View Feedbacks';
} elseif ($page == 'manage-certificates' && $role == 'Admin') {
	$title_info = 'Manage Certificates';
} elseif ($page == 'notification-manager' && $role == 'Admin') {
	$title_info = 'Bulletin Management';
} elseif ($page == 'all-notice' && $role == 'Admin') {
	$title_info = 'Show all notifications';
} elseif ($page == 'notice-board' && ($role == 'Admin' || $role == 'User')) {
	$title_info = 'Show all notifications';
}



if (!isset($title_info)) {
	header("Location: /login");
	die();
	// die('Permission denied');
}
$title = $title_info . " | CDAC-K CTF Challenge";
?>

<!DOCTYPE html>

<?php

$preventReloadPage = "";

if ($page == 'login') {
	$preventReloadPage = "/login";
} elseif ($page == 'all-center') {
	$preventReloadPage = "/admin-zone/all-center";
} elseif ($page == 'all-positions') {
	$preventReloadPage = "/admin-zone/all-positions";
} elseif ($page == 'account') {
	$preventReloadPage = "/account";
} elseif ($page == 'all-category') {
	$preventReloadPage = "/admin-zone/all-category";
} elseif ($page == 'all-questions') {
	$preventReloadPage = "/admin-zone/all-questions";
} elseif ($page == 'manage-certificates') {
	$preventReloadPage = "/admin-zone/manage-certificates";
}

if ($preventReloadPage != "") {
	echo "<script type='text/javascript'>const navigationEntries = performance.getEntriesByType('navigation');if (navigationEntries.length && navigationEntries[0].type === 'reload') {document.location.replace('" . $preventReloadPage . "');}</script>";
} ?>


<html lang="en" class="<?php echo $class; ?>" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />


	<title><?php echo $title; ?></title>

	<meta name="description" content="Start your development with a Dashboard for Bootstrap 5" />
	<meta name="keywords" content="dashboard, material, material design, bootstrap 5 dashboard, bootstrap 5 design, bootstrap 5">

	<!-- Favicon -->
	<link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico" />

	<!-- Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com/">
	<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;ampdisplay=swap" rel="stylesheet">

	<!-- Icons -->
	<link rel="stylesheet" href="/assets/vendor/fonts/materialdesignicons.css" />
	<!-- <link rel="stylesheet" href="../../assets/vendor/fonts/flag-icons.css" /> -->

	<!-- Menu waves for no-customizer fix -->
	<!-- <link rel="stylesheet" href="/assets/vendor/libs/node-waves/node-waves.css" /> -->

	<!-- Core CSS -->
	<link rel="stylesheet" href="/assets/css/core.css" class="mainConfiguration-core-css" />
	<link rel="stylesheet" href="/assets/css/theme-default.css" class="mainConfiguration-theme-css" />
	<link rel="stylesheet" href="/assets/css/style.css" />

	<!-- Vendors CSS -->
	<?php
	if ($page != 'login' && $page != 'reset-password') {
		echo '<link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />';
	}
	if ($page == 'login' || $page == 'reset-password') {
		echo '<link rel="stylesheet" href="/assets/vendor/libs/@form-validation/umd/styles/index.min.css" />';
		echo '<link rel="stylesheet" href="/assets/css/login-registration.css">';
	}
	if ($page == 'registration') {
		echo '<link rel="stylesheet" href="/assets/vendor/libs/bs-stepper/bs-stepper.css" />';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/bootstrap-select/bootstrap-select.css" />';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css" />';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/@form-validation/umd/styles/index.min.css" />';
		echo '<link rel="stylesheet" href="/assets/css/login-registration.css">';
	}
	if ($page == 'dashboard' || $page == 'account') {
		echo '<link rel="stylesheet" href="/assets/vendor/libs/animate-css/animate.css" />';
	}
	if ($page == 'admin-dashboard') {
		echo '<link rel="stylesheet" href="/assets/vendor/libs/flatpickr/flatpickr.css" />';
	}
	if ($page == 'feedback') {
		echo '<link rel="stylesheet" href="/assets/vendor/libs/rateyo/rateyo.css" />';
	}
	if ($page == 'account') {
		echo '<link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css" />';
	}
	if ($page == 'all-users') {
		echo '<link rel="stylesheet" href="/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css">';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css">';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css" />';
	}
	if ($page == 'all-questions') {
		echo '<link rel="stylesheet" href="/assets/vendor/libs/quill/typography.css" />';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/quill/katex.css" />';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/quill/editor.css" />';
	}
	if ($page == 'all-feedback') {
		echo '<link rel="stylesheet" href="/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css">';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css">';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/rateyo/rateyo.css" />';
	}
	if ($page == 'notification-manager') {
		echo '<link rel="stylesheet" href="/assets/vendor/libs/@form-validation/umd/styles/index.min.css" />';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css" />';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/flatpickr/flatpickr.css" />';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/quill/typography.css" />';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/quill/katex.css" />';
		echo '<link rel="stylesheet" href="/assets/vendor/libs/quill/editor.css" />';
	}
	?>

	<link rel="stylesheet" href="/assets/vendor/libs/spinkit/spinkit.css" />
	<!-- <link rel="stylesheet" href="../../assets/vendor/libs/typeahead-js/typeahead.css" />  -->

	<!-- Helpers -->
	<script src="/assets/vendor/js/helpers.js"></script>
	<script src="/assets/js/mainConfiguration.js"></script>
	<script src="/assets/js/config.js"></script>

</head>
