<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/template/head.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/common/variables.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";
?>

<body class="spin-lock">

	<!-- Navbar -->
	<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/template/navbar.php');
	require_once $_SERVER["DOCUMENT_ROOT"] . "/template/loadingSpinner.php";
	?>
	<!-- / Navbar -->

	<!-- Content -->
	<?php

	if (isset($_SESSION['registrationData']) && isset($_SESSION['registrationAction'])) {
		$keysToCheck = array('userFullName', 'userEmail', 'userPhoneNumber', 'userProfession', 'userCenter', 'exp');
		$allKeysExist = allKeysExistInRegistrationData($keysToCheck, $_SESSION['registrationData']);
		if ($_SESSION['registrationAction'] === 'ask-ldapPassword' && $allKeysExist) {
			// var_dump($_SESSION['registrationData']['exp']);
			// echo '<br>';
			// var_dump(time());
			// die();
			if (time() < $_SESSION['registrationData']['exp']) {
				require_once($_SERVER['DOCUMENT_ROOT'] . '/template/askLdapPassword.php');
				exit();
			} else {
				unset($_SESSION['registrationData']);
				unset($_SESSION['registrationAction']);
				$remark = 'session expired';
			}
		}
	}
	?>

	<div class="position-relative" style="height:calc(95vh - 70px)">
		<div class="authentication-wrapper authentication-cover">

			<div class="authentication-inner row m-0">

				<!--  Multi Steps Registration -->
				<div class="d-flex align-items-center justify-content-center authentication-bg pt-4 mt-4">
					<div class="w-px-700 mt-5 mt-lg-0">
						<div id="multiStepsRegistration" class="bs-stepper wizard-numbered shadow-none">
							<div class="bs-stepper-header border-bottom-0">
								<div class="step" data-target="#accountDetailsValidation">
									<button type="button" class="step-trigger">
										<span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
										<span class="bs-stepper-label">
											<span class="bs-stepper-number">01</span>
											<span class="d-flex flex-column gap-1 ms-2">
												<span class="bs-stepper-title">Account</span>
												<span class="bs-stepper-subtitle">Account Details</span>
											</span>
										</span>
									</button>
								</div>
								<div class="line"></div>
								<div class="step" data-target="#personalInfoValidation">
									<button type="button" class="step-trigger">
										<span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
										<span class="bs-stepper-label">
											<span class="bs-stepper-number">02</span>
											<span class="d-flex flex-column gap-1 ms-2">
												<span class="bs-stepper-title">Personal</span>
												<span class="bs-stepper-subtitle">Enter Information</span>
											</span>
										</span>
									</button>
								</div>
								<div class="line"></div>
								<div class="step" data-target="#finalValidation">
									<button type="button" class="step-trigger">
										<span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
										<span class="bs-stepper-label">
											<span class="bs-stepper-number">03</span>
											<span class="d-flex flex-column gap-1 ms-2">
												<span class="bs-stepper-title">Validation</span>
												<span class="bs-stepper-subtitle">Validate Your Details</span>
											</span>
										</span>
									</button>
								</div>
							</div>
							<div class="bs-stepper-content">
								<form id="multiStepsForm" onSubmit="return false">
									<!-- Account Details -->
									<div id="accountDetailsValidation" class="content">
										<div class="content-header mb-3">
											<h4 class="mb-0">Account Information</h4>
											<small>Enter Your Account Details</small>
										</div>
										<div class="row g-3">
											<div class="col-sm-6">
												<div class="form-floating form-floating-outline">
													<input type="text" name="multiStepsFullName" id="multiStepsFullName" class="form-control" placeholder="Please enter your full name" autocomplete="off" />
													<label for="multiStepsFullName">Full Name</label>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-floating form-floating-outline">
													<input type="email" name="multiStepsEmail" id="multiStepsEmail" class="form-control" placeholder="Please enter your email address" aria-label="Please enter your email address" autocomplete="off" />
													<label for="multiStepsEmail">Email</label>
												</div>
											</div>
											<div class="col-sm-6 form-password-toggle">
												<div class="input-group input-group-merge">
													<div class="form-floating form-floating-outline">
														<input type="password" id="multiStepsPass" name="multiStepsPass" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="multiStepsPass2" autocomplete="off" />
														<label for="multiStepsPass">Password</label>
													</div>
													<span class="input-group-text cursor-pointer" id="multiStepsPass2"><i class="mdi mdi-eye-off-outline"></i></span>
												</div>
											</div>
											<div class="col-sm-6 form-password-toggle">
												<div class="input-group input-group-merge">
													<div class="form-floating form-floating-outline">
														<input type="password" id="multiStepsConfirmPass" name="multiStepsConfirmPass" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="multiStepsConfirmPass2" autocomplete="off" />
														<label for="multiStepsConfirmPass">Confirm Password</label>
													</div>
													<span class="input-group-text cursor-pointer" id="multiStepsConfirmPass2"><i class="mdi mdi-eye-off-outline"></i></span>
												</div>
											</div>
											<div class="col-12 d-flex justify-content-between">
												<button class="btn btn-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
													<span class="align-middle d-sm-inline-block d-none">Previous</span>
												</button>
												<button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="mdi mdi-arrow-right"></i></button>
											</div>
										</div>
									</div>
									<!-- Personal Info -->
									<div id="personalInfoValidation" class="content">
										<div class="content-header mb-3">
											<h4 class="mb-0">Personal Information</h4>
											<small>Enter Your Personal Information</small>
										</div>
										<div class="row g-3">
											<div class="col-sm-6">
												<div class="input-group input-group-merge">
													<span class="input-group-text">IN (+91)</span>
													<div class="form-floating form-floating-outline">
														<input type="number" id="multiStepsMobile" name="multiStepsMobile" class="form-control multi-steps-mobile" placeholder="98554147861" minlength="10" maxlength="10" pattern="[1-9][0-9]{9}" autocomplete="off" />
														<label for="multiStepsMobile">Mobile</label>
													</div>
												</div>
											</div>

											<?php
											$designations = getDesignationList($conn);
											$cdacCenters = getCDACCenterList($conn); ?>

											<div class="col-sm-6">
												<div class="form-floating form-floating-outline">
													<select id="multiStepsCenter" class="select2 form-select" name="multiStepsCenter">
														<option value="">Select your CDAC Center</option>
														<?php
														foreach ($cdacCenters as $cdacCenter) {
															echo '<option value="' . $cdacCenter . '">' . ucfirst($cdacCenter) . '</option>';
														}
														?>
													</select>
													<label for="multiStepsCenter">Select CDAC Center</label>
												</div>
											</div>

											<div class="col-sm-6">
												<div class="form-floating form-floating-outline">
													<select id="multiStepsDesignation" class="select2 form-select" name="multiStepsDesignation">
														<option value="">Select your Designation</option>
														<?php
														foreach ($designations as $designation) {
															echo '<option value="' . $designation . '">' . ucfirst($designation) . '</option>';
														}
														?>
													</select>
													<label for="multiStepsDesignation">Select Designation</label>
												</div>
											</div>

											<div class="col-12 d-flex justify-content-between">
												<button class="btn btn-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
													<span class="align-middle d-sm-inline-block d-none">Previous</span>
												</button>
												<button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="mdi mdi-arrow-right"></i></button>
											</div>
										</div>
									</div>
									<!-- Final Verification -->
									<div id="finalValidation" class="content">
										<div class="content-header mb-3">
											<h4 class="mb-0">Validate Your Information</h4>
											<small>Please re-check and confirm your details</small>
										</div>
										<div class="row g-3">
											<div class="col-md-12">
												<table class="table table-borderless table-striped">
													<tbody>
														<tr>
															<td class="fw-bold">Your Full Name:</td>
															<td></td>
														</tr>
														<tr>
															<td class="fw-bold">Your Email Address:</td>
															<td></td>
														</tr>
														<tr>
															<td class="fw-bold">Your Phone Number:</td>
															<td></td>
														</tr>
														<tr>
															<td class="fw-bold">Your Profession:</td>
															<td></td>
														</tr>
														<tr>
															<td class="fw-bold">Your Designation:</td>
															<td></td>
														</tr>
														<tr>
															<td class="fw-bold">Your CDAC Center:</td>
															<td></td>
														</tr>
													</tbody>
												</table>
											</div>
											<div class="col-md-12">
												<label class="form-check m-0">
													<input type="checkbox" class="form-check-input checkbox-col" id="finalCheckbox" name="finalCheckbox" onchange="toggleFinalCheckbox()" autocomplete="off">
													<span class="form-check-label">By checking this box, I affirm the accuracy of the details provided above. I understand that any inaccuracies may result in disqualification from accessing or utilizing this platform at any time. I agree that this affirmation constitutes a binding agreement between myself and the platform.</span>
												</label>
											</div>
											<div class="col-12 d-flex justify-content-between">
												<button class="btn btn-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
													<span class="align-middle d-sm-inline-block d-none">Previous</span>
												</button>
												<button type="submit" class="btn btn-primary btn-next btn-submit" disabled>Submit</button>
											</div>
										</div>
									</div>
								</form>
							</div>
							<!-- Modal -->
							<?php
							require_once $_SERVER["DOCUMENT_ROOT"] . "/template/toast.php";
							require_once $_SERVER["DOCUMENT_ROOT"] . "/template/modal.php";
							captchaModal();
							?>
							<div class="modal fade" id="welcome-modal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
								<div class="modal-dialog modal-lg modal-dialog-scrollable">
									<div class="modal-content">
										<div class="modal-body">
											<h2>Welcome to the CTF Competition!</h2>
											<p class="fw-bold">
												Capture the Flag (CTF) competitions in computer security are engaging exercises where participants hunt for "flags" concealed within intentionally vulnerable programs or websites. Our CTF follows a Jeopardy-style format, where competitors tackle challenges by deciphering clues and solving tasks in a strategic sequence. Points are awarded for each completed task, with higher points reserved for more difficult challenges.
											</p>
											<p>
												<span class="h5 fw-bolder">Rules:</span>
											<ol>
												<li>The competition will commence at the scheduled time and automatically conclude after the scheduled time. Only flags submitted within this period will be considered for scoring.</li>
												<li>Usage of automated scanning tools is strictly prohibited and may result in immediate disqualification.</li>
												<li>Brute-forcing flags is strictly forbidden and may lead to disqualification.</li>
												<li>Multiple logins from the same participant are not permitted.</li>
												<li>Discussion about the steps to solve the challenge is strictly prohibited.</li>
												<li>Flag sharing is strictly prohibited.</li>
												<li>Any detected malicious activity associated with an account will prompt an automatic review, potentially resulting in disqualification.</li>
											</ol>
											</p>
											<p>
												<span class="h5 fw-bolder">Terms & Conditions:</span>
											<ol>
												<li><strong>Jurisdiction:</strong> Our platform operates under the jurisdiction of India.</li>
												<li>
													<strong>Collection of Personally Identifiable Information (PII) and Sensitive Personally Identifiable Information (SPII):</strong> We may collect certain PII and SPII from users for various purposes including but not limited to account registration, providing personalized services, and improving user experience. This may include information such as name, email address, phone number. We collect this information solely for the purpose of providing and improving our services to you.
												</li>
												<li>
													<strong>Protection of PII/SPII:</strong> We take appropriate technical and organizational measures to protect the confidentiality, integrity, and availability of your PII/SPII. This includes encryption of data, restricted access to authorized personnel only, and regular security audits.
												</li>
												<!-- <li> -->
												<!-- <strong>Sharing of PII/SPII with Third Parties:</strong> We may share your PII/SPII with third parties in the following circumstances: -->
												<!-- <ul> -->
												<!-- <li>With your consent.</li> -->
												<!-- <li>To comply with legal obligations or respond to lawful requests from governmental authorities.</li> -->
												<!-- <li>To protect our rights, property, or safety, or the rights, property, or safety of others.</li> -->
												<!-- </ul> -->
												<!-- </li> -->
												<li>
													<strong>User Rights and Exercise of Rights:</strong> As a user, you have the right to:
													<ul>
														<li>Access, correct, or update your PII/SPII.</li>
														<li>Object to the processing of your PII/SPII.</li>
														<li>Withdraw consent for the collection or processing of your PII/SPII. To exercise these rights or for any inquiries regarding your PII/SPII, please contact us through the provided contact information.</li>
													</ul>
												</li>
												<li>
													<strong>Changes to Terms and Conditions:</strong> We reserve the right to modify these terms and conditions at any time, with or without notice. Any changes will be effective immediately upon posting the updated terms and conditions on our platform. It is your responsibility to review these terms periodically for any updates. Your continued use of our platform after any modifications indicates your acceptance of the revised terms.
												</li>
											</ol>
											</p>
											<style>
												.selected-label::after {
													content: "\2714";
													/* Unicode for checkmark symbol */
													margin-left: 2.5px;
													color: green;
												}
											</style>
											<p>
												<span class="blinking-emoji">👉</span> I agree to the rules and regulations and hereby declare myself as a/an
													<br />

													<label>
														<span style="white-space: nowrap;" class="ms-4">
															<input type="checkbox" class="form-check-input checkbox-col" id="studentCheckbox" onchange="toggleRole('student')" autocomplete="off">
															<span id="studentLabel" class="fw-bolder pointer blink_me">Student</span>
															<br />
														<input type="checkbox" class="form-check-input checkbox-col ms-4" id="employeeCheckbox" onchange="toggleRole('employee')" autocomplete="off">
														<span id="employeeLabel" class="fw-bolder pointer blink_me">Employee</span>
													</label></span>
													<br />
													<span id="removeSelect" class="ms-4">(select any) of <a class="text-decoration-none" href="https://www.cdac.in">CDACINDIA</a>.</span>
											</p>
											<p>
											<div class="mb-3">
												<label class="form-check m-0">
													<input type="checkbox" class="form-check-input checkbox-col" id="tc-checkbox" onchange="toggleTC()">
													<span class="form-check-label">I agree to the Terms and Conditions</span>
												</label>
											</div>
											</p>
										</div>

										<div class="modal-footer">
											<button type="button" class="btn btn-danger" onclick="closeTab()">Exit</button>
											<button type="button" id="confirm-btn" class="btn btn-success" disabled>Confirm</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<img src="/assets/img/CDAC-CTF.png" alt="cdacCTF-Logo" class="authentication-image-object-left d-none d-lg-block login-logo-container-img">
				</div>
				<!-- / Multi Steps Registration -->
			</div>
		</div>
	</div>

	<!-- Footer -->
	<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/template/footer.php');
	?>
	<!-- / Footer -->

	<!-- scripts -->

	<?php
	$requiredJs = 1;
	require_once($_SERVER['DOCUMENT_ROOT'] . '/template/scripts-section.php');

	if (isset($remark))
		if ($remark == 'session expired') {
			echo "<script type='text/javascript'>showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-warning', 'Session Expired!!', 'Session Expired, Please Try Again');</script>";
		}
	?>
	<!-- / scripts -->
</body>

</html>