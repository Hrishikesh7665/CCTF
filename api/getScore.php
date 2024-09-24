<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";
	require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";

	$scoreboardData = fetchScoreboardData();


	if ($scoreboardData['status']) {
		$customJsonData = array();
		foreach ($scoreboardData['data'] as $data) {
			$customJsonData[] = array(
				'position' => $data['rank'],
				'userName' => $data['name'],
				'solved' => intval($data['solved']),
				'points' => intval($data['score']),
				'status' => $data['status'] === "true" ? "Active" : "Inactive",
				'email' => $data['email'],
				// 'uid' => intval($data['uid']),
				'time' => date("d-m-Y h:i:s A", strtotime($data['time'])),
			);
		}

		$jsonData = json_encode($customJsonData);

		header('Content-Type: application/json');
		echo $jsonData;
		exit();
	} else {
		$jsonData = array(
			'position' => '-',
			'userName' => '-',
			'solved' => '-',
			'points' => '-',
			'status' => '-',
			'email' => '-',
			'time' => '-'
		);
		$jsonData = json_encode($jsonData);
		header('Content-Type: application/json');
		echo $jsonData;
		exit();
	}
}
