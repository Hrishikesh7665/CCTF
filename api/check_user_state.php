<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

// Check if user is logged in
if (!$_loginInfo) {
    header("Location: /");
    exit();
}

function sendResponse($status, $message, $notification = null)
{
    global $_loginInfo;
    if ($status != '210') {
        session_destroy();
        $_loginInfo = array();
    }
    $response = new stdClass();
    $response->status = $status;
    $response->message = $message;
    $response->notifications = $notification;
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/common/functions.php');
    $savedAuth = $_loginInfo['tshash'];
    $savedRole = $_loginInfo['role'];
    $savedUID = $_loginInfo['uid'];
    $savedEmail = $_loginInfo['email'];

    try {
        $sql = "SELECT u.id AS users_id, CASE WHEN la.ts_hash = '' THEN NULL ELSE la.ts_hash END AS last_ts_hash, u.name, u.email, u.status, u.role FROM users u JOIN (SELECT users_id, CASE WHEN ts_hash = '' THEN NULL ELSE ts_hash END AS ts_hash FROM logs__auth WHERE (users_id, time_stamp) IN (SELECT users_id, MAX(time_stamp) AS max_time_stamp FROM logs__auth WHERE users_id = ? AND remark = 'Login Success' GROUP BY users_id)) la ON u.id = la.users_id WHERE u.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $savedUID, $savedUID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
    } catch (Exception $e) {
        handle_error($e);
    }

    if ($row && $row['status'] != 'true') {
        insertLog($conn, $savedUID, 'Baned user from platform');
        sendResponse('201', 'baned');
    } elseif ($row && $row['last_ts_hash'] != $savedAuth) {
        insertLog($conn, $savedUID, 'Multiple session detected');
        sendResponse('202', 'duplicate session');
    } elseif ($row && ucwords($row['role']) === $savedRole && $row['email'] === $savedEmail) {
        try {
            $result = getNotifications($conn, $savedUID);

            $notifications = [];
            $unviewedCount = 0;

            while ($row = $result->fetch_assoc()) {
                // Encrypt the ID and add it as 'enID'
                $row['enID'] = encryptData($row['id'], $key);

                // Hash the original ID
                $row['hashedID'] = hash_hmac("sha512", $row['id'], 'cdack-CTF');

                // Remove the original 'id' for security
                unset($row['id']);

                // Check if the notification has not been viewed and increment the counter
                if ($row['viewed'] == false) {
                    $unviewedCount++;
                }

                // Add the notification to the list after modifications
                $notifications[] = $row;
            }

            // Create a JSON response
            $response = [
                'unviewed_count' => $unviewedCount,
                'notifications' => $notifications
            ];

            sendResponse('210', 'success', $response);
        } catch (Exception $e) {
            handle_error($e);
        }
    } else {
        sendResponse('200', 'failed');
    }
}
