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
				<?php require_once $_SERVER["DOCUMENT_ROOT"] . "/template/loadingSpinner.php"; ?>
					<!-- Content -->
					<div class="container-xxl flex-grow-1 container-p-y">
						<div class="card mb-4">
							<h4 class="card-header">CTF Scoreboard</h4>
							<div class="card-body pt-2 mt-1">
								<?php
								$scoreboardData = fetchScoreboardData();
								if ($scoreboardData['status']) {
								?>
									<div class="table-responsive text-nowrap">
										<table class="table table-hover table-striped">
											<thead class="table-dark center">
												<tr>
													<th class="text-center">Rank</th>
													<th class="text-center">Player</th>
													<th class="text-center">Solved</th>
													<th class="text-center">Score</th>
													<th class="text-center">Time</th>
													<th class="text-center">Status</th>
												</tr>
											</thead>
											<tbody>
												<?php
												function ordinalSuffix($num)
												{
													$suffix = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
													if (($num % 100) >= 11 && ($num % 100) <= 13) {
														return $num . 'th';
													} else {
														return $num . $suffix[$num % 10];
													}
												}

												$medals = ['🥇', '🥈', '🥉']; // Emojis for gold, silver, and bronze medals

												foreach ($scoreboardData['data'] as $key => $player) :
												?>
													<tr>
														<td class="text-center">
															<?php
															if ($key < 3) {
																echo $medals[$key];
															} else {
																echo ordinalSuffix($key + 1);
															}
															?>
														</td>
														<td class="text-center"><?= $player['name'] ?></td>
														<td class="text-center"><?= $player['solved'] ?></td>
														<td class="text-center"><?= $player['score'] ?></td>
														<td class="text-center"><?= date("d-m-Y h:i:s A", strtotime($player['time'])) ?></td>
														<td class="text-center"><?= $player['status'] === "true" ? "Active" : "Inactive" ?></td>
													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
								<?php } else { ?>
									<div class="alert alert-warning" role="alert">
										<h5 class="alert-heading">No Data Available</h5>
										<p>There is currently no data available. Please check back later.</p>
									</div>
								<?php } ?>
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