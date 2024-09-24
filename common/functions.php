<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(404);
    include($_SERVER["DOCUMENT_ROOT"] . "/404.html");
    exit();
}

define('AES_256_CBC', 'aes-256-cbc');

function formatDate($input)
{
    return DateTime::createFromFormat('dmY', substr($input, 0, 2) . substr($input, 2, 2) . '20' . substr($input, 4, 2))->format('jS F Y');
}

// Function to sanitize and validate input data
function validateInput($data)
{
	global $conn;
	$data = htmlspecialchars(trim($data));
	return mysqli_real_escape_string($conn, $data);
}

function replacePlaceholdersInWordDoc($inputFilename, $outputFilename, $placeholders)
{
    // Create new PhpWord instance
    $phpWord = new \PhpOffice\PhpWord\PhpWord();

    // Load the template document
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($inputFilename);

    // Replace placeholders with values
    foreach ($placeholders as $placeholder => $value) {
        $templateProcessor->setValue($placeholder, $value);
    }

    // Save the modified document
    $templateProcessor->saveAs($outputFilename);
}

function wordToPdf($wordFilePath, $pdfFilePath, $outputDir, $bin = 'soffice', $overwrite = true)
{
    if (!file_exists($wordFilePath)) {
        return array('status' => false, 'message' => 'File does not exist at ' . $wordFilePath);
    }

    if ($overwrite) {
        if (!file_exists($outputDir) || !is_dir($outputDir)) {
            return array('status' => false, 'message' => $outputDir . ' Folder does not exist');
        }
    }

    $command = $bin . ' --headless --convert-to pdf:writer_pdf_Export "' . $wordFilePath . '" --outdir "' . $outputDir . '"';
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // If the system is Windows, replace backslashes with double backslashes
        $command = str_replace('\\', '\\\\', $command);
    }else if (strtoupper(PHP_OS) === 'LINUX') {
        $command =  'export HOME=/tmp && ' . $command . ' 2>&1';
    }

    $output = exec($command);
    $words = explode(" ", $output);
    $first_word = $words[0];
    if ($first_word === 'Overwriting:' || $first_word === 'convert') {
        // Move the generated PDF to the desired location
        $sourcePdf = rtrim($outputDir, '/') . '/' . pathinfo($wordFilePath, PATHINFO_FILENAME) . '.pdf';
        $targetPdf = rtrim($outputDir, '/') . '/' . $pdfFilePath;
        rename($sourcePdf, $targetPdf);
        return array('status' => true, 'message' => 'Conversion successful.');
    }
    return array('status' => false, 'message' => 'Conversion failed.');
}

// Function to verify password and check recent password changes
function checkPasswordHistory($conn, $userId, $password)
{
    // Prepare and execute query to fetch user's current password
    $stmt = $conn->prepare("SELECT `password` FROM `users` WHERE `id` = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify if the provided password matches the stored current password
    if ($user && password_verify($password, $user['password'])) {
        // Password matches current password
        $stmt->close();
        return false;
    }

    // Prepare and execute query to fetch recent password changes
    $query = "SELECT `old_value`, `new_value` FROM `logs__user_activity` WHERE `user_id` = ? AND `field_name` = 'password' ORDER BY `updated_at` DESC LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the provided password matches any of the recent password changes
    while ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['new_value']) || password_verify($password, $row['old_value'])) {
            // Password matches a recent password change
            $stmt->close();
            return false;
        }
    }

    // Password doesn't match current or recent passwords
    $stmt->close();
    return true;
}

// Example usage:
// $userId = 1;
// $password = "examplePassword";
// if (verifyPassword($userId, $password)) {
// echo "Password verification successful.";
// } else {
// echo "Password verification failed.";
// }


function generateAndHashSecurityToken($length = 32)
{
    $token = bin2hex(random_bytes($length));
    $token .= time();
    $hashedToken = hash('sha256', $token);
    return $hashedToken;
}

