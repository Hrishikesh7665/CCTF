<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(404);
    include($_SERVER["DOCUMENT_ROOT"] . "/404.html");
    exit();
}

session_name("secure_session");

$page = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);


// Check if HTTPS is being used
$https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

// Start session with appropriate cookie settings
session_start([
    'cookie_lifetime' => 0,
    'cookie_httponly' => $https,
    'cookie_secure' => $https, // Set to true if HTTPS is being used
    'cookie_samesite' => 'Lax',
]);


$session_timeout = 3600; // Session Timeout for inactivity


if (isset($_SESSION['username']) && isset($_SESSION['email']) && isset($_SESSION['uid']) && isset($_SESSION['role']) && isset($_SESSION['tshash']) && isset($_SESSION['displaypic']) && isset($_SESSION['auth'])) {
    $_loginInfo = array(
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'uid' => $_SESSION['uid'],
        'role' => ucwords($_SESSION['role']),
        'tshash' => $_SESSION['tshash'],
        'displaypic' => $_SESSION['displaypic'],
        'profession' => $_SESSION['profession'],
        'designation' => $_SESSION['designation'],
        'location' => $_SESSION['location'],
        'phone' => $_SESSION['phone'],
        'auth' => $_SESSION['auth'],
        'baseFolder' => ($_SESSION['role'] == 'Admin') ? 'admin-zone' : 'user-zone'
    );
} else {
    $_loginInfo = false;
}


function setCustomSession($username, $email, $uid, $role, $tshash, $profession, $designation, $location, $phone, $displaypic, $auth)
{
    global $_loginInfo;
    // Set session values
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $_SESSION['uid'] = $uid;
    $_SESSION['role'] = ucwords($role);
    $_SESSION['tshash'] = $tshash;
    $_SESSION['displaypic'] = $displaypic;
    $_SESSION['profession'] = $profession;
    $_SESSION['designation'] = $designation;
    $_SESSION['location'] = $location;
    $_SESSION['phone'] = $phone;
    $_SESSION['auth'] = $auth;
    $_SESSION['agent'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['last_activity'] = time();
    $_loginInfo = array(
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'uid' => $_SESSION['uid'],
        'role' => ucwords($_SESSION['role']),
        'tshash' => $_SESSION['tshash'],
        'displaypic' => $_SESSION['displaypic'],
        'profession' => $_SESSION['profession'],
        'designation' => $_SESSION['designation'],
        'location' => $_SESSION['location'],
        'phone' => $_SESSION['phone'],
        'auth' => $_SESSION['auth'],
        'baseFolder' => ($_SESSION['role'] == 'Admin') ? 'admin-zone' : 'user-zone'
    );
    return true;
}

// && $_SESSION['agent'] !== $_SERVER['HTTP_USER_AGENT']

if (isset($_SESSION['auth'])) {
    if (isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];
        if ($inactive_time > $session_timeout) {
            // Unset all session values
            $_SESSION = array();
            $_loginInfo = array();

            // Destroy the session cookie
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }

            // Destroy the session
            session_destroy();

            $conn->close();

            header("Location: /");
            die();
        }
    }
    session_regenerate_id();

    if ($_SESSION['agent'] === $_SERVER['HTTP_USER_AGENT']) {
    } else {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";
        require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";

        insertLog($conn, $_SESSION['uid'], 'Login attempted with cookie');

        // Unset all session values
        $_SESSION = array();
        $_loginInfo = array();

        // Destroy the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destroy the session
        session_destroy();

        $conn->close();

        header("Location: /");
        die();
    }
}
