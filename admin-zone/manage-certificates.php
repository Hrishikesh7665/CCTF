<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/template/head.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/common/variables.php";
?>

<?php

$remark = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

	if ($_POST['action'] === 'uploadCertificate' && isset($_POST['captcha2']) && isset($_POST['useremail']) && isset($_POST['certType']) && isset($_FILES['formFile'])) {

		$captcha = $_POST['captcha2'];
		$email = $_POST['useremail'];

		// Check if captcha is provided and matches the session captcha
		if (empty($captcha) || !isset($_SESSION['captcha']) || $captcha != $_SESSION['captcha']) {
			$remark = array('status' => false, 'error' => 'Invalid Captcha', 'message' => 'Captcha verification failed. Please try again');
		}

		$mailExists = emailExists($email);

		if (!$mailExists['status']) {
			$remark = array('status' => false, 'error' => 'Invalid Email', 'message' => 'User doesn\'t exits.');
		}

		$fileExtension = strtolower(pathinfo($_FILES['formFile']['name'], PATHINFO_EXTENSION));
		$fileMagicNumber = file_get_contents($_FILES['formFile']['tmp_name'], false, null, 0, 4);

		if ($fileExtension != "pdf" || $fileMagicNumber != "%PDF") {
			$remark = array('status' => false, 'error' => 'Invalid File', 'message' => 'Invalid file type. Only PDF files are allowed');
		}

		if ($remark == "") {
			move_uploaded_file($_FILES['formFile']['tmp_name'], $_SERVER["DOCUMENT_ROOT"] . '/certificates/' . $currentCTF . '/' . $_POST['certType'] . 'Certificate_' . $email . '.pdf');
			$remark = array('status' => true, 'success' => 'Certificate Uploaded Successfully', 'message' => 'Certificate uploaded successfully for the user');
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
							<div class="card-body">
								<?php
								$certDir = $_SERVER["DOCUMENT_ROOT"] . '/certificates' . '/' . $currentCTF;
								if (!is_dir($certDir)) {
									// Create the directory if it doesn't exist
									mkdir($certDir, 0777, true);
								}
								$files = scandir($certDir);
								$pdfCount = 0;
								foreach ($files as $file) {
									if (is_file($certDir . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) == 'pdf') {
										$pdfCount++;
									}
								}

								if ($pdfCount == 0) {
									echo '
										<div class="alert alert-warning" role="alert">
											<h5 class="alert-heading">No Certificate Available</h5>
											<p>No Certificate Available In The System. Please Generate Participation Certificate If Needed.</p>
										</div>';
								} ?>
								<div class="certificate-buttons">
									<button onclick="generateAllCert()" type="button" class="btn btn-primary" <?php if ($pdfCount >=1) {echo 'disabled';}?> >Generate Perticiapation Certificates</button>
								</div>
							</div>
						</div>
						<?php
						// if ($pdfCount >= 1) {
						try {
							$sql = "SELECT ROW_NUMBER() OVER(ORDER BY sscore DESC, ti ASC, u.id ASC) AS rank, (SELECT ts FROM scoreboard WHERE user_id = sb.user_id ORDER BY ts DESC LIMIT 1) as ti, u.id AS user_id, u.email AS email, u.profession AS profession, u.phoneNumber AS phone, (SELECT list__center.center FROM list__center WHERE list__center.center_id = u.location) as center, u.name AS name, u.status AS status, COUNT(sb.c_id) AS solved, SUM(ch.score) AS sscore FROM users AS u JOIN scoreboard AS sb ON sb.user_id = u.id JOIN challenges AS ch ON sb.c_id = ch.id GROUP BY sb.user_id, u.id, u.email ORDER BY sscore DESC, ti ASC, u.id ASC";
							$result = mysqli_query($conn, $sql);
							if (!$result) {
								throw new Exception(mysqli_error($conn));
							}

							$rowsCount = mysqli_num_rows($result);
							if ($rowsCount > 0) {
								$count = 1;
								while ($row = mysqli_fetch_assoc($result)) {
									$certPresent = false;

									$userName = $row['name'];
									$userEmail = $row['email'];
									$userPhone = $row['phone'];
									$userProfession = ucwords($row['profession']);
									$userCenter = $row['center'];

									$sendBtn = 'disabled';
									$downBtn = 'disabled';

									$certType = ($count >= 4) ? 'Participation' : 'Awarded';
									$certificateLoc = '/certificates/' . $currentCTF . '/' . $certType . 'Certificate_' . $userEmail . '.pdf';

									if (file_exists($_SERVER['DOCUMENT_ROOT'] . $certificateLoc)) {
										$certPresent = true;
										$sendBtn = 'onclick="mailCertificate(\'' . $userName . '\', \'' . $userEmail . '\', \'' . $certificateLoc . '\')"';
										$downBtn = 'onclick="downloadPDF(\'' . $userName . ' ' . $certType . ' Certificate\', \'' . $certificateLoc . '\')"';
									}

									$editBtn = 'onclick="certUpload(\'' . $certType . '\',\'' . $userEmail . '\')"';

									echo <<<HTML
											<div class="card-transparent mt-2 pt-2">
												<div class="row certificate-preview">
													<!-- Certificate -->
													<div class="col-xl-8 col-md-8 col-12 mb-md-0 mb-4">
														<div class="card certificate-preview-card">
															<div class="card-body">
																<div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
																	<div class="mb-xl-0">
																		<p class="mb-1 fw-bold">Username: <span class="fw-normal">{$userName}</span></p>
																		<p class="mb-1 fw-bold">Email: <span class="fw-normal">{$userEmail}</span></p>
																		<p class="mb-1 fw-bold">Phone No.: <span class="fw-normal">{$userPhone}</span></p>
																		<p class="mb-1 fw-bold">Profession: <span class="fw-normal">{$userProfession}</span></p>
																	</div>
																	<div>
																		<h4 class="fw-medium text-capitalize pb-0 mb-0 text-nowrap">{$certType} Certificate</h4>
																		<p class='pb-2 mb-2 fw-bold'>Rank #{$row['rank']}</p>
																		<div class="mb-1">
																		<p class="mb-1 fw-bold">CDAC Center: <span class="fw-normal">{$userCenter}</span></p>
																		</div>
																	</div>
																</div>
															</div>
															<hr class="my-0" />
										HTML;
									if ($certPresent) {
										echo '<div class="card-body d-flex justify-content-center">';
										echo '<iframe src="' . $certificateLoc . '#view=fit&toolbar=0&navpanes=0" width="620px" height="440px" frameborder="0"></iframe>';
									} else {
										echo '<div class="card-body">';
										echo '<div class="alert alert-danger" role="alert">
														<h5 class="alert-heading">Certificate Unavailable</h5>
														<p>Sorry, there is currently no certificate available for this user.</p>
													</div>';
									}

									echo <<<HTML
															</div>
															<!-- <hr class="my-0" /> -->
														</div>
													</div>
													<!-- /Certificate -->

													<!-- Certificate Actions -->
													<div class="col-xl-4 col-md-4 col-12 certificate-actions">
														<div class="card">
															<div class="card-body">
															<button class="btn btn-success d-grid w-100 mb-3" {$sendBtn}>
																<span class="d-flex align-items-center justify-content-center text-nowrap"><i class="mdi mdi-send-outline me-2"></i>Send Certificate</span>
															</button>

															<button class="btn btn-info d-grid w-100 mb-3" {$downBtn}>
																<span class="d-flex align-items-center justify-content-center text-nowrap"><i class="mdi mdi-file-download-outline me-2"></i>Download Certificate</span>
															</button>

															<button class="btn btn-warning d-grid w-100 mb-0" {$editBtn}>
																<span class="d-flex align-items-center justify-content-center text-nowrap"><i class="mdi mdi-file-upload-outline me-2"></i>Upload Certificate</span>
															</button>

															</div>
														</div>
													</div>
													<!-- /Certificate Actions -->
												</div>
											</div>
										HTML;
									$count++;
								}
							}
						} catch (Exception $e) {
							handle_error($e);
						}
						// }
						?>
					</div>
				</div>

				<div class="modal fade" id="fileUpload-Modal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">File Upload and Verification</h5>
								<button type="button" class="btn-close" aria-label="Close" onclick="closeFileUploadModal()"></button>
							</div>
							<div class="modal-body">
								<form id="file-upload-form" class="row g-3" action="<?php echo pathinfo($_SERVER["PHP_SELF"], PATHINFO_FILENAME); ?>" method="POST" enctype="multipart/form-data">
									<div class="mb-4">
										<label for="formFile" class="form-label">Please Chose Certificate PDF</label>
										<input class="form-control" type="file" id="formFile" name="formFile" accept=".pdf" required>
									</div>
									<div class="mb-3 text-center">
										<label for="captcha" class="fw-bold">Please verify that you're human</label>
										<div class="d-flex justify-content-center align-items-center">
											<img src="/captcha" alt="Captcha" id="captchaImage2" class="me-2">
											<button type="button" onclick="reloadCaptcha2()" class="btn btn-link">
												<i class="bi bi-arrow-clockwise"></i> Refresh
											</button>
										</div>
									</div>

									<div class="col-12">
										<div class="input-group input-group-merge">
											<div class="form-floating form-floating-outline mb-3">
												<input type="text" class="form-control" id="captcha2" name="captcha2" placeholder="Enter the captcha" autocomplete="off" required>
												<label for="captcha2">Captcha</label>
											</div>
										</div>
									</div>
									<div class="col-12 text-center">
										<button type="button" class="btn btn-secondary" onclick="closeFileUploadModal()">Back</button>
										<button type="submit" class="btn btn-primary">Submit</button>
									</div>
									<input type="hidden" name="action" value="uploadCertificate">
								</form>
							</div>
						</div>
					</div>
				</div>

				<?php
				require_once $_SERVER["DOCUMENT_ROOT"] . "/template/toast.php";
				require_once $_SERVER["DOCUMENT_ROOT"] . "/template/modal.php";
				captchaModal();
				?>

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
	if ($remark != "") {
		echo '<script type="text/javascript">';
		if ($remark['status'] && isset($remark['success'])) {
			echo "showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', '" . $remark['success'] . "', '" . $remark['message'] . "');";
		} elseif ($remark['status'] && isset($remark['warning'])) {
			echo "showToast(5000, 'mdi-alert', 'animate__shakeX', 'text-warning', '" . $remark['warning'] . "', '" . $remark['message'] . "');";
		} else {
			echo "showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', '" . $remark['error'] . "', '" . $remark['message'] . "');";
		}
		echo '</script>';
	}
	?>
	<!-- / scripts -->

</body>

</html>