<?php
header('Cache-Control: no-cache, must-revalidate');

require_once($_SERVER['DOCUMENT_ROOT'] . '/template/head.php');

if ($_loginInfo) {
	if ($_loginInfo['role'] == 'Admin') {
		header("Location: /admin-zone/admin-dashboard");
		die();
	} elseif ($_loginInfo['role'] == 'User') {
		header("Location: /user-zone/dashboard");
		die();
	}
}

$loginStatus = '';

// Simple Login handling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['captcha'])) {
	require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/common/functions.php');

	$email = ($_POST['email']);
	$password = ($_POST['password']);
	$userEnteredCaptcha = ($_POST['captcha']);

	// Verify captcha
	if (isset($_SESSION['captcha']) && !empty($userEnteredCaptcha) && $userEnteredCaptcha === $_SESSION['captcha']) {
		// Captcha verification passed
		$simpleLoginResult = loginSimple($email, $password);

		if ($simpleLoginResult['status'] === true) {
			// Successful login
			if (is_null($simpleLoginResult['data']['displayPic'])) {
				$userPic = '/assets/img/avatars/defaultAvatar.png';
			} else {
				$userPic = '/assets/img/avatars/' . $simpleLoginResult['data']['displayPic'];
			}
			setCustomSession($simpleLoginResult['data']['name'], $simpleLoginResult['data']['email'], $simpleLoginResult['data']['id'], $simpleLoginResult['data']['role'], $simpleLoginResult['data']['hash'], $simpleLoginResult['data']['profession'], $simpleLoginResult['data']['designation'], $simpleLoginResult['data']['location'], $simpleLoginResult['data']['phoneNumber'], $userPic, $simpleLoginResult['data']['auth']);
			if ($simpleLoginResult['data']['role'] === 'admin') {
				header('Location: /admin-zone/admin-dashboard');
				exit();
			} elseif ($simpleLoginResult['data']['role'] === 'user') {
				header('Location: /user-zone/dashboard');
				exit();
			}
		} else {
			$loginStatus = array('status' => false, 'message' => $simpleLoginResult['message']);
		}
	} else {
		$loginStatus = array('status' => false, 'message' => 'Captcha verification failed.');
	}
}


//Ldap Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login-ldap']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['captcha'])) {
	require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/common/functions.php');

	$email = ($_POST['email']);
	$password = ($_POST['password']);
	$userEnteredCaptcha = ($_POST['captcha']);

	// Verify captcha
	if (isset($_SESSION['captcha']) && !empty($userEnteredCaptcha) && $userEnteredCaptcha === $_SESSION['captcha']) {
		// Captcha verification passed

		try {
			$sql = "SELECT `id`, `name`, `email`, `password`, `role`, `status`, `profession`, (SELECT list__designation.designation FROM list__designation WHERE list__designation.designation_id=users.designation) as designation, `phoneNumber`, `displayPic`, `auth_type`, `creation_ts`, (SELECT list__center.center FROM list__center WHERE list__center.center_id=users.location) as location FROM users WHERE email = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("s", $email);
			$stmt->execute();
			$result = $stmt->get_result();
			$count = mysqli_num_rows($result);
		} catch (Exception $e) {
			handle_error($e);
			die();
		}

		if ($result->num_rows > 0) {
			// Fetch the first row
			$row = $result->fetch_assoc();
			$id = $row['id'];
			$status = $row['status'];
			$ldapStatus = loginLDAP($email, $password);

			if ($ldapStatus['status']) {
				// Ldap auth successful
				if ($row['status'] == 'true') {
					$logEntry = insertLog($conn, $row['id'], 'Login Success');
					if ($logEntry['status'] === true) {
						$user = array(
							'data' => array(
								'name' => $row['name'],
								'id' => $id,
								'role' => $row['role'],
								'email' => $row['email'],
								'profession' => $row['profession'],
								'designation' => $row['designation'],
								'location' => $row['location'],
								'phoneNumber' => $row['phoneNumber'],
								'displayPic' => $row['displayPic'],
								'auth' => $row['auth_type'],
								'hash' => $logEntry['hash']
							)
						);
						if (is_null($user['data']['displayPic'])) {
							$userPic = '/assets/img/avatars/defaultAvatar.png';
						} else {
							$userPic = '/assets/img/avatars/' . $user['data']['displayPic'];
						}
						setCustomSession($user['data']['name'], $user['data']['email'], $user['data']['id'], $user['data']['role'], $user['data']['hash'], $user['data']['profession'], $user['data']['designation'], $user['data']['location'], $user['data']['phoneNumber'], $userPic, $user['data']['auth']);
						if ($user['data']['role'] === 'admin') {
							header('Location: /admin-zone/admin-dashboard');
							exit();
						} elseif ($user['data']['role'] === 'user') {
							header('Location: /user-zone/dashboard');
							exit();
						}
					} else {
						$loginStatus = array('status' => false, 'message' => 'Add to log failed');
					}
				} else {
					insertLog($conn, $row['id'], 'Login Attempted While User Not Activated');
					$loginStatus = array('status' => false, 'message' => 'User not active');
				}
			} else if ($ldapStatus['status'] === 'LDAP authentication failed') {
				insertLog($conn, $id, 'Invalid login Attempt');
				$loginStatus = array('status' => false, 'message' => 'Invalid email or password');
			} else {
				$loginStatus = array('status' => false, 'message' => $ldapStatus['message']);
			}
		} else {
			$loginStatus = array('status' => false, 'message' => 'Invalid email or password');
		}
	} else {
		$loginStatus = array('status' => false, 'message' => 'Captcha verification failed.');
	}
}
?>

