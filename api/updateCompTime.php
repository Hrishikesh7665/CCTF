<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/common/variables.php');

// Check if user is logged in
if (!$_loginInfo) {
    header("Location: /");
    exit();
}

function isValidUnixTimestamp($timestamp)
{
    global $t;

    if (!is_numeric($timestamp)) {
        return false;
    }

    if ($timestamp < 0 || $timestamp > PHP_INT_MAX) {
        return false;
    }

    if ($timestamp >= $t) {
        return true;
    }

    return false;
}


function showResponse($response)
{
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && (isset($_POST['startTime']) || isset($_POST['endTime']) || isset($_POST['registration']))) {
    $response = array('status' => true);
    if (isset($_POST['startTime'])) {
        if (isValidUnixTimestamp($_POST['startTime'])) {
            $xml->competitionTime->startTime = $_POST['startTime'];
        } else {
            $response = array('status' => false, 'message' => 'Invalid Start Timestamp');
            showResponse($response);
        }
    }
    if (isset($_POST['endTime'])) {
        if (isValidUnixTimestamp($_POST['endTime'])) {
            $xml->competitionTime->endTime = $_POST['endTime'];
        } else {
            $response = array('status' => false, 'message' => 'Invalid End Timestamp');
            showResponse($response);
        }
    }

    if (isset($_POST['registration'])) {
        if ($_POST['registration'] === "open" || $_POST['registration'] === "close") {
            $xml->registration->status = $_POST['registration'];
        } else {
            $response = array('status' => false, 'message' => 'Invalid Registration Status');
            showResponse($response);
        }
    }

    if ($response['status']) {
        $xml->asXML($xmlConfigFile);
        $response = array('status' => true, 'message' => 'Updated');
        showResponse($response);
    }
}
