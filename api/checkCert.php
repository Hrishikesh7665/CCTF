<?php
header('Content-Type: application/json');

// Retrieve the raw POST data
$rawData = file_get_contents("php://input");

// Decode the JSON data
$data = json_decode($rawData, true);

function isValidCertificateNumber($input)
{
    // Trim the input to remove surrounding whitespace and quotes
    $input = trim($input);
    $input = preg_replace('/^["\']+|["\']+$/', '', $input);

    // Define the regular expression pattern
    $pattern = '/^CCTF\/\d{10}$/i';

    // Test the input against the pattern and return the result
    return preg_match($pattern, $input);
}

function getOrdinalSuffix($number) {
    if (!in_array(($number % 100), array(11, 12, 13))){
        switch ($number % 10) {
            case 1:  return 'st';
            case 2:  return 'nd';
            case 3:  return 'rd';
        }
    }
    return 'th';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($data['certNo'])) {
    $certNo = trim($data['certNo']);
    if (isValidCertificateNumber($certNo)) {

        require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";
        require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";

        $archiveDb = new ArchiveDbConnection();
        $archiveConn = $archiveDb->getConnection();
        $table_name = substr($certNo, strpos($certNo, '/') + 1, 6);


        // Prepare the statement
        $stmt = $archiveConn->prepare("SELECT `name`,`email`,`rank`,`cert_no`,`issuedDate` FROM `$table_name` WHERE `cert_no` = ?");

        if ($stmt !== false) {
            // Bind parameters
            $stmt->bind_param('s', $certNo);

            // Execute the statement
            if ($stmt->execute()) {
                // Get the result
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Fetch all rows
                    $rows = $result->fetch_all(MYSQLI_ASSOC);
                    $rank = $rows[0]['rank'];
                    $compileData = [
                        'certStatus' => 'Verified',
                        'certNo' => $rows[0]['cert_no'],
                        'name' => $rows[0]['name'],
                        'email' => $rows[0]['email'],
                        'rank' => $rank . getOrdinalSuffix($rank),
                        'ctf_date' => formatDate($table_name),
                        'issuedDate' => (new DateTime($rows[0]['issuedDate']))->format('jS F Y'),
                    ];
                    // Output the results as JSON
                    echo json_encode(array('status' => true, 'message' => 'Certificate number received', 'data' => $compileData));
                } else {
                    // No rows found
                    echo json_encode(array('status' => false, 'message' => 'No records found'));
                }

                // Close the statement
                $stmt->close();
            } else {
                // Error executing statement
                echo json_encode(array('status' => false, 'message' => 'DB Error Occurred'));
            }
            // Close the connection
            $archiveConn->close();
        } else {
            // Error preparing statement
            echo json_encode(array('status' => false, 'message' => 'DB Error Occurred'));
        }

    } else {
        echo json_encode(array('status' => false, 'message' => 'Invalid certificate number'));
        exit();
    }
} else {
    echo json_encode(array('status' => false, 'message' => 'Invalid request'));
    exit();
}
