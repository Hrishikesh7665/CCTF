<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/template/head.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";

$submitStatus = "";
$passwordStatus = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["old-password"]) && isset($_POST["new-password"]) && isset($_POST["confirm-password"])) {

	$oldPassword = $_POST["old-password"];
	$newPassword = $_POST["new-password"];
	$confirmPassword = $_POST["confirm-password"];
	$captcha = $_POST["captcha"];

	$errors = array();

	if (empty($captcha)) {
		$errors[] = "Please enter valid captcha.";
	} elseif (!isset($_SESSION['captcha'])) {
		$errors[] = "Please enter valid captcha.";
	} elseif ($captcha != $_SESSION['captcha']) {
		$errors[] = "Please enter valid captcha.";
	}

	if (empty($oldPassword)) {
		$errors[] = "Please enter the current password.";
	}

	if ($newPassword == $oldPassword) {
		$errors[] = "Old password and new password can not be same.";
	}

	if (!verifyPasswordComplexity($newPassword)) {
		$errors[] = "The password must contain at least one lowercase letter, one uppercase letter, one number, one special character, and be at least 8 characters long.";
	}

	if ($newPassword !== $confirmPassword) {
		$errors[] = "New and confirm passwords must match.";
	}

	if (!checkPasswordHistory($conn, $_loginInfo["uid"], $newPassword)) {
		$errors[] = "Please refrain from using a previous password for your new one.";
	}

	if (empty($errors)) {
		$userId = $_loginInfo["uid"];
		$state = changePass($userId, $oldPassword, $newPassword);
		if ($state['status']) {
			$passwordStatus = array("status" => true, "message" => "Password changed successfully.");
		} else {
			$passwordStatus = array("status" => false, "type" => "red", "message" => $state['message']);
		}
	} elseif ($errors[0] == 'Please enter valid captcha.') {
		$passwordStatus = array("status" => false, "type" => "red", "message" => 'Captcha Verification Failed');
	} else {
		$passwordStatus = array("status" => false, "type" => "yellow", "message" => implode("<br>", $errors));
	}
}

