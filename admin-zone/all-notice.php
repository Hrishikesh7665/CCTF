<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/template/head.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/common/variables.php";

$remark = ["status" => true];

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
                        // Query to fetch notifications
                        $sql = "SELECT * FROM `notification`";
                        $result = $conn->query($sql); // Assuming $conn is your database connection

                        if ($result->num_rows > 0) {
                            echo '<div class="accordion accordion-custom-button mt-4" id="noticeItems">';

                            $count = 1;
                            while ($row = $result->fetch_assoc()) {
                                $id = htmlspecialchars($row['id']);
                                $title = htmlspecialchars($row['title']);
                                $description = $row['description'];
                                $activeTime = htmlspecialchars($row['activeTime']);
                                $expiredTime = htmlspecialchars($row['expiredTime']);
                                $role = json_decode($row['role'], true); // Assuming role is a JSON string

                                $state = htmlspecialchars($row['state']);
                                $ts = htmlspecialchars($row['ts']);

                                // Generate unique IDs for accordion items
                                $accordionId = "accordionItem" . $id;
                                $headingId = "heading" . $id;
                                $collapseId = "collapse" . $id;

                                $stateBadgeColor = ($state === 'active') ? 'bg-success' : 'bg-warning';
                                $stateLabel = ucfirst($state);

                                $accordionItemClass = $count == 1 ? 'accordion-item border-light' : 'accordion-item border-light';
                                $buttonClass = $count == 1 ? 'accordion-button bg-light text-dark border-primary' : 'accordion-button bg-light text-dark border-light collapsed';
                                $collapseClass = $count == 1 ? 'accordion-collapse collapse show' : 'accordion-collapse collapse';
                                $ariaExpanded = $count == 1 ? 'true' : 'false';

                                $rolesList = '';
                                if (!empty($role) && is_array($role)) {
                                    foreach ($role as $r) {
                                        $rolesList .= '<span class="badge bg-primary me-1">' . ucfirst(htmlspecialchars($r)) . '</span> ';
                                    }
                                }

                                echo '<div class="' . $accordionItemClass . ' mb-3 rounded shadow-sm">
                                        <h2 class="accordion-header" id="' . $headingId . '">
                                            <button type="button" class="' . $buttonClass . ' pt-2 pb-2" data-bs-toggle="collapse" data-bs-target="#' . $accordionId . '" aria-expanded="' . $ariaExpanded . '" aria-controls="' . $accordionId . '">
                                                ' . $title . '<span class="badge fw-small ms-2 ' . $stateBadgeColor . ' rounded-pill px-2 py-1">' . $stateLabel . '</span>
                                                <a href="notification-manager?nid=' . encryptData($id, $key) . '" class="btn rounded-pill btn-light btn-icon btn-outline-primary btn-fab demo waves-effect ms-2">
                                                    <span class="mdi mdi-pencil ri-15px text-primary"></span>
                                                </a>
                                            </button>
                                        </h2>
                                        <div id="' . $accordionId . '" class="' . $collapseClass . '" aria-labelledby="' . $headingId . '" data-bs-parent="#noticeItems">
                                            <div class="accordion-body p-4 body-color rounded">
                                                <div class="mb-3">
                                                    <strong class="text-primary">Notification Description:</strong>
                                                    <p>' . str_replace('\"', '"', htmlspecialchars_decode($description)) . '</p>
                                                </div>
                                                <div class="mb-2">
                                                    <span class="badge bg-success me-1">Active: ' . (new DateTime($activeTime))->format('j-m-Y h:i A') . '</span>
                                                    <span class="badge bg-danger ms-1">Expires: ' . (new DateTime($expiredTime))->format('j-m-Y h:i A') . '</span>
                                                </div>
                                                <div class="mt-2">
                                                    <strong class="text-info">Notification Available to:</strong> ' . $rolesList . '
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                $count++;
                            }


                            echo '</div>';
                        } else {
                            echo '<p class="alert alert-warning">No notifications available.</p>';
                        }

                        ?>



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


    <!-- / scripts -->
    <?php
    if ($remark['status'] && isset($remark['message'])) {
        echo "<script type='text/javascript'>showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Successful!!','" . $remark['message'] . "');</script>";
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