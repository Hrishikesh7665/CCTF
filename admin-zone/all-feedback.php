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

						<div class="card">
							<h5 class="card-header">Questions Ratings & Feedback</h5>
							<hr class="my-0">
							<div class="card-datatable table-responsive">
								<table class="table" id="QuestionFeedbackTable">
									<thead class="table-light">
										<tr>
											<th>Challenge</th>
											<th class="text-nowrap">User</th>
											<th>Feedback</th>
											<th>Last Update</th>
										</tr>
									</thead>
								</table>
							</div>
						</div>


						<div class="card mt-4">
							<h5 class="card-header">Platform Ratings, Feedback & Areas of Improvement</h5>
							<hr class="my-0">
							<div class="card-datatable table-responsive">
								<table class="table" id="PlatformFeedbackTable">
									<thead class="table-light">
										<tr>
											<!-- <th>Challenge</th> -->
											<th class="text-nowrap">User</th>
											<th>Feedback</th>
											<th>Areas of Improvement</th>
											<th>Last Update</th>
										</tr>
									</thead>
								</table>
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