// Validate form on POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateDetails'])) {
	// Initialize $submitStatus as default
	$submitStatus = [
		"status" => false,
		"message" => "Validation failed.",
	];

	// Check if all required fields are set and captcha is verified
	if (isset($_POST["userName"], $_POST["phoneNumber"], $_POST["captcha"])) {
		$userName = $_POST["userName"];
		$phoneNumber = $_POST["phoneNumber"];
		$profession = $_loginInfo["profession"];
		$captcha = $_POST["captcha"];

		// Verify Captcha
		if (empty($captcha) || !isset($_SESSION['captcha']) || $captcha != $_SESSION['captcha']) {
			$submitStatus["message"] = "Please enter valid captcha.";
		} else {
			//phone number already in used or not
			$phExists = phoneExists($phoneNumber, $_loginInfo["uid"]);

			// Initialize designation and location
			$designation = null;
			$location = "";

			// Set designation and location if available
			if (isset($_POST["designation"])) {
				$designation = $_POST["designation"];
			}

			if (isset($_POST["location"])) {
				$location = ucwords($_POST["location"]);
			}

			// Validate User Name
			if ($userName === '' || strlen($userName) < 4 || strlen($userName) > 40 || !preg_match('/^[a-zA-Z. ]{4,}$/', $userName)) {
				$submitStatus["message"] = "Please enter your full name.";
			}

			// Validate Phone Number
			elseif ($phoneNumber === "" || !ctype_digit($phoneNumber) || strlen($phoneNumber) !== 10 || !$phExists['status']) {
				if (!$phExists['status']) {
					insertLog($conn, $_loginInfo["uid"], 'Trying to set another user phone number');
					$submitStatus["message"] = "Given phone number is already registered with another user.";
				} else {
					$submitStatus["message"] = "Please enter a valid 10-digit Phone Number.";
				}
			}
			// Validate Profession
			elseif ($profession === "") {
				$submitStatus["message"] = "Please enter a Profession.";
			}
			// Validate Designation if Profession is 'employee'
			elseif ($profession === "employee" && $designation === "") {
				$submitStatus["message"] = "Please enter a Designation.";
			}
			// Validate Location
			elseif ($location === "") {
				$submitStatus["message"] = "Please enter a CDAC Center.";
			} else {
				// Insert data into the users table using parameterized query
				$stmt = $conn->prepare(
					"UPDATE users SET `name`=?, phoneNumber=?, profession=?, designation=(SELECT list__designation.designation_id FROM list__designation WHERE list__designation.designation = ?), `location`= (SELECT list__center.center_id FROM list__center WHERE list__center.center = ?) WHERE id=?"
				);

				// Check if profession is 'student', if so, set designation to null
				if ($profession === "student") {
					$designation = null;
				}

				$stmt->bind_param(
					"sssssi",
					$userName,
					$phoneNumber,
					$profession,
					$designation,
					$location,
					$_loginInfo["uid"]
				);

				// Execute the query
				if ($stmt->execute()) {
					$submitStatus = [
						"status" => true,
						"message" => "Your account details updated successfully.",
					];
					// Update session and login info
					$_SESSION["username"] = $userName;
					$_SESSION["location"] = $location;
					$_SESSION["designation"] = $designation;
					$_SESSION["profession"] = $profession;
					$_SESSION["phone"] = $phoneNumber;
					$_loginInfo["username"] = $userName;
					$_loginInfo["phone"] = $phoneNumber;
					$_loginInfo["location"] = $location;
					$_loginInfo["designation"] = $designation;
					$_loginInfo["profession"] = $profession;
				} else {
					handle_error($stmt->error);
				}

				// Close the statement
				$stmt->close();
			}
		}
	} else {
		$submitStatus["message"] = "All required fields are not set.";
	}
}



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
						<div class="row">
							<div class="col-md-12">
								<div class="card mb-4">
									<h4 class="card-header">Profile Details</h4>
									<!-- Account -->
									<div class="card-body">
										<div class="d-flex align-items-start align-items-sm-center gap-4">
											<img src="<?php echo $_loginInfo["displaypic"]; ?>" alt="user-avatar" class="d-block w-px-120 h-px-120 rounded userAvatar" id="uploadedAvatar" />
											<div class="button-wrapper">
												<label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
													<span class="d-none d-sm-block">Upload new photo</span>
													<i class="mdi mdi-tray-arrow-up d-block d-sm-none"></i>
													<input type="file" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg" onchange="handleFileChange(this)" />
												</label>
												<button type="button" id="button-reset" class="btn btn-outline-danger account-image-reset mb-3" onclick="resetImage()">
													<i class="mdi mdi-reload d-block d-sm-none"></i>
													<span class="d-none d-sm-block">Reset</span>
												</button>
												<div class="text-muted small">Allowed JPG, JPEG, or PNG. Max size of 5MB </div>
											</div>
										</div>
									</div>
									<div class="card-body pt-2 mt-1">
										<form id="formAccountSettings" method="POST" action="<?php echo pathinfo($_SERVER["PHP_SELF"], PATHINFO_FILENAME); ?>" onsubmit="return false">
											<div class="row mt-2 gy-4">
												<div class="col-md-6">
													<div class="form-floating form-floating-outline">
														<input class="form-control" type="text" id="userName" name="userName" value="<?php echo $_loginInfo["username"]; ?>" placeholder="Please enter your full name" />
														<label for="userName">Full Name</label>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-floating form-floating-outline">
														<input class="form-control" type="email" id="email" name="email" value="<?php echo $_loginInfo["email"]; ?>" placeholder="Please enter your email address" readonly />
														<label for="email">E-mail</label>
													</div>
												</div>
												<div class="col-md-6">
													<div class="input-group input-group-merge">
														<span class="input-group-text">IN (+91)</span>
														<div class="form-floating form-floating-outline">
															<input type="number" id="phoneNumber" name="phoneNumber" class="form-control" value="<?php echo $_loginInfo["phone"]; ?>" placeholder="98554147861" minlength="10" maxlength="10" pattern="[1-9][0-9]{9}" />
															<label for="phoneNumber">Phone Number</label>
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-floating form-floating-outline">
														<input class="form-control" type="text" id="profession" name="profession" value="<?php echo ucwords($_loginInfo["profession"]); ?>" readonly />
														<label for="profession">Profession</label>
													</div>
												</div>
												<?php
												$designations = getDesignationList($conn);
												$cdacCenters = getCDACCenterList($conn);

												// Start the dropdown HTML for Designation
												echo '
																<div class="col-md-6">
																	<div class="form-floating form-floating-outline">
																		<select id="designation" name="designation" class="select2 form-select"' .
													($_loginInfo["profession"] == "student" ? " disabled" : "") .
													'>
																			<option value="">Select Designation</option>';
												// Loop through the designation list and populate the options
												foreach ($designations as $designationName) {
													$selected =
														strtolower($_loginInfo["designation"]) ==
														strtolower($designationName)
														? "selected"
														: "";
													echo '
																			<option value="' .
														strtolower($designationName) .
														'" ' .
														$selected .
														">" .
														$designationName .
														"</option>";
												}

												// Close the dropdown HTML for Designation
												echo '
																		</select>
																		<label for="designation">Designation</label>
																	</div>
																</div>';

												// Start the dropdown HTML for CDAC Center
												echo '
																<div class="col-md-6">
																	<div class="form-floating form-floating-outline">
																		<select id="location" name="location" class="select2 form-select">
																			<option value="">Select CDAC Center</option>';

												// Loop through the center list and populate the options
												foreach ($cdacCenters as $centerName) {
													$selected =
														strtolower($_loginInfo["location"]) ==
														strtolower($centerName)
														? "selected"
														: "";
													echo '
																			<option value="' .
														strtolower($centerName) .
														'" ' .
														$selected .
														">" .
														$centerName .
														"</option>";
												}

												// Close the dropdown HTML for CDAC Center
												echo '
																		</select>
																		<label for="location">CDAC Center</label>
																	</div>
																</div>';
												?>
											</div>
											<div class="mt-4">
												<button type="submit" class="btn btn-primary me-2">Update Profile</button>
												<!-- <button type="reset" class="btn btn-outline-secondary">Reset</button> -->
											</div>
										</form>
									</div>
									<!-- /Account -->
								</div>
								<?php
								if ($_loginInfo['auth'] === 'self') {
								?>
									<div class="card mb-4">
										<h4 class="card-header">Change Password</h4>
										<!-- Password -->
										<div class="card-body">
											<form id="changePass" method="POST" action="<?php echo pathinfo($_SERVER["PHP_SELF"], PATHINFO_FILENAME); ?>" onsubmit="return false">
												<div class="row gy-4">
													<div class="form-password-toggle">
														<div class="input-group input-group-merge">
															<div class="form-floating form-floating-outline">
																<input type="password" id="old-password" class="form-control" name="old-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" autocomplete="false" required />
																<label for="old-password">Current Password</label>
															</div>
															<span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
														</div>
													</div>
													<div class="form-password-toggle">
														<div class="input-group input-group-merge">
															<div class="form-floating form-floating-outline">
																<input type="password" id="new-password" class="form-control" name="new-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" autocomplete="false" required />
																<label for="new-password">New Password</label>
															</div>
															<span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
														</div>
													</div>
													<div class="form-password-toggle">
														<div class="input-group input-group-merge">
															<div class="form-floating form-floating-outline">
																<input type="password" id="confirm-password" class="form-control" name="confirm-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" autocomplete="false" required />
																<label for="confirm-password">Confirm Password</label>
															</div>
															<span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
														</div>
													</div>
												</div>
												<div class="mt-4">
													<button type="submit" class="btn btn-primary me-2">Change Password</button>
												</div>
											</form>
										</div>
										<!-- /Password -->
									</div>
								<?php
								}
								?>
							</div>
						</div>
						<!-- toast & modal-->
						<?php
						require_once $_SERVER["DOCUMENT_ROOT"] . "/template/toast.php";
						require_once $_SERVER["DOCUMENT_ROOT"] . "/template/modal.php";
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
	if ($submitStatus != "") {
		if ($submitStatus["status"] === true) {
			echo "<script type='text/javascript'>
					showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Account Updated', '" . $submitStatus["message"] . "');
				</script>";
		} elseif ($submitStatus["status"] === false && $submitStatus["message"] === "Please enter valid captcha.") {
			echo "<script type='text/javascript'>
			showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
			</script>";
		} else {
			echo "<script type='text/javascript'>
					showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Account updation Failed', '" . $submitStatus["message"] . "');
				</script>";
		}
	}
	if ($passwordStatus != "") {
		if ($passwordStatus["status"] === true) {
			echo "<script type='text/javascript'>
					showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Invalid Password', '" . $passwordStatus["message"] . "');
				</script>";
		} elseif ($passwordStatus['type'] == 'red' && $passwordStatus['message'] == 'Captcha Verification Failed') {
			echo "<script type='text/javascript'>
				showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
			</script>";
		} elseif ($passwordStatus['type'] == 'yellow') {
			echo "<script type='text/javascript'>
				showToast(5500, 'mdi-alert', 'animate__shakeX', 'text-warning', 'Invalid Password', '" . $passwordStatus["message"] . "');
			</script>";
		} elseif ($passwordStatus['type'] == 'red') {
			echo "<script type='text/javascript'>
					showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Password', '" . $passwordStatus["message"] . "');
				</script>";
		}
	}
	if ($_loginInfo['auth'] === 'self') {
	?>
		<script type="text/javascript">
			// Function to handle form submission for changing password
			document.getElementById("changePass").addEventListener("submit", function(event) {
				event.preventDefault();
				const oldPasswordInput = document.getElementById("old-password");
				const newPasswordInput = document.getElementById("new-password");
				const confirmPasswordInput = document.getElementById("confirm-password");
				const oldPassword = oldPasswordInput.value;
				const newPassword = newPasswordInput.value;
				const confirmPassword = confirmPasswordInput.value;
				const complexityRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/;

				if (oldPassword.length < 1) {
					showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Password', 'Please enter current password.');
					return;
				}

				if (newPassword == oldPassword) {
					showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-warning', 'Invalid Password', 'Old password and new password can not be same.');
					return;
				}

				if (newPassword.length < 10 || !complexityRegex.test(newPassword)) {
					showToast(5500, 'mdi-alert', 'animate__shakeX', 'text-warning', 'Invalid Password', 'Password must be at least 10 characters long and include at least one lowercase letter, one uppercase letter, one number, and one special character.');
					return;
				}

				if (newPassword !== confirmPassword) {
					showToast(5500, 'mdi-alert', 'animate__shakeX', 'text-warning', 'Invalid Password', 'New and confirm passwords must match.');
					return;
				}
				// Show captcha modal
				$('#captcha-modal-display').modal('show');
				// Set up captcha form submission on modal submit
				document.getElementById('captcha-form').addEventListener('submit', function(event) {
					event.preventDefault();
					if (document.getElementById('captcha').value.length == 6) {
						submitFormWithCaptcha('changePass');
					} else {
						showToast(5500, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Invalid Captcha', 'Captcha verification failed. Please try again');
						reloadCaptcha();
					}
				});
			});
		</script>
	<?php
	}
	?>
	<!-- / scripts -->
</body>

</html>