function isValidHashFormat($input)
{
    // Regular expression for SHA-256 hash
    $pattern = '/^[a-f0-9]{64}$/i'; // 64 characters hexadecimal string

    if (preg_match($pattern, $input)) {
        return true; // Valid hash format
    } else {
        return false; // Invalid hash format
    }
}

// Send Email Function
function sendEmail($toEmail, $toName, $subject, $isHTML, $body, $file = false)
{
    require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . '/assets/library/vendor/autoload.php';

    $mail = new \PHPMailer\PHPMailer\PHPMailer(); // Create an instance of PHPMailer
    $smtp = new SecureSMTP(); // Create an instance of SecureSMTP

    $smtp->configureMailer($mail); // Configure the mailer with SMTP settings

    // Email settings
    $mail->addAddress($toEmail, $toName); // Add recipient
    $mail->Subject = $subject;

    if ($isHTML) {
        $mail->isHTML(true); // Set email format to HTML
        $mail->Body = $body; // HTML message body
    } else {
        $mail->Body = $body; // Plain text message body
        $mail->AltBody = $body; // Alternate plain text for non-HTML mail clients
    }

    if ($file != false) {
        $mail->addAttachment($file); // Add attachment if provided
    }

    // Send the email
    if (!$mail->send()) {
        echo 'Message could not be sent. ' . $mail->ErrorInfo;
        die();
    }
}

// Verify password complexity
function verifyPasswordComplexity($password)
{
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

    if (preg_match($pattern, $password)) {
        return true;
    } else {
        return false;
    }
}

