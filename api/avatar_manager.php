<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

// Check if user is logged in
if (!$_loginInfo) {
    header("Location: /");
    exit();
}
$userId = $_loginInfo['uid'];
$response = array();

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/img/avatars/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar']['name'])) {

    $uploadedFile = $_FILES['avatar']['tmp_name'];

    // Get the MIME type of the file
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $fileType = finfo_file($fileInfo, $uploadedFile);

    // Validate file type (you can adjust this based on your requirements)
    $allowedImageTypes = array('image/jpeg', 'image/png');

    if (in_array($fileType, $allowedImageTypes)) {
        // Generate a unique file name
        $fileName = uniqid('avatar_') . '.' . pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $destinationFile = $uploadDir . $fileName;

        if (move_uploaded_file($uploadedFile, $destinationFile)) {
            // File successfully uploaded
            require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
            // Update database with image name
            $imageName = $fileName;

            $sql = "UPDATE users SET displayPic = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $imageName, $userId);
            if ($stmt->execute()) {
                $imgPath = '/assets/img/avatars/' . $imageName;
                $_loginInfo['displaypic'] = $imgPath;
                $_SESSION['displaypic'] = $imgPath;
                $response = array('status' => true, 'message' => $imgPath);
            } else {
                // SQL execution failed, delete the file
                unlink($destinationFile);
                $response = array('status' => false, 'message' => 'Error updating database.');
            }
            $stmt->close();
        } else {
            // Error uploading file
            $response = array('status' => false, 'message' => 'Error uploading file.');
        }
    } else {
        // Invalid file type
        $response = array('status' => false, 'message' => 'Invalid file type.');
    }

    // Close the fileinfo resource
    finfo_close($fileInfo);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resetimage'])) {

    $captcha = $_POST['captcha'];
    if (empty($captcha) || !isset($_SESSION['captcha']) || $captcha != $_SESSION['captcha']) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'message' => 'Captcha not valid.'));
        exit();
    }

    require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

    // Check if the user already has a null display picture in the database
    $sql_check_null = "SELECT displayPic FROM users WHERE id = ? AND displayPic IS NULL";
    $stmt_check_null = $conn->prepare($sql_check_null);
    $stmt_check_null->bind_param("i", $userId);
    $stmt_check_null->execute();
    $stmt_check_null->store_result();

    if ($stmt_check_null->num_rows > 0) {
        // The user already has a null display picture, exit the block
        $stmt_check_null->close();
        exit();
    }

    // Close the previous statement before continuing
    $stmt_check_null->close();

    // If the user does not already have a null display picture, proceed to reset it
    // Check if the user has a display picture in the database
    $sql_check = "SELECT displayPic FROM users WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $userId);
    $stmt_check->execute();
    $stmt_check->bind_result($oldImage);
    $stmt_check->fetch();
    $stmt_check->close();

    // Delete the old image if it exists
    if (!empty($oldImage)) {
        $oldImagePath = $_SERVER['DOCUMENT_ROOT'] . '/assets/img/avatars/' . $oldImage;
        if (file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }
    }

    // Set the displayPic field to NULL in the database
    $sql_update = "UPDATE users SET displayPic = NULL WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("i", $userId);

    if ($stmt_update->execute()) {
        $userPic = '/assets/img/avatars/defaultAvatar.png';
        $_loginInfo['displaypic'] = $userPic;
        $_SESSION['displaypic'] = $userPic;
        $response = array('status' => true, 'message' => $userPic);
    } else {
        // SQL execution failed
        $response = array('status' => false, 'message' => 'Error updating database.');
    }

    $stmt_update->close();
}


// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
