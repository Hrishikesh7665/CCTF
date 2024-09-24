<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/template/head.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/common/variables.php";

$remark = ["status" => true];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['captcha']) && isset($_POST['action'])) {
    if ($_POST['action'] == 'uploadExcel') {
        $captcha = $_POST["captcha"];
        if (empty($captcha) || !isset($_SESSION['captcha']) || $captcha != $_SESSION['captcha']) {
            $remark = ["status" => false, "type" => "delete", "message" => "Captcha verification failed."];
        } else {
            // File size validation (less than 10MB)
            if ($_FILES['excelFile']['size'] > 10485760) {
                $remark = ["status" => false, "type" => "delete", "message" => "File size exceeds the 10MB limit."];
            } else {
                $fileExtension = strtolower(pathinfo($_FILES['excelFile']['name'], PATHINFO_EXTENSION));
                $validExtensions = ['xls', 'xlsx', 'csv', 'tsv'];

                // File extension validation
                if (!in_array($fileExtension, $validExtensions)) {
                    $remark = ["status" => false, "type" => "delete", "message" => "Invalid file extension."];
                } else {
                    $fileMagicNumber = file_get_contents($_FILES['excelFile']['tmp_name'], false, null, 0, 4);
                    $mimeType = mime_content_type($_FILES['excelFile']['tmp_name']);

                    // MIME type validation
                    $validMimeTypes = [
                        'application/vnd.ms-excel', // xls
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // xlsx
                        'text/csv', // csv
                        'text/tab-separated-values' // tsv
                    ];
                    if (!in_array($mimeType, $validMimeTypes)) {
                        $remark = ["status" => false, "type" => "delete", "message" => "Invalid file type."];
                    } else {
                        // Check magic number (optional, depends on file type)
                        $validMagicNumbers = [
                            'D0CF11E0', // Excel 97-2003 format (xls)
                            '504B0304'  // Excel 2007+ format (xlsx)
                        ];
                        if ($fileExtension != 'csv' && $fileExtension != 'tsv' && !in_array(strtoupper(bin2hex($fileMagicNumber)), $validMagicNumbers)) {
                            $remark = ["status" => false, "type" => "delete", "message" => "Invalid file content."];
                        } else {
                            // All validations passed, proceed with file upload
                            $randNamePart = bin2hex(random_bytes(5));
                            $newFileName = 'File_' . $randNamePart . '.' . $fileExtension;
                            $newFilePath = $_SERVER["DOCUMENT_ROOT"] . '/' . $newFileName;
                            if (move_uploaded_file($_FILES['excelFile']['tmp_name'], $newFilePath)) {
                                if ($fileExtension == 'xlsx' || $fileExtension == 'xls') {
                                    // Include the PHPSpreadsheet autoload file
                                    require_once $_SERVER['DOCUMENT_ROOT'] . '/assets/library/vendor/autoload.php';

                                    // Create a reader for the Excel file
                                    $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($newFilePath);
                                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                                    $spreadsheet = $reader->load($newFilePath);

                                    // Get the first sheet
                                    $worksheet = $spreadsheet->getActiveSheet();
                                    $rowData = $worksheet->toArray();
                                } else {
                                    // Read CSV or TSV file
                                    $rowData = [];
                                    $delimiter = ($fileExtension == 'csv') ? ',' : "\t";
                                    if (($handle = fopen($newFilePath, 'r')) !== false) {
                                        while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
                                            $rowData[] = $data;
                                        }
                                        fclose($handle);
                                    }
                                }

                                // Skip the header row
                                array_shift($rowData);

                                // Prepare SQL for checking existing values
                                $stmtCheck = $conn->prepare("SELECT COUNT(*) as count FROM `list__designation` WHERE `designation` = ?");

                                // Prepare SQL for inserting new values
                                $stmtInsert = $conn->prepare("INSERT INTO `list__designation`(`designation`) VALUES (?)");

                                foreach ($rowData as $row) {
                                    
                                    $designationValue = htmlspecialchars(trim($row[1]), ENT_QUOTES, 'UTF-8'); // Assuming designation name is in the first column

                                    // Check if value already exists in the database
                                    $stmtCheck->bind_param("s", $designationValue);
                                    $stmtCheck->execute();
                                    $result = $stmtCheck->get_result();
                                    $exists = $result->fetch_assoc()['count'];

                                    if ($exists == 0) {
                                        // Insert new value if it does not exist
                                        $stmtInsert->bind_param("s", $designationValue);
                                        $stmtInsert->execute();
                                    }
                                }

                                // Delete the uploaded file
                                unlink($newFilePath);

                                $remark = ["status" => true, "type" => "added", "message" => "File Imported Successfully"];
                            } else {
                                $remark = ["status" => false, "type" => "delete", "message" => "Failed to upload file."];
                            }
                        }
                    }
                }
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['captcha']) && isset($_POST['designationID'])) {
    $captcha = $_POST["captcha"];
    if (empty($captcha)) {
        $remark = ["status" => false, "type" => "delete", "message" => "Captcha verification failed,"];
    } elseif (!isset($_SESSION['captcha'])) {
        $remark = ["status" => false, "type" => "delete", "message" => "Captcha verification failed,"];
    } elseif ($captcha != $_SESSION['captcha']) {
        $remark = ["status" => false, "type" => "delete", "message" => "Captcha verification failed,"];
    }

    if ($remark['status']) {
        try {
            $sql = "DELETE FROM `list__designation` WHERE designation_id=?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_POST['designationID']);
            if ($stmt->execute()) {
                $remark = ["status" => true, "type" => "delete", "message" => "Designation Deleted Successfully"];
            }
            $stmt->close();
        } catch (Exception $e) {
            handle_error($e);
        }
    }
}

// if ($_SERVER['REQUEST_METHOD'] == 'POST'){
// var_dump($_POST);
// die();
// }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editDesignation']) && isset($_POST['setName'])) {
    if (isset($_POST['designationName'])) {
        $designation = htmlspecialchars(trim($_POST['designationName']), ENT_QUOTES, 'UTF-8');
    } else {
        $designation = '';
    }

    if (strlen($designation) <= 1) {
        $remark = ["status" => false, "message" => "Please enter a designation name,"];
    }

    if ($remark['status']) {
        try {
            $sql = "UPDATE `list__designation` SET `designation`=? WHERE designation_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $designation, $_POST['setName']);
            if ($stmt->execute()) {
                $remark = ["status" => true, "message" => "Designation Name Updated Successfully"];
            }
            $stmt->close();
        } catch (Exception $e) {
            handle_error($e);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addNewDesignation'])) {
    if (isset($_POST['designationName'])) {
        $designation = htmlspecialchars(trim($_POST['designationName']), ENT_QUOTES, 'UTF-8');
    } else {
        $designation = '';
    }

    if (strlen($designation) <= 1) {
        $remark = ["status" => false, "message" => "Please enter a designation name,"];
    }

    if ($remark['status']) {
        try {
            $sql = "INSERT INTO `list__designation`(`designation`) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $designation);
            if ($stmt->execute()) {
                $remark = ["status" => true, "type" => "added", "message" => "New Designation Added Successfully"];
            }
            $stmt->close();
        } catch (Exception $e) {
            handle_error($e);
        }
    }
}
?>

<body class="spin-lock">
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu --> <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/template/side-menu.php"; ?>
            <!-- / Menu -->
            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar --> <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/template/navbar.php"; ?>
                <!-- / Navbar -->
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/template/loadingSpinner.php"; ?>
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-designation pb-1">
                                <div class="mb-4">
                                    <h5>All Designations</h5>
                                </div>
                                <div class="mb-0">
                                    <button class="btn btn-primary cursor-pointer me-2" onclick="showAddDesignationModal()">Add New Designation</button>
                                    <button class="btn btn-primary cursor-pointer" onclick="showExcelModal()">Import New Designation(s)</button>
                                </div>
                            </div>
                            <?php
                            try {
                                $sql = "SELECT d.designation_id, d.designation, COUNT(u.designation) AS employee_count FROM `list__designation` d LEFT JOIN `users` u ON d.designation_id = u.designation GROUP BY d.designation_id, d.designation ORDER BY `designation_id` ASC;";
                                $result = mysqli_query($conn, $sql);
                                if (!$result) {
                                    throw new Exception(mysqli_error($conn));
                                } elseif (mysqli_num_rows($result) > 0) {
                                    echo '<div class="table-responsive text-nowrap mt-0">';
                                    echo '<table class="table">';
                                    echo '<thead>';
                                    echo '<tr>';
                                    echo '<th style="display:none;">#</th>';
                                    echo '<th>Designation Name</th>';
                                    echo '<th class="text-center">Number of Employee Associate</th>';
                                    echo '<th class="text-center">Actions</th>';
                                    echo '</tr>';
                                    echo '</thead>';
                                    echo '<tbody class="text-designation">';
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td style="display:none;">' . $row['designation_id'] . '</td>';
                                        echo '<td>' . $row['designation'] . '</td>';
                                        echo '<td class="text-center">' . $row['employee_count'] . '</td>';
                                        echo '<td class="text-center">';
                                        echo '<div class="btn-group" role="group" aria-label="Basic example">';
                                        echo '<button type="button" class="btn btn-icon btn-label-primary btn-fab demo waves-effect" onclick="editDesignation(' . $row['designation_id'] . ', \'' . $row['designation'] . '\')">';
                                        echo '<span class="tf-icons mdi mdi-pencil-outline mdi-24px"></span>';
                                        echo '</button>';
                                        echo '<button type="button" class="btn btn-icon btn-label-danger btn-fab demo waves-effect" onclick="deleteDesignation(' . $row['designation_id'] . ')">';
                                        echo '<span class="tf-icons mdi mdi-delete-outline mdi-24px"></span>';
                                        echo '</button>';
                                        echo '</div>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                    echo '</tbody>';
                                    echo '</table>';
                                    echo '</div>';
                                } else {
                                    echo '<div class="alert alert-info text-designation" role="alert">';
                                    echo 'No categories found. Would you like to <a href="javascript:void(0)" onclick="showAddDesignationModal()" class="cursor-pointer">add a new designation</a>?';
                                    echo '</div>';
                                }
                            } catch (Exception $e) {
                                handle_error($e);
                            }
                            ?>
                        </div>

                        <?php
                        require_once($_SERVER['DOCUMENT_ROOT'] . '/template/toast.php');
                        require_once($_SERVER['DOCUMENT_ROOT'] . '/template/modal.php');
                        captchaModal();
                        ?>

                        <div class="modal fade" id="modal-add-designation" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="modal-title">Add New Designation</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="designationCancel()"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="addNewDesignation" name="addNewDesignation" method="POST" action="<?php echo pathinfo($_SERVER["PHP_SELF"], PATHINFO_FILENAME); ?>" onsubmit="return false">
                                            <div class="form-floating form-floating-outline mb-4">
                                                <input id="designationName" name="designationName" class="form-control" type="text" placeholder="Please enter designation name" aria-describedby="designationName" autocomplete="off" required />
                                                <label for="designationName">Designation Name</label>
                                            </div>
                                            <input type="hidden" id="setName" name="setName">
                                            <div class="col-12 text-start qs-update">
                                                <button type="submit" id="addDesignation" class="btn btn-label-primary waves-effect">Add New Designation</button>
                                                <button type="button" name="close-modal" class="btn btn-label-danger waves-effect" onclick="designationCancel()">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="modal-upload-excel" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="modal-title">Upload Excel File</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="excelCancel()"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="uploadExcelForm" name="uploadExcelForm" method="POST" enctype="multipart/form-data" action="<?php echo pathinfo($_SERVER["PHP_SELF"], PATHINFO_FILENAME); ?>" onsubmit="return false">
                                            <div>
                                                <label for="excelFile">Select Excel File</label>
                                                <input id="excelFile" name="excelFile" class="form-control mt-2" type="file" placeholder="Please select an Excel file" aria-describedby="excelFile" accept=".xls,.xlsx,.csv,.tsv" required />
                                            </div>
                                            <div  class="mt-1 pt-1 mb-2">
                                                <a href="/assets/Excel Template/DesignationTemplate.xlsx" download>Download Excel Template</a>
                                            </div>
                                            <div class="col-12 text-start qs-update">
                                                <button type="submit" id="uploadExcel" class="btn btn-label-primary waves-effect">Upload Excel File</button>
                                                <button type="button" name="close-modal" class="btn btn-label-danger waves-effect" onclick="excelCancel()">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <?php
                    require_once($_SERVER['DOCUMENT_ROOT'] . '/template/footer.php');
                    ?>
                    <!-- / Footer -->
                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->
    <!-- scripts -->
    <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/template/scripts-section.php"; ?>
    <!-- / scripts -->
    <?php
    if ($remark['status'] && isset($remark['type']) && isset($remark['message'])) {
        echo "<script type='text/javascript'>showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Action Successful','" . $remark['message'] . "');</script>";
    } elseif (isset($remark['message']) && isset($remark['type'])) {
        echo "<script type='text/javascript'>showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Error!!','" . $remark['message'] . " please try again');</script>";
    } elseif ($remark['status'] && isset($remark['message'])) {
        echo "<script type='text/javascript'>showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Designation Updated Successfully','" . $remark['message'] . "');</script>";
    } elseif (isset($remark['message'])) {
        echo "<script type='text/javascript'>showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Incomplete Designation Details!!','" . $remark['message'] . " and try again');</script>";
    }
    ?>

</body>

</html>