// Fetch User Activity Timeline
function getUserActivityTimeline($user_id)
{
    global $conn;

    try {
        // Set the user variable
        $sql = "SET @user_id := ?;";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Query to fetch data
        $sql = "";
        $sql .= "SELECT ";
        $sql .= "        activity_type, ";
        $sql .= "        timestamp, ";
        $sql .= "        detail, ";
        $sql .= "        points_gained, ";
        $sql .= "        total_score ";
        $sql .= "    FROM ( ";
        $sql .= "        SELECT ";
        $sql .= "            'New User Added' AS activity_type, ";
        $sql .= "            creation_ts AS timestamp, ";
        $sql .= "            CONCAT('New user added with ID: ', id) AS detail, ";
        $sql .= "            NULL AS points_gained, ";
        $sql .= "            NULL AS total_score ";
        $sql .= "        FROM ";
        $sql .= "            users ";
        $sql .= "        WHERE ";
        $sql .= "            id = @user_id ";
        $sql .= "            AND NOT EXISTS ( ";
        $sql .= "                SELECT 1 ";
        $sql .= "                FROM logs__auth ";
        $sql .= "                WHERE users_id = @user_id ";
        $sql .= "                    AND remark = 'New User Added' ";
        $sql .= "            ) ";
        $sql .= "        UNION ALL ";
        $sql .= "        SELECT ";
        $sql .= "            CASE ";
        $sql .= "                WHEN remark = 'Login Success' THEN 'Login Success' ";
        $sql .= "                WHEN remark LIKE 'Invalid login Attempt%' THEN 'Invalid Login Attempt' ";
        $sql .= "                WHEN remark = 'Login Attempted While User Not Activated' THEN 'Login Attempted While User Not Activated' ";
        $sql .= "                ELSE 'Login Attempted' ";
        $sql .= "            END AS activity_type, ";
        $sql .= "            time_stamp AS timestamp, ";
        $sql .= "            CASE ";
        $sql .= "                WHEN remark = 'Login Attempted While User Not Activated' THEN 'User not activated' ";
        $sql .= "                ELSE remark ";
        $sql .= "            END AS detail, ";
        $sql .= "            NULL AS points_gained, ";
        $sql .= "            NULL AS total_score ";
        $sql .= "        FROM ";
        $sql .= "            logs__auth ";
        $sql .= "        WHERE ";
        $sql .= "            users_id = @user_id ";
        $sql .= "        UNION ALL ";
        // $sql .= "        SELECT ";
        // $sql .= "            'Password changed by user' AS activity_type, ";
        // $sql .= "            time_stamp AS timestamp, ";
        // $sql .= "            'Password changed by user' AS detail, ";
        // $sql .= "            NULL AS points_gained, ";
        // $sql .= "            NULL AS total_score ";
        // $sql .= "        FROM ";
        // $sql .= "            logs__auth ";
        // $sql .= "        WHERE ";
        // $sql .= "            users_id = @user_id ";
        // $sql .= "                AND remark = 'Password changed by user' ";
        // $sql .= "        UNION ALL ";
        //add by me
        // $sql .= "        SELECT ";
        // $sql .= "            'Password reset by user' AS activity_type, ";
        // $sql .= "            time_stamp AS timestamp, ";
        // $sql .= "            'Password reset by user' AS detail, ";
        // $sql .= "            NULL AS points_gained, ";
        // $sql .= "            NULL AS total_score ";
        // $sql .= "        FROM ";
        // $sql .= "            logs__auth ";
        // $sql .= "        WHERE ";
        // $sql .= "            users_id = @user_id ";
        // $sql .= "                AND remark = 'Password reset by user' ";
        // $sql .= "        UNION ALL ";
        //add by me end
        $sql .= "        SELECT ";
        $sql .= "            'Invalid password change attempt by user' AS activity_type, ";
        $sql .= "            time_stamp AS timestamp, ";
        $sql .= "            'Invalid password change attempt by user' AS detail, ";
        $sql .= "            NULL AS points_gained, ";
        $sql .= "            NULL AS total_score ";
        $sql .= "        FROM ";
        $sql .= "            logs__auth ";
        $sql .= "        WHERE ";
        $sql .= "            users_id = @user_id ";
        $sql .= "                AND remark LIKE 'Invalid password change attempt by user%' ";
        $sql .= "        UNION ALL ";
        $sql .= "        SELECT ";
        $sql .= "            'Correct flag submitted' AS activity_type, ";
        $sql .= "            time AS timestamp, ";
        $sql .= "            CONCAT('Challenge: ', challenges.title, ', Category: ', category.name) AS detail, ";
        $sql .= "            challenges.score AS points_gained, ";
        $sql .= "            COALESCE(( ";
        $sql .= "                SELECT SUM(challenges.score) ";
        $sql .= "                FROM logs__flag ";
        $sql .= "                JOIN challenges ON logs__flag.c_id = challenges.id ";
        $sql .= "                WHERE logs__flag.u_id = @user_id ";
        $sql .= "                    AND logs__flag.flag_status = 1 ";
        $sql .= "                    AND logs__flag.time <= logs__flag_outer.time ";
        $sql .= "            ), 0) AS total_score ";
        $sql .= "        FROM ";
        $sql .= "            logs__flag AS logs__flag_outer ";
        $sql .= "        JOIN challenges ON logs__flag_outer.c_id = challenges.id ";
        $sql .= "        JOIN category ON challenges.cat_id = category.cat_id ";
        $sql .= "        WHERE ";
        $sql .= "            logs__flag_outer.u_id = @user_id ";
        $sql .= "            AND logs__flag_outer.flag_status = 1 ";
        $sql .= "        UNION ALL ";
        $sql .= "        SELECT ";
        $sql .= "            'Wrong flag submitted' AS activity_type, ";
        $sql .= "            time AS timestamp, ";
        $sql .= "            CONCAT('Challenge: ', challenges.title, ', Category: ', category.name) AS detail, ";
        $sql .= "            NULL AS points_gained, ";
        $sql .= "            NULL AS total_score ";
        $sql .= "        FROM ";
        $sql .= "            logs__flag ";
        $sql .= "        JOIN challenges ON logs__flag.c_id = challenges.id ";
        $sql .= "        JOIN category ON challenges.cat_id = category.cat_id ";
        $sql .= "        WHERE ";
        $sql .= "            u_id = @user_id ";
        $sql .= "                AND flag_status = 0 ";
        $sql .= "        UNION ALL ";
        $sql .= "        SELECT ";
        $sql .= "            'Challenge Access' AS activity_type, ";
        $sql .= "            time AS timestamp, ";
        $sql .= "            CONCAT('Visited Challenge: ', challenges.title, ', Category: ', category.name) AS detail, ";
        $sql .= "            NULL AS points_gained, ";
        $sql .= "            NULL AS total_score ";
        $sql .= "        FROM ";
        $sql .= "            logs__qs ";
        $sql .= "        JOIN challenges ON logs__qs.c_id = challenges.id ";
        $sql .= "        JOIN category ON challenges.cat_id = category.cat_id ";
        $sql .= "        WHERE ";
        $sql .= "            u_id = @user_id ";
        $sql .= "        UNION ALL ";
        $sql .= "        SELECT ";
        $sql .= "            'Field Updated' AS activity_type, ";
        $sql .= "            updated_at AS timestamp, ";
        $sql .= "            CONCAT('Field \"', field_name, '\" updated from \"', old_value, '\" to \"', new_value, '\"') AS detail, ";
        $sql .= "            NULL AS points_gained, ";
        $sql .= "            NULL AS total_score ";
        $sql .= "        FROM ";
        $sql .= "            logs__user_activity ";
        $sql .= "        WHERE ";
        $sql .= "            user_id = @user_id ";
        $sql .= "            AND field_name != 'password' ";
        $sql .= "    ) AS combined_activities ";
        $sql .= "    ORDER BY `combined_activities`.`timestamp` ASC;";

        // Execute the query
        $result = $conn->query($sql);

        // Fetch data
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return array('status' => true, 'message' => 'User timeline data was retrieved', 'data' => $data);
    } catch (Exception $e) {
        handle_error($e);
        return array('status' => false, 'message' => 'Error in fetching user timeline data');
    }
}


