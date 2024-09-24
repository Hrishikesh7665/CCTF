<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

// Check if user is logged in
if (!$_loginInfo) {
    header("Location: /");
    exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['requestedData'])) {
    if ($_POST['requestedData'] === 'questions-feedbacks') {
        try {
            $sql = "SELECT fc.`id` AS feedback_id, c.`id` AS challenge_id, c.`title` AS challenge_title, u.`id` AS user_id, u.`email` AS user_email, u.`name` AS user_name, u.`displayPic` AS pic, fc.`feedback`, fc.`rating`, fc.`ts` FROM `feedback__challenges` AS fc JOIN `challenges` AS c ON fc.`challenge_id` = c.`id` JOIN `users` AS u ON fc.`userId` = u.`id` AND c.id in (SELECT c_id FROM logs__qs) ORDER BY c.`id`, u.`id`, fc.`ts` ASC;";
            
            // $sql = "SELECT fc.`id` AS feedback_id, c.`id` AS challenge_id, c.`title` AS challenge_title, u.`id` AS user_id, u.`email` AS user_email, u.`name` AS user_name, u.`displayPic` AS pic, fc.`feedback`, fc.`rating`, fc.`ts` FROM `feedback__challenges` AS fc JOIN `challenges` AS c ON fc.`challenge_id` = c.`id` JOIN `users` AS u ON fc.`userId` = u.`id` ORDER BY c.`id`, u.`id`, fc.`ts` ASC;";

            // Execute the SQL query
            $result = $conn->query($sql);

            // Fetch the result as an associative array
            $resultArray = [];
            while ($row = $result->fetch_assoc()) {
                $resultArray[] = array(
                    'challenge' => $row['challenge_title'],
                    'email' => $row['user_email'],
                    'reviewer' => ucwords($row['user_name']),
                    'feedback' => !empty($row['feedback']) ? ucwords($row['feedback']) : '',
                    'review' => $row['rating'],
                    'date' => date('l d F Y', strtotime($row['ts'])),
                    'time' => date('h:i:s A', strtotime($row['ts'])),
                    'avatar' => (!empty($row['pic']) ? $row['pic'] : 'defaultAvatar.png'),
                );
            }

            $response = array(
                "data" => $resultArray
            );

            // Set the response header to JSON
            header('Content-Type: application/json');

            // Convert array to JSON format and output
            echo json_encode($response, JSON_PRETTY_PRINT);

            // Close the database connection
            $conn->close();
        } catch (Exception $e) {
            // Handle any errors
            handle_error($e);
        }
    } elseif ($_POST['requestedData'] === 'platform-feedbacks') {
        try {
            // Your SQL query
            $sql = "SELECT fc.`id` AS feedback_id, u.`id` AS user_id, u.`email` AS user_email, u.`name` AS user_name, u.`displayPic` AS pic, fc.`feedback`, fc.`advice`, fc.`rating`, fc.`ts` FROM `feedback__platform` AS fc JOIN `users` AS u ON fc.`userId` = u.`id` ORDER BY fc.`ts` ASC;";

            // Execute the SQL query
            $result = $conn->query($sql);

            // Fetch the result as an associative array
            $resultArray = [];
            while ($row = $result->fetch_assoc()) {
                $resultArray[] = array(
                    'email' => $row['user_email'],
                    'reviewer' => ucwords($row['user_name']),
                    'feedback' => !empty($row['feedback']) ? ucwords($row['feedback']) : '',
                    'advice' => !empty($row['advice']) ? ucwords($row['advice']) : '',
                    'review' => $row['rating'],
                    'date' => date('l d F Y', strtotime($row['ts'])),
                    'time' => date('h:i:s A', strtotime($row['ts'])),
                    'avatar' => (!empty($row['pic']) ? $row['pic'] : 'defaultAvatar.png'),
                );
            }

            $response = array(
                "data" => $resultArray
            );

            // Set the response header to JSON
            header('Content-Type: application/json');

            // Convert array to JSON format and output
            echo json_encode($response, JSON_PRETTY_PRINT);

            // Close the database connection
            $conn->close();
        } catch (Exception $e) {
            // Handle any errors
            handle_error($e);
        }
    }
} else {
    die('ERROR');
}
