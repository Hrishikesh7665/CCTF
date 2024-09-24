<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password']) && isset($_POST['confirmPassword']) && isset($_POST['changePassword'])) {

    require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');
    require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";
    require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";

    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    $passResetData = $_SESSION['passwordResetData'];
    $userEmail = $passResetData['userEmail'];
    $userID = $passResetData['userID'];
    $savedOTP = $passResetData['otp'];
    $exp = $passResetData['exp'];
    $isVerified = $passResetData['isVerified'];
    $currentTimestamp = time() - (10 * 60);

    if (!isset($_SESSION['passwordResetData'])) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Error'));
        exit();
    }

    if ($currentTimestamp > $exp) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Session Old'));
        exit();
    }

    if (!$isVerified) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Error'));
        exit();
    }

    if ($password !== $confirmPassword) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Match failed'));
        exit();
    }

    if (!verifyPasswordComplexity($password)) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Complexity'));
        exit();
    }

    if (!checkPasswordHistory($conn, $userID, $password)) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Reused'));
        exit();
    }

    try {

        $newHashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateSql = "UPDATE users SET password = ? WHERE email = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ss", $newHashedPassword, $userEmail);
        $updateStmt->execute();

        insertLog($conn, $userID, 'Password reset by user');
        $remark = ['status' => true, 'message' => 'successful'];
        session_unset();
    } catch (Exception $e) {
        // Handle exception
        handle_error($e);
    }


    // header('Content-Type: application/json');
    echo json_encode($remark);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp']) && isset($_POST['verifyOTP'])) {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');
    $otp = $_POST['otp'];
    $passResetData = $_SESSION['passwordResetData'];
    $savedOTP = $passResetData['otp'];
    $exp = $passResetData['exp'];
    $isVerified = $passResetData['isVerified'];
    $currentTimestamp = time();

    if (!isset($_SESSION['passwordResetData'])) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Error'));
        exit();
    }

    if (empty($otp) || $otp != $savedOTP) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Invalid'));
        exit();
    } elseif ($isVerified || $currentTimestamp > $exp) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Expired'));
        exit();
    } elseif ($otp === $savedOTP) {
        $passResetData['isVerified'] = true;
        $_SESSION['passwordResetData'] = $passResetData;
        header('Content-Type: application/json');
        echo json_encode(array('status' => true));
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['captcha']) && isset($_POST['email']) && isset($_POST['generateOTP'])) {

    require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

    $captcha = $_POST['captcha'];
    $userEmail = $_POST['email'];

    if (empty($captcha) || !isset($_SESSION['captcha']) || $captcha != $_SESSION['captcha']) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Invalid Captcha'));
        exit();
    }

    if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Invalid Email'));
        exit();
    }

    require_once $_SERVER["DOCUMENT_ROOT"] . "/template/allEmailTemplate.php";
    require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";
    require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";

    try {
        // Retrieve user information
        $stmt = $conn->prepare("SELECT `id`, `name`, `email`, `status`, `auth_type` FROM `users` WHERE `email` = ?");
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            // Check if user status is true
            if ($row['status'] == 'true' && $row['auth_type'] == 'self') {
                // Call the stored procedure to check OTP generation possibility
                $stmt = $conn->prepare("CALL CheckOTPGenerationPossibility(?, @isPossible, @errorMessage)");
                $stmt->bind_param("i", $row['id']);
                $stmt->execute();

                // Retrieve the output parameters from the stored procedure
                $stmt = $conn->prepare("SELECT @isPossible, @errorMessage");
                $stmt->execute();
                $result = $stmt->get_result();
                $row_procedure = $result->fetch_assoc();
                $isPossible = $row_procedure['@isPossible'];
                $errorMessage = $row_procedure['@errorMessage'];

                // Proceed if OTP generation is possible
                if ($isPossible) {
                    // Generate OTP
                    $code = sprintf("%06d", rand(0, 999999));
                    $body = generatePassRecoveryEmail($platformLink, $row['name'], $code);
                    sendEmail($userEmail, $row['name'], 'Reset Your CDAC-K CTF Platform Password: Use One Time Password (OTP) for Account Recovery', true, $body);

                    // Insert data into otp_log table
                    $stmt = $conn->prepare("INSERT INTO logs__otp (user_Id, otp) VALUES (?, ?)");
                    $stmt->bind_param("is", $otpUserId, $code);
                    $otpUserId = $row['id'];
                    $stmt->execute();

                    // Set session data
                    session_unset();
                    $passResetData = array(
                        'userID' => $row['id'],
                        'userFullName' => $row['name'],
                        'userEmail' => $userEmail,
                        'otp' => $code,
                        'exp' => $newTimestamp = time() + (5 * 60),
                        'isVerified' => false
                    );
                    $_SESSION['passwordResetData'] = $passResetData;

                    // Success message
                    $remark = ["status" => true, "message" => 'Mail sent'];
                } else {
                    // Error message from stored procedure
                    $remark = ["status" => false, 'error' => 'limit', "message" => $errorMessage];
                }
            } else {
                // Account type error or status is not true
                $remark = ["status" => false, 'error' => 'Invalid Account Type or Status'];
            }
        } else {
            // User not found
            $remark = ["status" => false, 'error' => 'Not Found'];
        }
        $stmt->close();
    } catch (Exception $e) {
        // Handle exception
        handle_error($e);
    }


    header('Content-Type: application/json');
    echo json_encode($remark);
    exit();
}