function fetchScoreboardData()
{
    global $conn;
    $response = array();
    try {
        // Execute SQL query
        // $sql = "SELECT \n"

        //     . "    @a:=@a+1 as rank, \n"

        //     . "    (SELECT ts FROM scoreboard WHERE user_id=sb.user_id ORDER BY ts DESC LIMIT 1) as ti, \n"

        //     . "    u.id as user_id,\n"

        //     . "    u.email as email,\n"

        //     . "    u.name as name, \n"

        //     . "    u.status as status, \n"

        //     . "    COUNT(sb.c_id) as solved, \n"

        //     . "    SUM(ch.score) as sscore \n"

        //     . "FROM \n"

        //     . "    (SELECT @a:= 0) AS a, \n"

        //     . "    users as u, \n"

        //     . "    challenges as ch, \n"

        //     . "    scoreboard as sb \n"

        //     . "WHERE \n"

        //     . "    sb.c_id = ch.id \n"

        //     . "    AND sb.user_id = u.id \n"

        //     . "GROUP BY \n"

        //     . "    sb.user_id, u.id, u.email\n"

        //     . "ORDER BY \n"

        //     . "    sscore DESC, rank ASC;";

        $sql = "SELECT \n"

    . "    ROW_NUMBER() OVER(ORDER BY sscore DESC, ti ASC, u.id ASC) AS rank,\n"

    . "    (SELECT ts FROM scoreboard WHERE user_id = sb.user_id ORDER BY ts DESC LIMIT 1) as ti, \n"

    . "    u.id AS user_id, \n"

    . "    u.email AS email, \n"

    . "    u.name AS name, \n"

    . "    u.status AS status, \n"

    . "    COUNT(sb.c_id) AS solved, \n"

    . "    SUM(ch.score) AS sscore\n"

    . "FROM \n"

    . "    users AS u \n"

    . "JOIN \n"

    . "    scoreboard AS sb \n"

    . "    ON sb.user_id = u.id \n"

    . "JOIN \n"

    . "    challenges AS ch \n"

    . "    ON sb.c_id = ch.id \n"

    . "GROUP BY \n"

    . "    sb.user_id, \n"

    . "    u.id, \n"

    . "    u.email \n"

    . "ORDER BY \n"

    . "    sscore DESC, \n"

    . "    ti ASC, \n"

    . "    u.id ASC;";

        $result = mysqli_query($conn, $sql);

        if (!$result) {
            // If query execution failed
            $response = array('status' => false, 'message' => 'Error during executing query: ' . mysqli_error($conn));
        } else {
            // If query executed successfully
            $count = mysqli_num_rows($result);
            $i = 1;
            $players = array(); // Initialize array to store players' data

            if ($count > 0) {
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                    $player = array(
                        'rank' => $i,
                        'name' => $row['name'],
                        'solved' => $row['solved'],
                        'score' => $row['sscore'],
                        'status' => $row['status'],
                        'email' => $row['email'],
                        'uid' => $row['user_id'],
                        'time' => $row['ti']
                    );

                    // Add player data to the players array
                    $players[] = $player;
                    $i++;
                }
                // Assign players' data to response
                $response = array('status' => true, 'message' => 'Data fetched successfully', 'data' => $players);
            } else {
                // No records found
                $response = array('status' => false, 'message' => 'No records found');
            }
        }
        return $response;
    } catch (Exception $e) {
        handle_error($e);
    }
}


