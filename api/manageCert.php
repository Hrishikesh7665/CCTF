<?php
set_time_limit(3600); // 60 minutes


require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

// Check if user is logged in
if (!$_loginInfo) {
    header("Location: /");
    exit();
}

function generateCertNumber($rank)
{
    global $currentCTF;
    $incrementPart = str_pad($rank, 4, '0', STR_PAD_LEFT);
    return "CCTF/{$currentCTF}{$incrementPart}";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";
    require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";

    // Check Captcha
    if (!isset($_POST['captcha']) || empty($_POST['captcha']) || !isset($_SESSION['captcha']) || $_POST['captcha'] !== $_SESSION['captcha']) {
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'error' => 'Invalid Captcha', 'message' => 'Captcha not valid.']);
        exit();
    }

    $action = $_POST['action'];

    if ($action === 'emailCertificate' && isset($_POST['userName'], $_POST['userEmail'], $_POST['userCertificate'])) {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/template/allEmailTemplate.php";
        if (!validateCertificateRequest($_POST['userCertificate'], $_POST['userEmail'])) {
            http_response_code(403);
            exit();
        }

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $_POST['userCertificate'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'error' => 'File Error', 'message' => 'Certificate Not Found.']);
            exit();
        }

        $body = generateCertificateEmail($platformLink, $_POST['userName']);
        sendEmail($_POST['userEmail'], $_POST['userName'], 'Certificate From CDAC-K CTF', true, $body, $_SERVER['DOCUMENT_ROOT'] . $_POST['userCertificate']);
        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'message' => 'Email Successfully Sent']);
        exit();
    }

    if ($action === 'generateCertificates') {
        $sql = "
            SELECT ROW_NUMBER() OVER(ORDER BY sscore DESC, ti ASC, u.id ASC) AS rank, 
                MAX(sb.ts) AS ti, 
                u.id AS user_id, 
                u.email, 
                u.profession, 
                u.phoneNumber AS phone, 
                ld.designation, 
                lc.center, 
                u.name, 
                u.status, 
                COUNT(sb.c_id) AS solved, 
                SUM(ch.score) AS sscore 
            FROM users u 
            JOIN scoreboard sb ON sb.user_id = u.id 
            JOIN challenges ch ON sb.c_id = ch.id 
            LEFT JOIN list__designation ld ON u.designation = ld.designation_id
            LEFT JOIN list__center lc ON u.location = lc.center_id
            GROUP BY u.id, u.email 
            ORDER BY sscore DESC, ti ASC, u.id ASC";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $conn->close();

            $archiveDb = new ArchiveDbConnection();

            try {
                $archiveDb->createTableFromTemplate($currentCTF);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'error' => 'Table Creation Error', 'message' => 'Table Already Exists']);
                $archiveDb->closeConnection();
                exit();
            }

            $archiveConn = $archiveDb->getConnection(); // Get the connection only once and keep it open

            foreach ($rows as $row) {
                $certNumber = generateCertNumber($row['rank']);

                $sql = "INSERT INTO `$currentCTF` (name, email, profession, designation, phoneNumber, location, rank, score, qs_solved, cert_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $archiveConn->prepare($sql);

                if ($stmt === false) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => false, 'error' => 'Error in prepared statement']);
                    exit();
                }

                $stmt->bind_param(
                    'ssssssssis',
                    $row['name'],
                    $row['email'],
                    $row['profession'],
                    $row['designation'],
                    $row['phone'],
                    $row['center'],
                    $row['rank'],
                    $row['sscore'],
                    $row['solved'],
                    $certNumber
                );

                if ($stmt->execute() === false) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => false, 'error' => 'Error in statement execution']);
                    exit();
                }

                $stmt->close();

                if ($row['rank'] > 3) {
                    require_once $_SERVER['DOCUMENT_ROOT'] . '/assets/library/vendor/autoload.php';
                    // \PhpOffice\PhpWord\Autoloader::register();

                    $inputFilename = $_SERVER['DOCUMENT_ROOT'] . '/assets/Certificates Templates/Certificate.docx';
                    $outputFilename = $_SERVER['DOCUMENT_ROOT'] . '/TempCert_' . $row['email'] . '_' . time() . '.docx';
                    $outputDir = $_SERVER['DOCUMENT_ROOT'] . '/certificates/' . $currentCTF;

                    $date = formatDate($currentCTF);

                    $placeholders = [
                        'Name' => ucwords(strtolower($row['name'])),
                        'Date' => $date,
                        'CERT_NO' => $certNumber
                    ];

                    replacePlaceholdersInWordDoc($inputFilename, $outputFilename, $placeholders);
                    $certStatus = wordToPdf($outputFilename, 'ParticipationCertificate_' . $row['email'] . '.pdf', $outputDir);
                    unlink($outputFilename);

                    if ($certStatus['status']) {
                        echo "Certificate generated successfully for user: " . $row['name'] . ' ' . $row['email'] . "<br>";
                    }
                }
            }

            $archiveDb->closeConnection(); // Close the connection after all operations

            if (count($rows) >= 3) {
                header('Content-Type: application/json');
                echo json_encode(['status' => true, 'message' => 'Data Entered successfully but no certificates to generate.']);
                exit();
            }

            header('Content-Type: application/json');
            echo json_encode(['status' => true, 'message' => 'Participation Certificate Generated Successfully']);
            exit();
        } else {
            $conn->close();
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'error' => 'No Scoreboard Data', 'message' => 'No Certificate To Generate.']);
            exit();
        }
    }
}

function validateCertificateRequest($certificatePath, $email)
{
    // Break down the path into components
    $certificateComponents = explode("/", trim($certificatePath, '/'));
    
    // Check if the path contains exactly 3 components: "certificates", a dynamic folder name, and the file name
    if (count($certificateComponents) !== 3 || $certificateComponents[0] !== 'certificates') {
        return false;
    }

    // Extract the file name and split it by underscore
    $fileNameComponents = explode("_", $certificateComponents[2]);
    
    // Check if the file name is in the expected format
    if (count($fileNameComponents) < 2) {
        return false;
    }

    // Check if the file name ends with the email and '.pdf'
    $expectedFileName = $email . '.pdf';
    if (!str_ends_with($fileNameComponents[1], $expectedFileName)) {
        return false;
    }
    return true;
}
