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

                        <div class="card">
                            <h5 class="card-header pb-6 ms-2">Notifications</h5>
                            <div class="card-body">
                                <ul class="timeline timeline-outline mb-0 ms-3" id="notification-timeline">
                                    <!-- Notification items will be appended here dynamically -->
                                </ul>
                            </div>
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

<script>
function fetchNotifications() {
    // Make an AJAX POST request to fetch notifications
    $.ajax({
        url: '/api/test',  // Make sure this is the correct API URL
        method: 'POST',
        dataType: 'json',  // Expect JSON response
        success: function(response) {
            // Check if response contains notifications
            const timeline = $('#notification-timeline');
            const notifications = response.notifications;


            notifications.forEach(notification => {
                // Determine the viewed status for styling
                const viewedClass = notification.viewed ? 'timeline-point-primary' : 'timeline-point-info';
                // let description = new DOMParser().parseFromString(notification.description, "text/html").body.textContent;
                // description = description.replace(/\\"/g, '"');

                // Build the notification item
                const notificationItem = `
                    <li class="timeline-item timeline-item-transparent border-left-dashed">
                        <span class="timeline-point ${viewedClass}"></span>
                        <div class="timeline-event">
                            <div class="timeline-header mb-3">
                                <h6 class="mb-0">${notification.title}</h6>
                                <small class="text-muted">${notification.activeTime}</small>
                            </div>
                            <p class="mb-2">${notification.description}</p>
                            <div class="d-flex align-items-center mb-1">
                                <div class="badge bg-lightest">
                                    <img src="../../assets/img/icons/misc/pdf.png" alt="img" width="15" class="me-2">
                                    <span class="h6 mb-0">Download</span>
                                </div>
                            </div>
                        </div>
                    </li>
                `;
                timeline.append(notificationItem);
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching notifications:', status, error);
        }
    });
}

// Call the function to fetch and display notifications
fetchNotifications();

</script>

    <!-- / scripts -->
</body>

</html>