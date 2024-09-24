<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/template/head.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/common/variables.php";
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



                        <div class="card-transparent">
                            <div class="row certificate-preview">
                                <!-- Certificate -->
                                <div class="col-12 mb-md-0 mb-4">
                                    <div class="card certificate-preview-card">
                                        <?php

                                        function showCertUnavailable()
                                        {
                                            echo '<div class="card-body">
                                                <div class="alert alert-danger" role="alert">
                                                    <h5 class="alert-heading">Certificate Unavailable</h5>
                                                    <p>Sorry, there is currently no certificate available for you.</p>
                                                </div>
                                            </div>';
                                        }

                                        $certDir = $_SERVER["DOCUMENT_ROOT"] . '/certificates/' . $currentCTF;
                                        $files = scandir($certDir);
                                        $pdfCount = 0;
                                        $user_rank = 0;
                                        foreach ($files as $file) {
                                            if (is_file($certDir . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) == 'pdf') {
                                                $pdfCount++;
                                            }
                                        }
                                        try {
                                            if ($pdfCount >= 1) {
                                                $user_stats = getUserStats($conn, $_loginInfo['uid']);
                                                $user_rank = $user_stats['user_rank'];
                                            }
                                        } catch (Exception $e) {
                                            handle_error($e);
                                        }
                                        if ($user_rank != 0 && $pdfCount != 0 && $comp_state == 'end') {
                                            $certType = ($user_rank >= 4) ? 'Participation' : 'Awarded';
                                            $certificateLoc = '/certificates/' . $currentCTF . '/' . $certType . 'Certificate_' . $_loginInfo['email'] . '.pdf';
                                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $certificateLoc)) {
                                                echo '<div class="card">
                                                <div class="card-header">
                                                    <h3 class="text-center">Certificate Viewer</h3>
                                                </div>
                                                <div class="card-body d-flex justify-content-center">
                                                    <iframe src="' . $certificateLoc . '#view=fit&toolbar=0&navpanes=0" width="780px" height="550px" frameborder="0"></iframe>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="alert alert-info text-center" role="alert">
                                                    Congratulations on your participation in the CTF competition! Your dedication and skill have been recognized, and you\'ve demonstrated remarkable prowess in cracking the code. Here\'s your digital certificate as a testament to your hacking expertise and commitment to excellence.
                                                    </div>
                                                    <div class="d-flex justify-content-center">
                                                        <button class="btn btn-primary btn-lg" onclick="downloadPDF(\'' . $_loginInfo['username'] . ' ' . $certType . ' Certificate\', \'' . $certificateLoc . '\')">
                                                            <i class="mdi mdi-file-download-outline me-2"></i>Download Certificate
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>';                                           
                                            } else {
                                                showCertUnavailable();
                                            }
                                        } else {
                                            showCertUnavailable();
                                        }
                                        ?>
                                        <!-- <hr class="my-0" /> -->
                                    </div>
                                </div>
                                <!-- /Certificate -->

                                <!-- Certificate Actions -->
                                <!-- <div class="col-12 certificate-actions mt-1 pt-1"> -->
                                    <!-- <div class="card"> -->
                                        <!-- <div class="card-body"> -->

                                            <!-- <button class="btn btn-info d-grid w-100"> -->
                                                <!-- <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="mdi mdi-file-download-outline me-2"></i>Download -->
                                                    <!-- Certificate</span> -->
                                            <!-- </button> -->

                                        <!-- </div> -->
                                    <!-- </div> -->
                                <!-- </div> -->
                                <!-- /Certificate Actions -->
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

</body>

</html>