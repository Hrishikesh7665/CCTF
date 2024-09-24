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
                                $stmtCheck = $conn->prepare("SELECT COUNT(*) as count FROM `category` WHERE `name` = ?");

                                // Prepare SQL for inserting new values
                                $stmtInsert = $conn->prepare("INSERT INTO `category`(`name`) VALUES (?)");

                                foreach ($rowData as $row) {
                                    $name = htmlspecialchars(trim($row[1]), ENT_QUOTES, 'UTF-8'); // Assuming name is in the first column

                                    // Check if value already exists in the database
                                    $stmtCheck->bind_param("s", $name);
                                    $stmtCheck->execute();
                                    $result = $stmtCheck->get_result();
                                    $exists = $result->fetch_assoc()['count'];

                                    if ($exists == 0) {
                                        // Insert new value if it does not exist
                                        $stmtInsert->bind_param("s", $name);
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


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['captcha']) && isset($_POST['category'])) {
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
            $sql = "DELETE FROM `category` WHERE cat_id=?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_POST['category']);
            if ($stmt->execute()) {
                $remark = ["status" => true, "type" => "delete", "message" => "Category Deleted Successfully"];
            }
            $stmt->close();
        } catch (Exception $e) {
            handle_error($e);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editCategory']) && isset($_POST['c_id'])) {
    if (isset($_POST['categoryTitle'])) {
        $category = htmlspecialchars(trim($_POST['categoryTitle']), ENT_QUOTES, 'UTF-8');
    } else {
        $category = '';
    }

    if (strlen($category) <= 1) {
        $remark = ["status" => false, "message" => "Please enter a category name,"];
    }

    if ($remark['status']) {
        try {
            $sql = "UPDATE `category` SET `name`=? WHERE cat_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $category, $_POST['c_id']);
            if ($stmt->execute()) {
                $remark = ["status" => true, "message" => "Category Updated Successfully"];
            }
            $stmt->close();
        } catch (Exception $e) {
            handle_error($e);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addNewCategory'])) {
    if (isset($_POST['categoryTitle'])) {
        $category = htmlspecialchars(trim($_POST['categoryTitle']), ENT_QUOTES, 'UTF-8');
    } else {
        $category = '';
    }

    if (strlen($category) <= 1) {
        $remark = ["status" => false, "message" => "Please enter a category name,"];
    }

    if ($remark['status']) {
        try {
            $sql = "INSERT INTO `category`(`name`) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $category);
            if ($stmt->execute()) {
                $remark = ["status" => true, "type" => "added", "message" => "New Category Added Successfully"];
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
            <!-- Menu -->
            <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/template/side-menu.php"; ?>
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
                            <div class="card-header d-flex justify-content-between align-items-center pb-1">
                                <div class="mb-4">
                                    <h5>All Category(s)</h5>
                                </div>
                                <div class="mb-0">
                                    <button class="btn btn-primary cursor-pointer me-2" onclick="showAddCategoryModal()">Add New Category</button>
                                    <button class="btn btn-primary cursor-pointer" onclick="showExcelModal()">Import New Category(s)</button>
                                </div>
                            </div>
                            <?php
                            try {
                                $sql = "SELECT c.cat_id, c.name, COUNT(ch.id) AS category_count FROM category c LEFT JOIN challenges ch ON c.cat_id = ch.cat_id GROUP BY c.cat_id, c.name ORDER BY `c`.`cat_id` ASC;";
                                $result = mysqli_query($conn, $sql);
                                if (!$result) {
                                    throw new Exception(mysqli_error($conn));
                                } elseif (mysqli_num_rows($result) > 0) {
                                    echo '<div class="table-responsive text-nowrap mt-0">';
                                    echo '<table class="table">';
                                    echo '<thead class="text-center">';
                                    echo '<tr>';
                                    echo '<th style="display:none;">#</th>';
                                    echo '<th>Category Name</th>';
                                    echo '<th>Number of Questions</th>';
                                    echo '<th>Actions</th>';
                                    echo '</tr>';
                                    echo '</thead>';
                                    echo '<tbody class="text-center">';
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td style="display:none;">' . $row['cat_id'] . '</td>';
                                        echo '<td>' . $row['name'] . '</td>';
                                        echo '<td>' . $row['category_count'] . '</td>';
                                        echo '<td>';
                                        echo '<div class="btn-group" role="group" aria-label="Basic example">';
                                        echo '<button type="button" class="btn btn-icon btn-label-primary btn-fab demo waves-effect" onclick="editCategory(' . $row['cat_id'] . ', \'' . $row['name'] . '\')">';
                                        echo '<span class="tf-icons mdi mdi-pencil-outline mdi-24px"></span>';
                                        echo '</button>';
                                        echo '<button type="button" class="btn btn-icon btn-label-danger btn-fab demo waves-effect" onclick="deleteCategory(' . $row['cat_id'] . ')">';
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
                                    echo '<div class="alert alert-info text-center" role="alert">';
                                    echo 'No categories found. Would you like to <a href="javascript:void(0)" onclick="showAddCategoryModal()" class="cursor-pointer">add a new category</a>?';
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

                        <div class="modal fade" id="modal-add-category" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="modal-title">Add New Category</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="categoryCancel()"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="addNewCategory" name="addNewCategory" method="POST" action="<?php echo pathinfo($_SERVER["PHP_SELF"], PATHINFO_FILENAME); ?>" onsubmit="return false">
                                            <div class="form-floating form-floating-outline mb-4">
                                                <input id="categoryTitle" name="categoryTitle" class="form-control" type="text" placeholder="Please enter category name" aria-describedby="categoryTitle" autocomplete="off" required />
                                                <label for="categoryTitle">Category Name</label>
                                            </div>
                                            <input type="hidden" id="setCID" name="c_id">
                                            <div class="col-12 text-start qs-update">
                                                <button type="submit" id="addCategory" class="btn btn-label-primary waves-effect">Add New Category</button>
                                                <button type="button" name="close-modal" class="btn btn-label-danger waves-effect" onclick="categoryCancel()">Cancel</button>
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
                                                <a href="/assets/Excel Template/CategoryTemplate.xlsx" download>Download Excel Template</a>
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
        echo "<script type='text/javascript'>showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Category Updated Successfully','" . $remark['message'] . "');</script>";
    } elseif (isset($remark['message'])) {
        echo "<script type='text/javascript'>showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Incomplete Category!!','" . $remark['message'] . " and try again');</script>";
    }
    ?>
</body>

</html>