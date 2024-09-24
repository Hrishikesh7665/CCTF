<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

// Check if user is logged in
if (!$_loginInfo) {
    header("Location: /");
    exit();
}

$role = $_loginInfo['role'];
$user_id = $_loginInfo['uid'];

require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

// Check if the request method is POST and 'cid' is set
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cid'])) {
    $challenge_id = $_POST['cid'];

    try {
        // Check if user has solved the challenge
        $stmt = $conn->prepare("SELECT s_id FROM scoreboard WHERE user_id = ? AND c_id = ?");
        $stmt->bind_param("ii", $user_id, $challenge_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $isSolved = mysqli_num_rows($result) != 0;

        // Retrieve challenge details and check if user visited the question set for the first time
        $stmt = $conn->prepare("SELECT title, description, score FROM challenges WHERE id = ?");
        $stmt->bind_param("i", $challenge_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result || mysqli_num_rows($result) != 1) {
            die('ERROR');
        }

        $row = $result->fetch_assoc();

        $obj = new stdClass();
        $obj->title = $row['title'];
        $obj->description = $row['description'];
        $obj->score = $row['score'];
        $obj->isSolved = $isSolved;

        // Check if the user visited the question set for the first time
        $stmt = $conn->prepare("SELECT qlog_id, time FROM logs__qs WHERE u_id = ? and c_id = ?");
        $stmt->bind_param("ii", $user_id, $challenge_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $isVisited = mysqli_num_rows($result) != 0;

        if (!$isVisited) {
            // User is visiting the question set for the first time, add an entry
            $stmt = $conn->prepare("INSERT INTO logs__qs (u_id, c_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $challenge_id);
            $stmt->execute();
        }

        echo json_encode($obj);
        exit();
    } catch (Exception $e) {
        handle_error($e);
    }
} else {
    die('ERROR');
}
