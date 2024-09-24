<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/common/functions.php');

    $savedUID = $_loginInfo['uid'];

    try {
        // Fetch notifications using the function
        $result = getNotifications($conn, $savedUID);

        $notifications = [];
        $unviewedCount = 0;

        while ($row = $result->fetch_assoc()) {
            // Encrypt the ID and add it as 'enID'
            $row['enID'] = encryptData($row['id'], $key);

            // Hash the original ID
            $row['hashedID'] = hash_hmac("sha512", $row['id'], 'cdack-CTF');

            $row['description'] = str_replace('\"', '"', htmlspecialchars_decode($row['description']));

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

        // Send JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } catch (Exception $e) {
        handle_error($e);
    }
}
