<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

// Check if user is logged in
if (!$_loginInfo) {
    header("Location: /");
    exit();
}
function sendResponse($status, $message)
{
    $response = new stdClass();
    $response->status = $status;
    $response->message = $message;
    echo json_encode($response);
    exit();
}

if (isset($_POST['userID']) && isset($_POST['value']) && isset($_POST['target'])) {
    $userID = $_POST['userID'];
    $value = $_POST['value'];
    $target = $_POST['target'];
    require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
    if ($target === "state") {
        try {
            $stmt = $conn->prepare("UPDATE `users` SET `status`=?  WHERE id=?");
            $stmt->bind_param("si", $value, $userID);
            if ($stmt->execute()) {
                sendResponse(200, 'success');
            } else {
                sendResponse(202, 'failed');
            }
        } catch (Exception $e) {
            handle_error($e);
        }
    } elseif ($target === "role") {
        //value false = user
        //value true = admin
        if ($value === "true") {
            $value = "admin";
        } else {
            $value = "user";
        }
        try {
            $stmt = $conn->prepare("UPDATE `users` SET `role`=?  WHERE id=?");
            $stmt->bind_param("si", $value, $userID);
            if ($stmt->execute()) {
                sendResponse(200, 'success');
            } else {
                sendResponse(202, 'failed');
            }
        } catch (Exception $e) {
            handle_error($e);
        }
    }
} else {
    sendResponse(210, 'Insufficient parameters');
}