function emailExists($email)
{
    global $conn;
    try {
        $sql = "SELECT email FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = mysqli_num_rows($result);
        return array('status' => $count == 1, 'message' => $count == 1 ? 'Email exists' : 'Email does not exist');
    } catch (Exception $e) {
        handle_error($e);
        return array('status' => false, 'message' => 'Error checking email existence');
    }
}

function phoneExists($phNumber, $userId = null)
{
    global $conn;
    try {
        $sql = "SELECT id, phoneNumber FROM users WHERE phoneNumber = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $phNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($result->num_rows == 1) {
            // Phone number exists in the database
            if ($userId !== null && $row['id'] == $userId) {
                // Phone number belongs to the current user
                return array('status' => true, 'message' => 'Phone number exists and belongs to current user');
            } else {
                // Phone number exists but does not belong to the current user
                return array('status' => false, 'message' => 'Phone number exists but does not belong to current user');
            }
        } else {
            // Phone number does not exist in the database
            return array('status' => true, 'message' => 'Phone number does not exist');
        }
    } catch (Exception $e) {
        handle_error($e);
        return array('status' => false, 'message' => 'Error checking phone number existence');
    }
}


function findArrayElement($array, $email)
{
    foreach ($array as $key => $element) {
        if (isset($element["mail"]["count"]) && $element["mail"]["count"] === 1 && $element["mail"][0] === $email) {
            return $key;
        }
    }
    return false;
}


function insertLog($conn, $userId, $remark)
{
    try {
        if ($remark == 'Login Success') {
            // Generate timestamp with custom suffix
            $currentTs = time() . '+cdac_ctf_timestmap_hash';
            // Generate hash for timestamp
            $tsHash = hash('crc32b', $currentTs);
            $sql = "INSERT INTO logs__auth (users_id, ts_hash, remark) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $userId, $tsHash, $remark);
            $success = $stmt->execute();
            $stmt->close();
            if ($success) {
                return array('status' => true, 'hash' => $tsHash);
            } else {
                return array('status' => false);
            }
        } else {
            $sql = "INSERT INTO logs__auth (users_id, remark) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $userId, $remark);
            $success = $stmt->execute();
            $stmt->close();
        }
        return $success ? true : false;
    } catch (Exception $e) {
        handle_error($e);
    }
}

