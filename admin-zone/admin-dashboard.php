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

						<div class="row">
							<div class="mb-4">
								<div class="card">
									<div class="card-body">
										<div class="row">

											<div class="col-6">
												<dl class="row mb-2 g-2">
													<dt class="col-sm-3 mb-2 d-md-flex align-items-center justify-content-start">
														<span class="fw-normal">Server Time:</span>
													</dt>
													<dd class="col-sm-6">
														<span class="fw-normal" id="server-time"></span>
													</dd>
												</dl>
												<dl class="row mb-2 g-2">
													<dt class="col-sm-3 mb-2 d-md-flex align-items-center justify-content-start">
														<span class="fw-normal">CTF Status:</span>
													</dt>
													<dd class="col-sm-6">
														<span class="fw-normal"><?php echo ucwords($comp_state); ?></span>
														<span class="fw-normal" id="remaining-time"></span>
													</dd>
												</dl>
												<dl class="row mb-2 g-2">
													<dt class="col-sm-3 mb-2 d-md-flex align-items-center justify-content-start">
														<span class="fw-normal">Registration :</span>
													</dt>
													<dd class="col-sm-6">
														<div class="form-check form-check-inline">
															<input class="form-check-input" type="radio" name="registration_state" id="inlineRadio1" value="open" <?php if ($registrationStatus === "open") echo 'checked="checked"'; ?> />
															<label class="form-check-label" for="inlineRadio1">Open</label>
														</div>
														<div class="form-check form-check-inline">
															<input class="form-check-input" type="radio" name="registration_state" id="inlineRadio2" value="close" <?php if ($registrationStatus === "close") echo 'checked="checked"'; ?> />
															<label class="form-check-label" for="inlineRadio2">Close</label>
														</div>
													</dd>
												</dl>
											</div>

											<div class="col-6">
												<dl class="row mb-2 g-2">
													<dt class="col-sm-6 mb-2 d-md-flex align-items-center justify-content-end">
														<span class="fw-normal">CTF Start Time:</span>
													</dt>
													<dd class="col-sm-6">
														<div class="input-group">
															<input type="text" class="form-control start-time" placeholder="Select start time" disabled />
															<span class="input-group-text ms-2">
																<i class="mdi mdi-pencil-outline"></i>
															</span>
														</div>
													</dd>
													<dt class="col-sm-6 mb-2 d-md-flex align-items-center justify-content-end">
														<span class="fw-normal">CTF End Time:</span>
													</dt>
													<dd class="col-sm-6">
														<div class="input-group">
															<input type="text" class="form-control end-time" placeholder="Select end time" disabled />
															<span class="input-group-text ms-2">
																<i class="mdi mdi-pencil-outline"></i>
															</span>
														</div>
													</dd>
													<dd class="col-sm-12 d-flex align-items-center justify-content-end">
														<button type="button" id="updateTime" class="btn btn-secondary" onclick="updateCompTime()" disabled>Update Time</button>
													</dd>
												</dl>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">

							<!-- Bar Charts -->
							<div class="col-5 mb-4">
								<div class="card">
									<div class="card-header header-elements">
										<div>
											<h5 class="card-title mb-0">User Login Statistics</h5>
											<small class="text-muted">User Activity Statictic</small>
										</div>
									</div>
									<div class="card-body">
										<canvas id="barChart" class="chartjs" data-height="400"></canvas>
									</div>
								</div>
							</div>
							<!-- /Bar Charts -->

							<!-- Polar Area Chart -->
							<div class="col-7 mb-4">
								<div class="card">
									<div class="card-header header-elements">
										<div>
											<h5 class="card-title mb-0">CDAC Center Locations</h5>
											<small class="text-muted">Center Wise Enrollment Statictic</small>
										</div>
									</div>
									<div class="card-body">
										<canvas id="polarChart" class="chartjs" data-height="400"></canvas>
									</div>
								</div>
							</div>
							<!-- /Polar Area Chart -->

							<!-- Doughnut Chart -->
							<!-- <div class="col-4 mb-4"> -->
							<!-- <div class="card"> -->
							<!-- <h5 class="card-header">User by Devices</h5> -->
							<!-- <div class="card-body"> -->
							<!-- <canvas id="doughnutChart" class="chartjs mb-4" data-height="350"></canvas> -->
							<!-- <ul class="doughnut-legend d-flex justify-content-around ps-0 mb-2 pt-1"> -->
							<!-- <li class="ct-series-0 d-flex flex-column"> -->
							<!-- <h5 class="mb-0">Desktop</h5> -->
							<!-- <span class="badge badge-dot my-2 cursor-pointer rounded-pill" style="background-color: rgb(102, 110, 232);width:35px; height:6px;"></span> -->
							<!-- <div class="text-muted">80 %</div> -->
							<!-- </li> -->
							<!-- <li class="ct-series-1 d-flex flex-column"> -->
							<!-- <h5 class="mb-0">Tablet</h5> -->
							<!-- <span class="badge badge-dot my-2 cursor-pointer rounded-pill" style="background-color: rgb(40, 208, 148);width:35px; height:6px;"></span> -->
							<!-- <div class="text-muted">10 %</div> -->
							<!-- </li> -->
							<!-- <li class="ct-series-2 d-flex flex-column"> -->
							<!-- <h5 class="mb-0">Mobile</h5> -->
							<!-- <span class="badge badge-dot my-2 cursor-pointer rounded-pill" style="background-color: rgb(253, 172, 52);width:35px; height:6px;"></span> -->
							<!-- <div class="text-muted">10 %</div> -->
							<!-- </li> -->
							<!-- </ul> -->
							<!-- </div> -->
							<!-- </div> -->
							<!-- </div> -->
							<!-- /Doughnut Chart -->

							<!-- Line Area Charts -->
							<div class="col-12 mb-4">
								<div class="card">
									<div class="card-header header-elements">
										<div>
											<h5 class="card-title mb-0">Challenge Statistic</h5>
											<small class="text-muted">Challenge Wise Viewed & Solved Statistic</small>
										</div>
									</div>
									<div class="card-body pt-2">
										<canvas id="lineAreaChart" class="chartjs" data-height="450"></canvas>
									</div>
								</div>
							</div>
							<!-- /Line Area Charts -->

							<!-- Line Charts -->
							<div class="col-12 mb-4">
								<div class="card">
									<div class="card-header header-elements">
										<div>
											<h5 class="card-title mb-0">Problem Solving Statistics</h5>
											<small class="text-muted">Challenge Wise Average & Lowest Time Taken Statistic</small>
										</div>
									</div>
									<div class="card-body pt-2">
										<canvas id="lineChart" class="chartjs" data-height="500"></canvas>
									</div>
									<!-- <div class="card-body pb-0 mb-0">
										<p class="fw-light fst-italic">N.B: Values in Seconds</p>
									</div> -->
								</div>
							</div>
							<!-- /Line Charts -->


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