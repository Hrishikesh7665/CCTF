<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

// Check if user is logged in
if (!$_loginInfo) {
    header("Location: /");
    exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Your SQL query
        $sql = "SELECT u.id, u.name, u.email, u.role, u.status, u.profession, (SELECT designation FROM list__designation WHERE designation_id = u.designation) as designation, u.phoneNumber, u.displayPic, u.auth_type, u.creation_ts, (SELECT center FROM list__center WHERE center_id = u.location) as location, l.time_stamp AS last_login_time FROM users u LEFT JOIN logs__auth l ON u.id = l.users_id WHERE l.log_id = (SELECT MAX(log_id) FROM logs__auth WHERE users_id = u.id)ORDER BY u.id ASC;";

        // Execute the SQL query
        $result = $conn->query($sql);

        // Fetch the result as an associative array
        $resultArray = [];
        while ($row = $result->fetch_assoc()) {
            $resultArray[] = array(
                'id' => (int)($row['id']),
                'full_name' => $row['name'],
                'email' => $row['email'],
                'role' => ucwords($row['role']),
                'profession' => ucwords($row['profession']),
                'designation' => ($row['profession'] == 'student') ? '-' : (($row['profession'] != 'student' && $row['designation'] == null) ? 'Not Defined' : $row['designation']),
                'location' => $row['location'],
                'number' => $row['phoneNumber'],
                'status' => $row['status'],
                'avatar' => (!empty($row['displayPic']) ? $row['displayPic'] : 'defaultAvatar.png'),
                'last_login' => $row['last_login_time']
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
} else {
    // Return an error if the request method is not POST
    die('ERROR');
}