// Function to change password
function changePass($uid, $password, $newPassword)
{
    global $conn;
    try {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = mysqli_num_rows($result);

        if ($count == 1) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];
            if (password_verify($password, $hashedPassword)) {
                // Passwords match, update the password
                $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateSql = "UPDATE users SET password = ? WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("si", $newHashedPassword, $uid);
                $updateStmt->execute();
                insertLog($conn, $uid, 'Password changed by user');
                return array('status' => true, 'message' => 'Password updated successfully');
            } else {
                // Passwords don't match
                insertLog($conn, $uid, 'Invalid password change attempt by user');
                return array('status' => false, 'message' => 'Current password is incorrect');
            }
        } else {
            // User not found
            return array('status' => false, 'message' => 'User not found');
        }
    } catch (Exception $e) {
        handle_error($e);
        // return array('status' => false, 'message' => 'An error occurred while processing your request');
    }
}


// Function to handle simple user login
function loginSimple($email, $password)
{
    global $conn;
    try {
        $sql = "SELECT `id`, `name`, `email`, `password`, `role`, `status`, `profession`, (SELECT list__designation.designation FROM list__designation WHERE list__designation.designation_id=users.designation) as designation, `phoneNumber`, `displayPic`, `auth_type`, `creation_ts`, (SELECT list__center.center FROM list__center WHERE list__center.center_id=users.location) as location FROM `users` WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = mysqli_num_rows($result);

        if ($count == 1) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];
            if (password_verify($password, $hashedPassword)) {
                if ($row['status'] == 'true') {
                    $logEntry = insertLog($conn, $row['id'], 'Login Success');
                    if ($logEntry['status'] === true) {
                        $user = array(
                            'status' => true,
                            'message' => 'Login Success',
                            'data' => array(
                                'name' => $row['name'],
                                'id' => $row['id'],
                                'role' => $row['role'],
                                'email' => $row['email'],
                                'profession' => $row['profession'],
                                'designation' => $row['designation'],
                                'location' => $row['location'],
                                'phoneNumber' => $row['phoneNumber'],
                                'displayPic' => $row['displayPic'],
                                'auth' => $row['auth_type'],
                                'hash' => $logEntry['hash']
                            )
                        );
                        return $user;
                    } else {
                        return array('status' => false, 'message' => 'Add to log failed');
                    }
                } else {
                    insertLog($conn, $row['id'], 'Login Attempted While User Not Activated');
                    return array('status' => false, 'message' => 'User not active');
                }
            } else {
                insertLog($conn, $row['id'], 'Invalid login Attempt');
                return array('status' => false, 'message' => 'Invalid email or password');
            }
        } else {
            return array('status' => false, 'message' => 'Invalid email or password');
        }
    } catch (Exception $e) {
        handle_error($e);
        return array('status' => false, 'message' => 'Error during login');
    }
}

// Function to handle LDAP user login
function loginLDAP($email, $password)
{
    global $ldap_hostname, $ldapPort, $ldap_protocol, $ldap_rootDN, $ldap_root_password, $ldapBaseDn, $ldap_filter, $ldap_uft8;

    $ldapconn = ldap_connect($ldap_hostname, $ldapPort);
    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, $ldap_protocol);
    ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

    if ($ldap_uft8) {
        $email = mb_convert_encoding($email, 'UTF-8', mb_detect_encoding($email));
        $password = mb_convert_encoding($password, 'UTF-8', mb_detect_encoding($password));
    }

    if ($ldapconn) {
        if (@ldap_bind($ldapconn, $ldap_rootDN, $ldap_root_password)) {
            $searchResults = ldap_search($ldapconn, $ldapBaseDn, $ldap_filter);
            $entries = ldap_get_entries($ldapconn, $searchResults);

            if ($entries['count'] > 0) {
                $empID = findArrayElement($entries, $email);
                $ldapUserDN = $entries[$empID]['dn'];

                if (@ldap_bind($ldapconn, $ldapUserDN, $password)) {
                    $userMail = $entries[$empID]['mail'][0];
                    $givenName = $entries[$empID]['givenname'][0];
                    $ldapOutput = array(
                        "status" => true,
                        "userMail" => $userMail,
                        "givenName" => $givenName
                    );

                    ldap_unbind($ldapconn); // Unbind after successful authentication
                    return $ldapOutput;
                } else {
                    ldap_unbind($ldapconn); // Unbind on authentication failure
                    return array('status' => false, 'message' => 'LDAP authentication failed');
                }
            }
        }

        ldap_unbind($ldapconn); // Unbind if initial bind fails
    }

    return array('status' => false, 'message' => 'LDAP connection failed');
}

