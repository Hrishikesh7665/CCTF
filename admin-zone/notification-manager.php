<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/template/head.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/common/variables.php";

$remark = ["status" => true];
$nid = null;
$state = "";
$notice_role = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['nid'])) {
    $encNID = $_GET['nid'];
    $nid = decryptData($encNID, $key);
    if ($nid != false && filter_var($nid, FILTER_VALIDATE_INT) != false) {
        // Prepare the SQL query
        $sql = "SELECT `title`, `description`, `activeTime`, `expiredTime`, `role`, `state`, `ts` FROM notification WHERE id = ?";

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $nid);
            $stmt->execute();
            $result = $stmt->get_result();
            // Check if only one row is returned
            if ($result->num_rows === 1) {
                // Fetch the data
                $row = $result->fetch_assoc();

                $title = htmlspecialchars($row['title']);
                $description = $row['description'];
                $activeTime = htmlspecialchars($row['activeTime']);
                $expiredTime = htmlspecialchars($row['expiredTime']);
                $notice_role = json_decode($row['role'], true);
                $state = htmlspecialchars($row['state']);
                $ts = htmlspecialchars($row['ts']);


                // Close the statement
                $stmt->close();
            } else {
                $stmt->close();
                header('Location: /admin-zone/notification-manager');
                exit();
            }
        } else {
            echo "Error preparing the SQL statement.";
        }
    } else {
        header('Location: /admin-zone/notification-manager');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notificationID']) && isset($_POST['updateNotice'])) {
    $requiredFields = [
        'notificationTitle',
        'quillContent',
        'act-dt',
        'exp-dt',
        'selectState'
    ];

    $notificationID = decryptData($_POST['notificationID'], $key);

    if ($notificationID != false && filter_var($notificationID, FILTER_VALIDATE_INT) != false) {
        $allFieldsSet = array_reduce($requiredFields, fn($carry, $field) => $carry && isset($_POST[$field]) && !empty(trim($_POST[$field])), true);

        $activeTime = DateTime::createFromFormat('j-m-Y h:i A', $_POST['act-dt']);
        $expireTime = DateTime::createFromFormat('j-m-Y h:i A', $_POST['exp-dt']);

        if ($activeTime && $expireTime) {
            $activeTime = $activeTime->format('Y-m-d H:i:s');
            $expireTime = $expireTime->format('Y-m-d H:i:s');
        } else {
            $allFieldsSet = false;
        }

        $roles = $_POST['multicol-role'];
        $decryptionSuccessful = false;

        if (is_array($roles)) {
            $decryptedRoles = [];
            foreach ($roles as $role) {
                // Decrypt each role individually
                $decryptedRole = decryptData($role, $key);
                if ($decryptedRole !== false) {
                    $decryptedRoles[] = $decryptedRole;
                }
            }

            if (!empty($decryptedRoles)) {
                // Convert decrypted roles array into JSON
                $encRolesStr = json_encode($decryptedRoles);
                $decryptionSuccessful = true;
            }
        } elseif (is_string($roles)) {
            $decryptedRole = decryptData($roles, $key);
            if (is_string($decryptedRole) && !empty($decryptedRole)) {
                // Convert single decrypted role into JSON
                $encRolesStr = json_encode([$decryptedRole]); // Making it a JSON array
                $decryptionSuccessful = true;
            }
        }

        if ($allFieldsSet && in_array($_POST['selectState'], ['active', 'deactive']) && $decryptionSuccessful) {
            $notificationTitle = mysqli_real_escape_string($conn, $_POST['notificationTitle']);
            $quillContent = mysqli_real_escape_string($conn, $_POST['quillContent']);
            $selectState = mysqli_real_escape_string($conn, $_POST['selectState']);

            $sql = "UPDATE notification SET title = ?, description = ?, activeTime = ?, expiredTime = ?, state = ?, role = ?, ts = current_timestamp() WHERE id = ?";

            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssssssi", $notificationTitle, $quillContent, $activeTime, $expireTime, $selectState, $encRolesStr, $notificationID);

                if (mysqli_stmt_execute($stmt)) {
                    if (mysqli_stmt_affected_rows($stmt) > 0) {


                        $sql = "SELECT `title`, `description`, `activeTime`, `expiredTime`, `role`, `state`, `ts` FROM notification WHERE id = ?";

                        // Prepare the statement
                        if ($stmt1 = mysqli_prepare($conn, $sql)) {
                            // Bind the ID to the statement
                            mysqli_stmt_bind_param($stmt1, "i", $notificationID);

                            // Execute the statement
                            if (mysqli_stmt_execute($stmt1)) {
                                // Get the result
                                $result = mysqli_stmt_get_result($stmt1);

                                // Check if exactly one row was returned
                                if (mysqli_num_rows($result) === 1) {
                                    // Fetch the associative array
                                    $row = mysqli_fetch_assoc($result);


                                    $nid = $notificationID;
                                    $encNID = $_POST['notificationID'];

                                    // Escape and decode the fields
                                    $title = htmlspecialchars($row['title']);
                                    $description = $row['description'];
                                    $activeTime = htmlspecialchars($row['activeTime']);
                                    $expiredTime = htmlspecialchars($row['expiredTime']);
                                    $notice_role = json_decode($row['role'], true);
                                    $state = htmlspecialchars($row['state']);
                                    $ts = htmlspecialchars($row['ts']);

                                    $remark = ['status' => true, 'type' => 'success', 'message' => 'Notification has been updated successfully'];
                                } else {
                                    $remark = ['status' => false, 'type' => 'error', 'message' => 'Notification ID does not exist.'];
                                }
                            } else {
                                $remark = ['status' => false, 'type' => 'error', 'message' => 'Error executing the SELECT query.'];
                            }

                            // Close the statement
                            mysqli_stmt_close($stmt1);
                        } else {
                            // Handle statement preparation error
                            $remark = ['status' => false, 'type' => 'error', 'message' => 'Error preparing the SELECT statement.'];
                        }


                        $remark = ['status' => true, 'type' => 'success', 'message' => 'Notification has been updated successfully'];
                    } else {
                        // No rows affected means the id does not exist
                        $remark = ['status' => false, 'type' => 'error', 'message' => 'Notification ID does not exist.'];
                    }
                } else {
                    // Handle execution error
                    $remark = ['status' => false, 'type' => 'error', 'message' => 'Error executing the update.'];
                }
                mysqli_stmt_close($stmt);
            } else {
                // Handle statement preparation error
                $remark = ['status' => false, 'type' => 'error', 'message' => 'Error preparing the statement.'];
            }
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requiredFields = [
        'notificationTitle',
        'quillContent',
        'act-dt',
        'exp-dt',
        'selectState'
    ];

    $allFieldsSet = array_reduce($requiredFields, fn($carry, $field) => $carry && isset($_POST[$field]) && !empty(trim($_POST[$field])), true);

    $activeTime = DateTime::createFromFormat('j-m-Y h:i A', $_POST['act-dt']);
    $expireTime = DateTime::createFromFormat('j-m-Y h:i A', $_POST['exp-dt']);

    if ($activeTime && $expireTime) {
        $activeTime = $activeTime->format('Y-m-d H:i:s');
        $expireTime = $expireTime->format('Y-m-d H:i:s');
    } else {
        $allFieldsSet = false;
    }

    $roles = $_POST['multicol-role'];
    $decryptionSuccessful = false;

    if (is_array($roles)) {
        $decryptedRoles = [];
        foreach ($roles as $role) {
            // Decrypt each role individually
            $decryptedRole = decryptData($role, $key);
            if ($decryptedRole !== false) {
                $decryptedRoles[] = $decryptedRole;
            }
        }

        if (!empty($decryptedRoles)) {
            // Convert decrypted roles array into JSON
            $encRolesStr = json_encode($decryptedRoles);
            $decryptionSuccessful = true;
        }
    } elseif (is_string($roles)) {
        $decryptedRole = decryptData($roles, $key);
        if (is_string($decryptedRole) && !empty($decryptedRole)) {
            // Convert single decrypted role into JSON
            $encRolesStr = json_encode([$decryptedRole]); // Making it a JSON array
            $decryptionSuccessful = true;
        }
    }

    if ($allFieldsSet && in_array($_POST['selectState'], ['active', 'deactive']) && $decryptionSuccessful) {
        $notificationTitle = mysqli_real_escape_string($conn, $_POST['notificationTitle']);
        $quillContent = mysqli_real_escape_string($conn, $_POST['quillContent']);
        $selectState = mysqli_real_escape_string($conn, $_POST['selectState']);

        // Insert the notification, including the JSON encoded roles
        $sql = "INSERT INTO notification (title, description, activeTime, expiredTime, state, role) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssss", $notificationTitle, $quillContent, $activeTime, $expireTime, $selectState, $encRolesStr);

            if (mysqli_stmt_execute($stmt)) {
                $remark = ['status' => true, 'type' => 'success', 'message' => 'Notification has been added successfully'];
            }
            mysqli_stmt_close($stmt);
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

                        <?php
                        // Assuming you already have a database connection set up
                        $query = "SELECT DISTINCT `role` FROM `users`";
                        $result = mysqli_query($conn, $query);

                        $roles = [];
                        if ($result) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $roles[] = $row['role'];
                            }
                        }
                        ?>

                        <div class="row">
                            <div class="col-xl">
                                <div class="card mb-6">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0"><?php echo isset($nid) ? 'Edit' : 'Add New'; ?> Notification</h5>
                                        <small class="text-body float-end">Fill in the details</small>
                                        <?php echo isset($nid) ? '<small class="text-body"> Last Updated at ' . $ts . '</small>' : ''; ?>
                                    </div>
                                    <div class="card-body">
                                        <form id="notificationForm" action="<?php echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME); ?>" method="POST" role="form" autocomplete="off">
                                            <!-- Notification Title -->
                                            <?php if (isset($nid)) {
                                                echo '<input type="hidden" class="form-control" name="notificationID" value="' . $encNID . '">
                                                <input type="hidden" class="form-control" name="updateNotice" value="true">
                                                ';
                                            } ?>
                                            <div class="form-floating form-floating-outline mb-3">
                                                <input type="text" class="form-control" id="notificationTitle" name="notificationTitle" placeholder="Notification Title" value="<?php echo isset($nid) ? $title : ''; ?>" required />
                                                <label for="notificationTitle">Notification Title</label>
                                            </div>

                                            <!-- Notification Message -->
                                            <div class="form-floating form-floating-outline mb-4">
                                                <h6 class="mb-2 text-muted">Notification Description</h6>
                                                <div id="full-editor" required>
                                                    <?php echo isset($nid) ? str_replace('\"', '"', htmlspecialchars_decode($description)) : ''; ?>
                                                </div>
                                                <input type="hidden" class="form-control" id="demo" name="demo" value="<?php echo isset($nid) ? htmlspecialchars($description) : ''; ?>">
                                            </div>

                                            <!-- Active Time and Expire Time -->
                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" class="form-control" placeholder="Select active time" id="act-dt" name="act-dt" value="<?php echo isset($nid) ? (new DateTime($activeTime))->format('j-m-Y h:i A') : ''; ?>" required />
                                                        <label for="act-dt">Active Time</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-4">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" class="form-control" placeholder="Select expire time" id="exp-dt" name="exp-dt" value="<?php echo isset($nid) ? (new DateTime($expiredTime))->format('j-m-Y h:i A') : ''; ?>" required />
                                                        <label for="exp-dt">Expire Time</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Select Group (Role) and Status -->
                                            <div class="row">
                                                <div class="col-md-6 select2-primary mb-4">
                                                    <div class="form-floating form-floating-outline">
                                                        <select id="multicol-role" name="multicol-role[]" class="select2 form-select" multiple required>
                                                            <?php foreach ($roles as $role): ?>
                                                                <option
                                                                    value="<?= encryptData($role, $key); ?>"
                                                                    <?php if (isset($nid)) {
                                                                        if (in_array($role, $notice_role)) {
                                                                            echo "selected";
                                                                        }
                                                                    } ?>>
                                                                    <?= ucfirst(htmlspecialchars($role)); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-4">
                                                    <div class="form-floating form-floating-outline">
                                                        <select id="selectState" name="selectState" class="select2 form-select form-select-lg" data-allow-clear="true" required>
                                                            <option value="active">Active</option>
                                                            <option value="deactive">Deactive</option>
                                                        </select>
                                                        <label for="selectState">Notification State</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end mb-3">
                                                <?php
                                                if (isset($nid)) {
                                                    echo '<button type="button" class="btn btn-outline-secondary me-2" onclick="window.location.href=\'/admin-zone/all-notice\';">Cancel</button>';
                                                    echo '<button type="submit" class="btn btn-primary">Update Notice</button>';
                                                } else {
                                                    echo '<button type="button" id="rst-btn" class="btn btn-outline-secondary me-2" onclick="resetForm()">Reset</button>';
                                                    echo '<button type="submit" class="btn btn-primary">Save Notice</button>';
                                                }
                                                ?>
                                            </div>
                                        </form>

                                    </div>

                                </div>
                            </div>
                        </div>


                        <?php
                        require_once($_SERVER['DOCUMENT_ROOT'] . '/template/toast.php');
                        require_once($_SERVER['DOCUMENT_ROOT'] . '/template/modal.php');
                        captchaModal();
                        ?>

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

    <?php
    echo '<script type="text/javascript">let selected_state = "' . $state . '";</script>';
    if (isset($encNID)) {
        echo '<script type="text/javascript">history.replaceState(null, "", "/admin-zone/notification-manager?nid=' . $encNID . '");</script>';
    }
    ?>
    <!-- / scripts -->
    <?php
    if ($remark['status'] && isset($remark['type']) && isset($remark['message'])) {
        if ($remark['type'] === 'success') {
            echo "<script type='text/javascript'>showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Successful!!','" . $remark['message'] . "');</script>";
        } elseif ($remark['type'] === 'error') {
            echo "<script type='text/javascript'>showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Error!!','" . $remark['message'] . " Please try again');</script>";
        }
    }
    // elseif (isset($remark['message']) && isset($remark['type'])) {
    // echo "<script type='text/javascript'>showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Error!!','" . $remark['message'] . " please try again');</script>";
    // } elseif ($remark['status'] && isset($remark['message'])) {
    // echo "<script type='text/javascript'>showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Center Name Updated Successfully','" . $remark['message'] . "');</script>";
    // } elseif (isset($remark['message'])) {
    // echo "<script type='text/javascript'>showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Incomplete Center Details!!','" . $remark['message'] . " and try again');</script>";
    // }
    ?>

</body>

</html>