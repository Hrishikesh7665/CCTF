<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

// Check if user is logged in
if (!$_loginInfo) {
    header("Location: /");
    exit();
}

try {
    $role = $_loginInfo['role'];
    $userId = $_loginInfo['uid'];

    require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

    // Ensure all required parameters are set
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cid'], $_POST['flag'], $_POST['user_id'])) {

        $captcha = $_POST['captcha'];
        if (empty($captcha) || !isset($_SESSION['captcha']) || $captcha != $_SESSION['captcha']) {
            sendResponse(200, 'Captcha not valid.');
            exit();
        }

        // check game running or not
        // if () {
        // sendResponse(209, 'Captcha not valid.');
        // exit();
        // }

        $cid = substr($_POST['cid'], 0, 2);
        $flag = substr($_POST['flag'], 0, 25);
        $uid = substr($_POST['user_id'], 0, 3);

        try {
            // Check if the user has already solved the challenge
            if (hasAlreadySolved($conn, $cid, $userId)) {
                sendResponse(201, 'You have already solved this question');
                exit();
            }

            // Check flag correctness and save to logs__flag
            $flagStatus = checkFlag($conn, $cid, $flag, $userId);
            if ($flagStatus) {
                require_once($_SERVER['DOCUMENT_ROOT'] . '/common/functions.php');
                insertIntoScoreboard($conn, $cid, $userId);
                $user_stats = getUserStats($conn, $uid);
                sendResponse(202, $user_stats);
                exit();
            } else {
                sendResponse(203, 'Wrong Flag');
                exit();
            }
        } catch (Exception $e) {
            handle_error($e);
        }
    } else {
        sendResponse(210, 'Insufficient parameters');
        exit();
    }
} catch (Exception $e) {
    handle_error($e);
}

function hasAlreadySolved($conn, $cid, $userId)
{
    try {
        $stmt = $conn->prepare("SELECT * FROM scoreboard WHERE c_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cid, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return ($result->num_rows > 0);
    } catch (Exception $e) {
        handle_error($e);
    }
}

function checkFlag($conn, $cid, $flag, $userId)
{
    try {
        $stmt = $conn->prepare("SELECT flag FROM challenges WHERE id = ?");
        $stmt->bind_param("i", $cid);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $flagStatus = ($row['flag'] == $flag);
            logFlag($conn, $cid, $flag, $userId, $flagStatus);
            return $flagStatus;
        } else {
            logFlag($conn, $cid, $flag, $userId, false);
            return false;
        }
    } catch (Exception $e) {
        handle_error($e);
    }
}

function insertIntoScoreboard($conn, $cid, $userId)
{
    try {
        $stmt = $conn->prepare("INSERT INTO scoreboard (c_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $cid, $userId);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        handle_error($e);
    }
}

function logFlag($conn, $cid, $flag, $userId, $flagStatus)
{
    try {
        $stmt = $conn->prepare("INSERT INTO logs__flag (u_id, c_id, submitted_flag, flag_status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $userId, $cid, $flag, $flagStatus);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        handle_error($e);
    }
}

function sendResponse($status, $message)
{
    $response = new stdClass();
    $response->status = $status;
    $response->message = $message;
    echo json_encode($response);
    exit();
}