// Function to handle user signup
function signupUser($name, $email, $profession, $designation, $phone, $location, $password, $requireHash, $passRequired = true)
{
    global $conn;

    // if (strlen($password) < 5) {
    // return array('status' => false, 'message' => 'Password should be at least 4 characters long');
    // }

    // if (emailExists($email)['status']) {
    // return array('status' => false, 'message' => 'Email already exists');
    // }

    if ($requireHash && $passRequired) {
        $password = password_hash($password, PASSWORD_DEFAULT);
    } else if (!$requireHash && !$passRequired) {
        $password = null;
    }

    if ($profession === 'employee') {
        $authType = 'ldap';
    } elseif ($profession === 'student') {
        $authType = 'self';
    }

    try {
        $sql = "INSERT INTO `users`(`name`, `email`, `password`, `profession`, `designation`, `phoneNumber`, `auth_type`, `location`) VALUES (?, ?, ?, ?, (SELECT designation_id FROM list__designation WHERE designation = ?), ?, ?, (SELECT `center_id` FROM `list__center` WHERE `center` = ?) )";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $name, $email, $password, $profession, $designation, $phone, $authType, $location);

        if ($stmt->execute()) {
            // Get the ID of the newly inserted user
            $newUserId = $stmt->insert_id;
            insertLog($conn, $newUserId, 'New User Added');
            $remark = array("status" => true);
        } else {
            $remark = array("status" => false);
        }
        $stmt->close();
        return $remark;
    } catch (Exception $e) {
        handle_error($e);
        return array('status' => false, 'message' => 'Error during user signup');
    }
}

function getUserStats($conn, $login_user_id)
{
    // Get user stats
    $sql = "SELECT ROW_NUMBER() OVER(ORDER BY sscore DESC, ti ASC, u.id ASC) AS rank, (SELECT ts FROM scoreboard WHERE user_id = sb.user_id ORDER BY ts DESC LIMIT 1) as ti, u.id AS user_id, u.email AS email, u.name AS name, u.status AS status, COUNT(sb.c_id) AS solved, SUM(ch.score) AS sscore FROM users AS u JOIN scoreboard AS sb ON sb.user_id = u.id JOIN challenges AS ch ON sb.c_id = ch.id GROUP BY sb.user_id, u.id, u.email ORDER BY sscore DESC, ti ASC, u.id ASC";
    
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }

    $user_stats = [];
    $z = 0;
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $z++;
        $user_stats[] = $row;
    }

    // Find user's rank, score, and solved challenges
    $user_rank = 0;
    $user_score = 0;
    $user_solve = 0;
    foreach ($user_stats as $i => $row) {
        if ($row['user_id'] == $login_user_id) {
            $user_score = $row['sscore'];
            $user_solve = $row['solved'];
            $user_rank = $i + 1;
            break;
        }
    }

    // Get users count
    $sql = "SELECT COUNT(id) AS u_count FROM users WHERE role = 'user' AND status = 'true'";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $users_count = $row['u_count'];

    // Get challenges count
    $sql = "SELECT COUNT(id) AS ch_count FROM challenges";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $challenges_count = $row['ch_count'];

    return [
        'user_rank' => $user_rank,
        'user_score' => $user_score,
        'user_solve' => $user_solve,
        'users_count' => $users_count,
        'challenges_count' => $challenges_count
    ];
}

