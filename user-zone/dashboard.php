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
					<?php
					// $comp_state = "upcoming";
					if ($comp_state == 'going') {
						echo '<div class="container-xxl flex-grow-1 container-p-y">';
						// Begin of your existing PHP code block
						try {
							$stmt = $conn->prepare("SELECT c_id FROM scoreboard WHERE user_id = ?");
							$stmt->bind_param("i", $login_user_id);
							$stmt->execute();
							$solved_result = $stmt->get_result();
							$solved_ids = $solved_result->fetch_all(MYSQLI_ASSOC);
						} catch (Exception $e) {
							handle_error($e);
						}

						try {
							$sql = "SELECT ch.id, ch.title, ch.score, cat.name AS cat_name FROM challenges AS ch JOIN category AS cat ON ch.cat_id = cat.cat_id ORDER BY ch.cat_id, ch.id";
							$result = mysqli_query($conn, $sql);
							if (!$result) {
								throw new Exception(mysqli_error($conn));
							}
							$challenges = mysqli_fetch_all($result, MYSQLI_ASSOC);
							$count = mysqli_num_rows($result);
						} catch (Exception $e) {
							handle_error($e);
						}

						if ($count > 0) {
							$prev_cat_name = null;
							foreach ($challenges as $challenge) {
								$cat_name = $challenge['cat_name'];
								if ($prev_cat_name !== $cat_name) {
									if ($prev_cat_name !== null) {
										echo "</div>"; // close previous category container
									}
									$prev_cat_name = $cat_name;
									echo '<h5 class="pb-1 mb-4 underline">' . $cat_name . '</h5>';
									echo '<div class="row mb-5">';
								}

								$is_solved_class = in_array($challenge['id'], array_column($solved_ids, 'c_id')) ? "solved" : "points";
					?>
								<div class="col-md-6 col-lg-4">
									<div class="card text-center mb-3 zoom" data-id="<?php echo $challenge['id']; ?>" onclick="openQuestion(<?php echo $challenge['id']; ?>)">
										<div class="card-body">
											<h6 class="card-title"><?php echo ($challenge['title']); ?></h6>
											<p class="p-divider"></p>
											<span class="<?php echo $is_solved_class; ?>"><?php echo $challenge['score']; ?></span>
										</div>
									</div>
								</div>
					<?php
							}
							echo "</div>"; // close last category container
						}
					}
					if ($comp_state == 'upcoming') {
						echo <<<HTML
						<div class="container justify-content-center align-items-center" style="height: 70vh;">
							<p class="txt-center m1-txt1 p-t-33 p-b-68">Competition Will Start In</p>
							<div class="wsize2 flex-w flex-c hsize1 cd100">
								<div class="flex-col-c-m size2 how-countdown">
									<span class="l1-txt1 p-b-9 days" id="upcoming-days">0</span>
									<span class="s1-txt1">Days</span>
								</div>

								<div class="flex-col-c-m size2 how-countdown">
									<span class="l1-txt1 p-b-9 hours" id="upcoming-hours">0</span>
									<span class="s1-txt1">Hours</span>
								</div>

								<div class="flex-col-c-m size2 how-countdown">
									<span class="l1-txt1 p-b-9 minutes" id="upcoming-minutes">0</span>
									<span class="s1-txt1">Minutes</span>
								</div>

								<div class="flex-col-c-m size2 how-countdown">
									<span class="l1-txt1 p-b-9 seconds" id="upcoming-seconds">0</span>
									<span class="s1-txt1">Seconds</span>
								</div>
							</div>
					HTML;
					}
					if ($comp_state == 'end') {
						echo <<<HTML
						<div class="container d-flex justify-content-center align-items-center" style="height: 70vh;">
							<div class="col-md-6 col-lg-4 mb-3">
								<div class="card h-100">
									<div class="card-body text-center">
										<div class="mt-3">
											<h4 class="card-title">Competition Has Ended</h4>
											<p class="card-text">Thank you to everyone who participated!</p>
											<p class="card-text">Stay tuned for more exciting events.</p>
											<p class="card-text">For inquiries, contact us at: <br /> <a href="mailto:iss-kol@cdac.in">iss-kol@cdac.in</a></p>
										</div>
									</div>
								</div>
							</div>
					HTML;
					}
					?>

					<!-- toast & modal-->
					<?php
					require_once($_SERVER['DOCUMENT_ROOT'] . '/template/toast.php');
					require_once($_SERVER['DOCUMENT_ROOT'] . '/template/modal.php');
					basicModal();
					captchaModal();
					IntroModal();
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

	<canvas id="drawing_canvas"></canvas>


	<!-- Drag Target Area To SlideIn Menu On Small Screens -->
	<div class="drag-target"></div>

	</div>
	<!-- / Layout wrapper -->


	<!-- <div class="buy-now"> -->
		<!-- <div class="btn-buy-now"> -->
<!-- TEST -->
		<!-- </div> -->
	<!-- </div> -->

	<!-- scripts -->

	<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/template/scripts-section.php');
	?>
	<!-- / scripts -->


</body>

</html>