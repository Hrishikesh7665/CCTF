<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['platformFeedback']) && isset($_loginInfo['uid'])) {
    $feedbackData = $_POST['platformFeedback'];
    $userID = $_loginInfo['uid'];

    require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";

    $rating = $feedbackData['rating'];
    $feedbackText = $feedbackData['feedback'];
    $improvementText = $feedbackData['improvement'];

    try {
        $rating = intval($rating); // Convert to integer
        $feedbackText = htmlspecialchars($feedbackText);
        $improvementText = htmlspecialchars($improvementText);

        if (!($rating >= 0 && $rating <= 5)) {
            $remark = ["status" => false, "error" => 'Invalid feedback'];
            header('Content-Type: application/json');
            echo json_encode($remark);
            die();
        }

        $sql = "INSERT INTO feedback__platform (userId, feedback, advice, rating, ts) VALUES (?, ?, ?, ?, current_timestamp()) ON DUPLICATE KEY UPDATE feedback = VALUES(feedback), advice = VALUES(advice), rating = VALUES(rating), ts = current_timestamp();";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $userID, $feedbackText, $improvementText, $rating); // Notice the change from "issi" to "isss"

        if ($stmt->execute()) {
            $remark = ["status" => true];
        } else {
            $remark = ["status" => false, "error" => "Sql Error"];
        }
        $stmt->close();
    } catch (Exception $e) {
        handle_error($e);
    }

    header('Content-Type: application/json');
    echo json_encode($remark);
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ratingData']) && isset($_loginInfo['uid'])) {
    $feedbackData = $_POST['ratingData'];
    $userID = $_loginInfo['uid'];

    require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";

    foreach ($feedbackData as $feedback) {
        try {
            $id = $feedback['id'];
            $rating = $feedback['rating'];
            $feedbackText = $feedback['feedback'];

            // Sanitize user input
            $id = intval($id); // Convert to integer
            $rating = intval($rating); // Convert to integer
            $feedbackText = htmlspecialchars($feedbackText);

            if (empty($id) || !($rating >= 0 && $rating <= 5)) {
                $remark = ["status" => false, "error" => 'Invalid feedback'];
                header('Content-Type: application/json');
                echo json_encode($remark);
                die();
            }

            $sql = "INSERT INTO feedback__challenges (challenge_id, userId, feedback, rating, ts) VALUES (?, ?, ?, ?, current_timestamp()) ON DUPLICATE KEY UPDATE feedback = VALUES(feedback), rating = VALUES(rating), ts = current_timestamp();";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiss", $id, $userID, $feedbackText, $rating);

            if ($stmt->execute()) {
                $remark = ["status" => true];
            } else {
                $remark = ["status" => false, "error" => "Sql Error"];
            }
            $stmt->close();
        } catch (Exception $e) {
            handle_error($e);
        }
    }

    header('Content-Type: application/json');
    echo json_encode($remark);
    exit();
}
