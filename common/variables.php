<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
	http_response_code(404);
	include($_SERVER["DOCUMENT_ROOT"] . "/404.html");
	exit();
}


$xmlConfigFile = $_SERVER['DOCUMENT_ROOT'] . '/ctf_configuration.xml';

$t = time();
$xml = simplexml_load_file($xmlConfigFile);

if ($xml === false) {
	die('Error loading XML file');
}

$start_time = (string) $xml->competitionTime->startTime;
$end_time = (string) $xml->competitionTime->endTime;
$registrationStatus = (string) $xml->registration->status; //open close

if (!isset($page)){
	$page = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
}

if ($registrationStatus != 'open' && $page == 'registration') {
	header("Location: /");
	die();
}

if ($page != 'registration' && $page != 'verifyEmail' && $page !='public-scoreboard') {
	$login_user_id = $_loginInfo['uid'];

	$user_score = 0;
	$user_solve = 0;
	$users_count = 0;
	$challenges_count = 0;
	$user_rank = 0;

	if ($t >= $start_time && $t <= $end_time) {
		$comp_state = 'going';
	} elseif ($t <= $start_time) {
		$comp_state = 'upcoming';
	} elseif ($t >= $end_time) {
		$comp_state = 'end';
	}
	try {
		if ($comp_state == 'going') {
			require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
			require_once($_SERVER['DOCUMENT_ROOT'] . '/common/functions.php');
			$user_stats = getUserStats($conn, $login_user_id);
			$user_score = $user_stats['user_score'];
			$user_solve = $user_stats['user_solve'];
			$users_count = $user_stats['users_count'];
			$challenges_count = $user_stats['challenges_count'];
			$user_rank = $user_stats['user_rank'];
		}
	} catch (Exception $e) {
		handle_error($e);
	}
}
