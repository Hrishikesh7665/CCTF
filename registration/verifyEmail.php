<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['email']) && isset($_GET['token'])) {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";
    require_once $_SERVER["DOCUMENT_ROOT"] . "/template/allEmailTemplate.php";
    require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";

    $email = $_GET['email'];
    $token = $_GET['token'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL) && isValidHashFormat($token)) {
        try {
            $sql = "SELECT `id`, `name`, `email`, `password`, `phoneNumber`, `creation_ts`, (SELECT list__center.center FROM list__center WHERE list__center.center_id=temp__registration.location) as location FROM `temp__registration` WHERE `email`=? AND `special_key`=?"; // corrected SQL query

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $email, $token);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $remark = ["status" => true];
                } else {
                    $remark = ["status" => false];
                }
            } else {
                $remark = ["status" => false];
            }
            $stmt->close();
            if ($remark['status']) {
                $addUser = signupUser($row['name'], $row['email'], 'student', null, $row['phoneNumber'], $row['location'], $row['password'], false);
                if ($addUser['status']) {
                    $deleteSql = "DELETE FROM `temp__registration` WHERE `id`=?";
                    $deleteStmt = $conn->prepare($deleteSql);
                    $deleteStmt->bind_param("i", $row['id']);
                    if (!$deleteStmt->execute() && $debug_mode) {
                        echo 'Debug: Delete statement executed but not successful';
                        exit();
                    }
                    $body = generateWelcomeEmail($platformLink, $row['name']);
                    sendEmail($row['email'], $row['name'], 'Welcome '.$row['name'].', to CDAC-K CTF', true, $body);
                    $status = 'success';
                }
            } else {
                $status = 'failure';
            }
            require_once $_SERVER["DOCUMENT_ROOT"] . "/template/showStatus.php";
        } catch (Exception $e) {
            handle_error($e);
        }
    }
}
