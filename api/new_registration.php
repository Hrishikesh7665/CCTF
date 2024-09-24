<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['captcha']) && isset($_POST['password']) && isset($_POST['registerNewLdapUser'])) {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

    if (isset($_SESSION['registrationData']) && isset($_SESSION['registrationAction'])) {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";

        $keysToCheck = array('userFullName', 'userEmail', 'userPhoneNumber', 'userProfession', 'userCenter', 'exp');
        $allKeysExist = allKeysExistInRegistrationData($keysToCheck, $_SESSION['registrationData']);

        if ($_SESSION['registrationAction'] === 'ask-ldapPassword' && $allKeysExist) {

            $captcha = $_POST['captcha'];
            if (empty($captcha) || !isset($_SESSION['captcha']) || $captcha != $_SESSION['captcha']) {
                header('Content-Type: application/json');
                echo json_encode(array('status' => false, 'error' => 'Invalid Captcha', 'message' => 'Captcha not valid.'));
                exit();
            }

            if (time() > $_SESSION['registrationData']['exp']) {
                header('Content-Type: application/json');
                echo json_encode(array('status' => false, 'error' => 'Session Expired!!', 'message' => 'Session Expired, Please Try Again.'));
                exit();
            }

            require_once $_SERVER["DOCUMENT_ROOT"] . "/template/allEmailTemplate.php";
            require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";

            $userEmail = $_SESSION['registrationData']['userEmail'];
            $userPassword = $_POST['password'];

            $ldapStatus = loginLDAP($userEmail, $userPassword);

            if ($ldapStatus['status']) {
                $signupStatus = signupUser($_SESSION['registrationData']['userFullName'], $_SESSION['registrationData']['userEmail'], $_SESSION['registrationData']['userProfession'], $_SESSION['registrationData']['userDesignation'], $_SESSION['registrationData']['userPhoneNumber'], $_SESSION['registrationData']['userCenter'], null, false, false);
                if ($signupStatus) {
                    $body = generateWelcomeEmail($platformLink, $_SESSION['registrationData']['userFullName']);
                    sendEmail($_SESSION['registrationData']['userEmail'], $_SESSION['registrationData']['userFullName'], 'Welcome ' . $_SESSION['registrationData']['userFullName'] . ', to CDAC-K CTF', true, $body);
                    session_unset();
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => true, 'message' => 'Successfully registered'));
                    exit();
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => false, 'error' => 'Failed to register', 'message' => 'Error while registering. Please try again'));
                    exit();
                }
            } elseif (!$ldapStatus['status'] && $ldapStatus['message'] === 'LDAP authentication failed') {
                header('Content-Type: application/json');
                echo json_encode(array('status' => false, 'error' => 'Wrong Password', 'message' => 'LDAP authentication failed. Please try again'));
                exit();
            } elseif (!$ldapStatus['status'] && $ldapStatus['message'] === 'LDAP connection failed') {
                header('Content-Type: application/json');
                echo json_encode(array('status' => false, 'error' => 'LDAP Connection Error!!', 'message' => 'LDAP service is not work properly. Please try again latter'));
                exit();
            }
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['captcha']) && isset($_POST['userFullName']) && isset($_POST['userEmail']) && isset($_POST['userPhoneNumber']) && isset($_POST['userProfession']) && isset($_POST['userCenter']) && isset($_POST['checkOnly'])) {

    require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

    $captcha = $_POST['captcha'];
    if (empty($captcha) || !isset($_SESSION['captcha']) || $captcha != $_SESSION['captcha']) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Invalid Captcha', 'message' => 'Captcha not valid.'));
        exit();
    }

    require_once $_SERVER["DOCUMENT_ROOT"] . "/template/allEmailTemplate.php";
    require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";
    require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";


    $userFullName = $_POST['userFullName'];
    $userEmail = $_POST['userEmail'];
    $userPhoneNumber = $_POST['userPhoneNumber'];
    $userProfession = $_POST['userProfession'];
    $userCenter = $_POST['userCenter'];

    if ($userProfession == 'employee') {
        $userDesignation = $_POST['userDesignation'];
        $userPassword = null;
        $regex_pattern = '/@cdac\.in$/';
    } elseif ($userProfession == 'student') {
        $userDesignation = null;
        $userPassword = $_POST['userPassword'];
        $regex_pattern = '/@(?:google|gmail|yahoo|outlook|hotmail|rediffmail|icloud|protonmail|live)\.com/';
    }

    if ($userFullName === '' || strlen($userFullName) < 4 || strlen($userFullName) > 40 || !preg_match('/^[a-zA-Z. ]{4,}$/', $userFullName)) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Invalid Username', 'message' => 'Please enter a valid username, and please try again.'));
        exit();
    }

    if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL) || !preg_match($regex_pattern, $userEmail)) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Invalid Email Address', 'message' => 'Invalid email address. Please try again with valid email.'));
        exit();
    }

    if ($userPassword != null) {
        if (!verifyPasswordComplexity($userPassword)) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => false, 'error' => 'Weak Password', 'message' => 'The password complexity doesn\'t fulfill. Please try again with a different password.'));
            exit();
        }
    }

    //phone number already in used or not
    $phExists = phoneExists($userPhoneNumber);
    if (!$phExists['status']) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Phone Number Exists', 'message' => 'Phone number already in use. Please try again with a different phone number.'));
        exit();
    }

    $mailExists = emailExists($userEmail);

    if ($mailExists['status']) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => 'Email Address Exists', 'message' => 'Email already in use. Please try again with a different email address.'));
        exit();
    }

    if ($userProfession == 'employee') {
        session_unset();

        // Store values in an array
        $registrationData = array(
            'userFullName' => $userFullName,
            'userEmail' => $userEmail,
            'userPhoneNumber' => $userPhoneNumber,
            'userProfession' => $userProfession,
            'userCenter' => $userCenter,
            'userDesignation' => $userDesignation,
            'exp' => $newTimestamp = time() + (2 * 60),
        );

        // Store the array in a session variable
        $_SESSION['registrationData'] = $registrationData;
        $_SESSION['registrationAction'] = 'ask-ldapPassword';
        // $expiration_time = time() + 300;
        // session_set_cookie_params($expiration_time);

        // Make sure to save the session data
        // session_write_close();

        header('Content-Type: application/json');
        echo json_encode(array('status' => true, 'message' => 'Employee Successfully Checked'));
        exit();
    } elseif ($userProfession == 'student') {
        session_unset();

        $securityToken = generateAndHashSecurityToken();
        $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO `temp__registration` (`name`, `email`, `password`, `phoneNumber`, `special_key`, `location`) VALUES (?, ?, ?, ?, ?, (SELECT center_id FROM list__center WHERE center = ?))";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $userFullName, $userEmail, $hashedPassword, $userPhoneNumber, $securityToken, $userCenter);

            if ($stmt->execute()) {
                $remark = ["status" => true];
            } else {
                $remark = ["status" => false];
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $remark = ["status" => false, "message" => "Duplicate key violation occurred"];
            } else {
                handle_error($e);
            }
        } catch (Exception $e) {
            // Handle other exceptions
            handle_error($e);
        }

        if ($remark['status']) {
            $activationLink = 'registration/verifyEmail?email=' . $userEmail . '&token=' . $securityToken;
            $body = generateAccountActiveEmail($platformLink, $userFullName, $activationLink);
            sendEmail($userEmail, $userFullName, 'Verification Email From CDAC-K CTF', true, $body);
            $msg = 'Email Sent';
        } else {
            $msg = 'Email Not Sent';
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => $remark['status'], 'message' => $msg]);
        exit();
    }
}
