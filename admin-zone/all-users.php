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
			<!-- Menu --> <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/template/side-menu.php"; ?>
			<!-- / Menu -->
			<!-- Layout container -->
			<div class="layout-page">
				<!-- Navbar --> <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/template/navbar.php"; ?>
				<!-- / Navbar -->
				<!-- Content wrapper -->
				<div class="content-wrapper">
					<!-- Content -->
					<?php require_once $_SERVER["DOCUMENT_ROOT"] . "/template/loadingSpinner.php"; ?>
					<div class="container-xxl flex-grow-1 container-p-y">

						<!-- Users List Table -->
						<div class="card">
							<div class="card-header mb-0 pb-0">
								<h5 class="card-title mb-3">Search Filter</h5>
								<div class="d-flex justify-content-between align-items-center row">
									<div class="col-md-4 mb-3 user_profession"></div>
									<div class="col-md-4 mb-3 user_designation"></div>
									<div class="col-md-4 mb-3 user_location"></div>
								</div>
								<div class="d-flex justify-content-between align-items-center row">
									<div class="col-md-4 mb-3 user_role"></div>
									<div class="col-md-4 mb-3 user_status"></div>
								</div>
							</div>
							<div class="card-datatable table-responsive mt-0 pt-0">
								<table class="datatables-users table">
									<thead class="border-top table-light">
										<tr>
											<th></th>
											<th></th>
											<th>User Name</th>
											<th>Email</th>
											<th>Profession</th>
											<th>Designation</th>
											<th>CDAC Center</th>
											<th>Role</th>
											<th>Phone Number</th>
											<th>Account State</th>
											<th>Last Login</th>
											<th>Actions</th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
					<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/template/toast.php'); ?>
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