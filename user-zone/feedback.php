<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/template/head.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/common/variables.php');
?>

<body class="spin-lock">

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Menu -->
            <?php
            require_once($_SERVER['DOCUMENT_ROOT'] . '/template/side-menu.php');
            ?>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">

                <!-- Navbar -->
                <?php
                require_once($_SERVER['DOCUMENT_ROOT'] . '/template/navbar.php');
                ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/template/loadingSpinner.php"; ?>
                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">

                        <div class="accordion" id="collapsibleSection">
                            <!-- .accordion-item:not(.active):not(:first-child) .accordion-header -->
                            <?php
                            $userId = $_loginInfo['uid'];
                            try {
                                $sql = "SELECT COALESCE(c.id, 'blank') AS id, COALESCE(c.title, 'blank') AS title, f.userId, COALESCE(f.rating, 0) AS rating, f.feedback FROM challenges c JOIN feedback__challenges f ON c.id = f.challenge_id AND f.userId = ? AND c.id in (SELECT c_id FROM logs__qs WHERE u_id = ?);";
                                
                                // $sql = "SELECT COALESCE(c.id, 'blank') AS id, COALESCE(c.title, 'blank') AS title, f.userId, COALESCE(f.rating, 0) AS rating, f.feedback FROM challenges c LEFT OUTER JOIN feedback__challenges f ON c.id = f.challenge_id AND f.userId = ? ORDER BY c.id ASC;";
                                // $sql = "SELECT COALESCE(c.id, 'blank') AS id, COALESCE(c.title, 'blank') AS title, f.userId, COALESCE(f.rating, 0) AS rating, f.feedback FROM challenges c LEFT JOIN feedback__challenges f ON c.id = f.challenge_id WHERE f.userId = ? OR f.userId IS NULL OR f.challenge_id IS NULL ORDER BY c.id ASC;";
                                $stmt = mysqli_prepare($conn, $sql);
                                mysqli_stmt_bind_param($stmt, "ii", $userId, $userId);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                if (!$result) {
                                    throw new Exception(mysqli_error($conn));
                                }
                                $challenges = mysqli_fetch_all($result, MYSQLI_ASSOC);
                                $count = mysqli_num_rows($result);
                                mysqli_stmt_close($stmt);
                            } catch (Exception $e) {
                                handle_error($e);
                            }

                            if ($count > 0 && $comp_state != 'upcoming') {
                                echo '<form id="feedbackForm">'; // Form start
                                echo '	<div class="card accordion-item mb-3">
											<h2 class="accordion-header" id="headingQuestionFeedback">
												<button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseQuestionFeedback" aria-expanded="true" aria-controls="collapseQuestionFeedback"> Challenge Feedback </button>
											</h2>
											<div id="collapseQuestionFeedback" class="accordion-collapse collapse show" data-bs-parent="#collapsibleSection">
												<div class="accordion-body">
													<div class="row">'; // Open a row
                                $index = 0; // Initialize an index counter
                                //$rating = $challenge['rating'];
                                foreach ($challenges as $challenge) {
                                    echo '				<div class="col-md-4 col-sm-6 col-12">
															<div class="card-body ratingContainer">
																<h5 class="card-title">' . $challenge['title'] . '</h5>
																<div class="full-star-ratings" data-rateyo-full-star="true" data-rateyo-rating="' . $challenge['rating'] . '" id="ratting' . $challenge['id'] . '"></div>

																<div class="form-floating form-floating-outline m-2">
																	<textarea name="feedback[' . $challenge['id'] . ']" id="feedback[' . $challenge['id'] . ']" class="form-control" placeholder="Please share your feedback about this challenge" style="height: 80px;">' . $challenge['feedback'] . '</textarea>
																	<label for="basic-default-message' . $index . '">Feedback</label>
																</div>
															</div> 
														</div>';

                                    $index++; // Increment index counter
                                    // If index is a multiple of 3, close the current row and open a new one
                                    if ($index % 3 === 0) {
                                        echo '		</div>
													<div class="row">';
                                    }
                                }
                                echo '</div>
									<div class="row justify-content-end">
										<div class="col-auto">
											<button type="submit" class="btn btn-primary">Submit Feedback</button>
										</div>
									</div>
								</div>
								</div>
								</div>
								</form>'; // Close the last row and the outer containers
                            }
                            ?>

                            <?php
                            $userRating = 0;
                            $userFeedback = "";
                            $userAdvice = "";
                            try {
                                $sql = "SELECT `userId`, `feedback`, `advice`, `rating` FROM `feedback__platform` WHERE `userId` =? LIMIT 1";
                                $stmt = mysqli_prepare($conn, $sql);
                                mysqli_stmt_bind_param($stmt, "i", $userId);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                if (!$result) {
                                    throw new Exception(mysqli_error($conn));
                                }
                                $platformFeedback = mysqli_fetch_all($result, MYSQLI_ASSOC);
                                if (count($platformFeedback) != 0) {
                                    $userRating = $platformFeedback[0]['rating'];
                                    $userFeedback = $platformFeedback[0]['feedback'];
                                    $userAdvice = $platformFeedback[0]['advice'];
                                }
                                mysqli_stmt_close($stmt);
                            } catch (Exception $e) {
                                handle_error($e);
                            }

                            ?>

                            <div class="card accordion-item">
                                <h2 class="accordion-header" id="headingPlatformFeedback">
                                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapsePlatformFeedback" aria-expanded="false" aria-controls="collapsePlatformFeedback"> Platform Feedback </button>
                                </h2>
                                <div id="collapsePlatformFeedback" class="accordion-collapse collapse" aria-labelledby="headingPlatformFeedback" data-bs-parent="#collapsibleSection">
                                    <div class="accordion-body">
                                        <form id="platformFeedbackForm">
                                            <div class="mb-3">
                                                <h5>Please rate our CTF platform</h5>
                                                <div class="full-star-ratings" data-rateyo-full-star="true" id="platformRating" data-rateyo-rating="<?php echo $userRating; ?>"></div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-floating form-floating-outline">
                                                    <textarea name="platformFeedback" id="platformFeedback" class="form-control" placeholder="Please share your feedback about this platform" style="height: 80px;"><?php echo $userFeedback; ?></textarea>
                                                    <label for="platformFeedback">Feedback</label>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-floating form-floating-outline">
                                                    <textarea name="platformImprovements" id="platformImprovements" class="form-control" placeholder="Please share any suggestions or observations for improving our CTF platform" style="height: 80px;"><?php echo $userAdvice; ?></textarea>
                                                    <label for="platformImprovements">Areas for Improvement</label>
                                                </div>
                                            </div>
                                            <div class="row justify-content-end">
                                                <div class="col-auto">
                                                    <button type="submit" class="btn btn-primary" id="platformFeedbackSubBtn">Submit Feedback</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- toast & modal-->
                    <?php
                    require_once($_SERVER['DOCUMENT_ROOT'] . '/template/toast.php');
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

    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/template/scripts-section.php');
    ?>
    <!-- / scripts -->

</body>

</html>