function getCDACCenterList($conn)
{
    try {
        $centerList = array();

        // Fetch data from the database
        $query = "SELECT `center` FROM `list__center` ORDER BY `list__center`.`center_id` ASC";
        $result = mysqli_query($conn, $query);

        // Check if the query was successful
        if ($result) {
            // Loop through the result set and populate the array
            while ($row = mysqli_fetch_assoc($result)) {
                $centerList[] = $row['center'];
            }
        } else {
            handle_error(mysqli_error($conn));
        }
    } catch (Exception $e) {
        handle_error($e);
    }

    // Close the result set
    mysqli_free_result($result);

    return $centerList;
}


function getDesignationList($conn)
{
    try {
        $designationList = array();

        // Fetch data from the database
        $query = "SELECT `designation` FROM `list__designation` ORDER BY `list__designation`.`designation_id` ASC;";
        $result = mysqli_query($conn, $query);

        // Check if the query was successful
        if ($result) {
            // Loop through the result set and populate the array
            while ($row = mysqli_fetch_assoc($result)) {
                $designationList[] = $row['designation'];
            }
        } else {
            handle_error(mysqli_error($conn));
        }
    } catch (Exception $e) {
        handle_error($e);
    }

    // Close the result set
    mysqli_free_result($result);

    return $designationList;
}

function getCategoryList($conn)
{
    try {
        $categoryList = array();

        // Fetch data from the database
        $query = "SELECT * FROM category ORDER BY `category`.`cat_id` ASC";
        $result = mysqli_query($conn, $query);

        // Check if the query was successful
        if ($result) {
            // Loop through the result set and populate the array
            while ($row = mysqli_fetch_assoc($result)) {
                $categoryList[] = ["value" => $row['cat_id'], "name" => $row['name']];
            }
        } else {
            handle_error(mysqli_error($conn));
        }

        // Close the result set
        mysqli_free_result($result);
    } catch (Exception $e) {
        handle_error($e);
    }

    return $categoryList;
}

function allKeysExistInRegistrationData($keys, $registrationData)
{
    foreach ($keys as $key) {
        if (!array_key_exists($key, $registrationData)) {
            return false; // If any key is missing, return false
        }
    }
    return true; // All keys exist
}


// Function to encrypt data
function encryptData($data, $encryption_key)
{
    // Generate an initialization vector
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));

    // Encrypt data
    $encrypted = openssl_encrypt($data, AES_256_CBC, $encryption_key, 0, $iv);

    // Append a separator and base64-encoded initialization vector
    return $encrypted . ':' . base64_encode($iv);
}

// Function to decrypt data
function decryptData($encrypted_data, $encryption_key)
{
    // Define cipher method
    $cipher_method = 'AES-256-CBC';

    // Separate encrypted data and base64-encoded initialization vector
    $parts = explode(':', $encrypted_data);
    $encrypted = $parts[0];
    $iv = base64_decode($parts[1]);

    // Ensure the IV is the correct length
    if (strlen($iv) !== openssl_cipher_iv_length($cipher_method)) {
        $iv = str_pad($iv, openssl_cipher_iv_length($cipher_method), "\0");
    }

    // Decrypt data
    $decrypted = openssl_decrypt($encrypted, $cipher_method, $encryption_key, 0, $iv);

    return $decrypted;
}

// Function to fetch notification from notification table based on user id and role
function getNotifications($conn, $savedUID) {
    try {
        $sql = "SELECT n.id, n.title, n.description, n.role, n.activeTime, 
                CASE WHEN l.n_id IS NOT NULL THEN TRUE ELSE FALSE END AS viewed 
                FROM notification n 
                LEFT JOIN logs__notification l ON n.id = l.n_id AND l.u_id = ? 
                WHERE n.state = 'active' 
                AND CURRENT_TIMESTAMP BETWEEN n.activeTime AND n.expiredTime 
                AND JSON_CONTAINS(n.role, JSON_QUOTE((SELECT role FROM users WHERE id = ?))) ORDER BY `n`.`activeTime` DESC;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $savedUID, $savedUID);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Close the statement
        $stmt->close();

        return $result;
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }
}