<body class="spin-lock">
	<!-- Navbar -->
	<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/template/navbar.php');
	require_once $_SERVER["DOCUMENT_ROOT"] . "/template/loadingSpinner.php";
	?>
	<!-- / Navbar -->

	<div class="position-relative" style="height:calc(95vh - 70px)">
		<div class="authentication-wrapper authentication-basic login">
			<div class="authentication-inner">

				<!-- Login -->
				<div class="row">
					<div class="col">
						<div class="card">

							<div class="card-header overflow-hidden">
								<ul class="nav nav-tabs justify-content-center" role="tablist">
									<li class="nav-item">
										<button id="student-tab" class="nav-link active" data-bs-toggle="tab" data-bs-target="#login-tabs-student" role="tab" aria-selected="true">
											<span class="ri-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Student Login</span>
										</button>
									</li>
									<li class="nav-item">
										<button id="employee-tab" class="nav-link" data-bs-toggle="tab" data-bs-target="#login-tabs-employee" role="tab" aria-selected="false">
											<span class="ri-folder-user-line ri-20px d-sm-none"></span><span class="d-none d-sm-block">Employee Login</span>
										</button>
									</li>
								</ul>
							</div>


							<div class="tab-content p-1">
								<div class="tab-pane fade active show" id="login-tabs-student" role="tabpanel">
									<div class="card-body pt-0">
										<form id="formStudentAuthentication" class="mb-3" action="<?php echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME); ?>" method="POST" role="form">
											<div class="form-floating form-floating-outline mb-3">
												<input type="text" class="form-control" id="student-email" name="email" placeholder="Enter your email address" autocomplete="nope" required />
												<label for="student-email">Email</label>
											</div>
											<div class="mb-3">
												<div class="form-password-toggle">
													<div class="input-group input-group-merge">
														<div class="form-floating form-floating-outline">
															<input type="password" id="student-password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="student-password" autocomplete="false" required />
															<label for="student-password">Password</label>
														</div>
														<span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
													</div>
												</div>
											</div>
											<!-- captcha -->
											<div class="mb-2">
												<label for="student-captcha" class="fw-bold">Captcha Image</label>
												<div class="d-flex align-items-center">
													<img src="/captcha" alt="Captcha" id="student-captchaImage" class="me-2">
													<button type="button" onclick="reloadCaptcha('student-captchaImage')" class="btn btn-link">
														<i class="mdi mdi-reload"></i>
													</button>
												</div>
											</div>
											<div class="form-floating form-floating-outline mb-2">
												<input type="text" class="form-control" id="student-captcha" name="captcha" placeholder="Enter the captcha" autocomplete="off" required>
												<label for="student-captcha">Captcha</label>
											</div>
											<!-- captcha -->
											<div class="mb-2 d-flex justify-content-center">
												<a href="/reset-password" class="float-end mb-1" id="student-forgotPassword">
													<span>Forgot Password?</span>
												</a>
											</div>
											<div class="mb-1">
												<button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
											</div>
										</form>
									</div>
								</div>

								<div class="tab-pane fade" id="login-tabs-employee" role="tabpanel">
									<div class="card-body pt-0">
										<form id="formEmployeeAuthentication" class="mb-3" action="<?php echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME); ?>" method="POST" role="form">
											<div class="form-floating form-floating-outline mb-3">
												<input type="text" class="form-control" id="employee-email" name="email" placeholder="Enter your email address" autocomplete="nope" required />
												<label for="employee-email">Email</label>
											</div>
											<div class="mb-3">
												<div class="form-password-toggle">
													<div class="input-group input-group-merge">
														<div class="form-floating form-floating-outline">
															<input type="password" id="employee-password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="employee-password" autocomplete="false" required />
															<label for="employee-password">Password</label>
														</div>
														<span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
													</div>
												</div>
											</div>
											<!-- captcha -->
											<div class="mb-2">
												<label for="employee-captcha" class="fw-bold">Captcha Image</label>
												<div class="d-flex align-items-center">
													<img src="" alt="Captcha" id="employee-captchaImage" class="me-2">
													<button type="button" onclick="reloadCaptcha('employee-captchaImage')" class="btn btn-link">
														<i class="mdi mdi-reload"></i>
													</button>
												</div>
											</div>
											<div class="form-floating form-floating-outline mb-3">
												<input type="text" class="form-control" id="employee-captcha" name="captcha" placeholder="Enter the captcha" autocomplete="off" required>
												<label for="employee-captcha">Captcha</label>
											</div>
											<!-- captcha -->
											<!-- <div class="mb-3 d-flex justify-content-between"> -->
											<!-- <a href="/reset-password" class="float-end mb-1" id="employee-forgotPassword"> -->
											<!-- <span>Forgot Password?</span> -->
											<!-- </a> -->
											<!-- </div> -->
											<div class="mb-2">
												<button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<img src="/assets/img/CDAC-CTF.png" alt="cdacCTF-Logo" class="authentication-image-object-left d-none d-lg-block login-logo-container-img">
			</div>
		</div>
	</div>

	<!-- toast & modal-->
	<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/template/toast.php');
	?>

	<!-- Footer -->
	<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/template/footer.php');
	?>
	<!-- / Footer -->

	<!-- scripts -->
	<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/template/scripts-section.php');
	if ($loginStatus != '') {
		echo "<script type='text/javascript'>showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Try Again!!', '" . $loginStatus['message'] . "');</script>";
	}
	?>
</body>