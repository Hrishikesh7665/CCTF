<?php
if (
    $_SERVER["REQUEST_METHOD"] == "GET" &&
    realpath(__FILE__) == realpath($_SERVER["SCRIPT_FILENAME"])
) {
    http_response_code(404);
    include $_SERVER["DOCUMENT_ROOT"] . "/404.html";
    exit();
}

// require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

if ($page == "registration" || $page == "reset-password") {
    if ($_loginInfo != false) {
        header("Location: /login");
        die();
    }
} elseif ($_loginInfo or $page == "login") {
    if ($_loginInfo) {
        $role = $_loginInfo["role"];
    }
} else {
    header("Location: /");
    die();
}
?>

<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- Style Switcher -->
            <li class="nav-item dropdown-style-switcher dropdown">
                <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class='mdi mdi-24px'></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                            <span class="align-middle"><i class='mdi mdi-weather-sunny me-2'></i>Light</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                            <span class="align-middle"><i class="mdi mdi-weather-night me-2"></i>Dark</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                            <span class="align-middle"><i class="mdi mdi-monitor me-2"></i>System</span>
                        </a>
                    </li>
                </ul>
                <!-- / Style Switcher-->

                <?php if (
                    $page != "login" &&
                    $page != "registration" &&
                    $page != "reset-password"
                ) {

                    echo <<<HTML

                    <!-- Notification -->
                    <li class="nav-item dropdown-notifications navbar-dropdown dropdown">
                        <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <i class='mdi mdi-24px mdi-bell-outline'></i>
                            <div class="notificationBell flash_notification d-none" id="notification-badge"></div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end py-0" id="notification-panel-body">
                            <li class="dropdown-menu-header border-bottom">
                                <div class="dropdown-header d-flex align-items-center py-3">
                                    <h6 class="mb-0 me-auto">Notification</h6>
                                    <div class="d-flex align-items-center">
                                        <span class="badge rounded-pill bg-label-primary d-none" id="notification-count"></span>
                                    </div>
                                </div>
                            </li>
                            <li class="dropdown-menu-body">
                                <div class="text-center py-4">
                                    <i class="mdi mdi-information-outline mdi-36px text-muted mb-2"></i>
                                    <p class="mb-0 text-muted">No Notifications</p>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!--/ Notification -->

                    <!-- Quick links -->
                    <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-1 me-xl-0">
                        <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <i class='mdi mdi-view-grid-outline mdi-24px'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end py-0">
                            <div class="dropdown-menu-header border-bottom">
                                <div class="dropdown-header d-flex align-items-center py-3">
                                    <h6 class="mb-0 me-auto">Quick Links</h6>
                                </div>
                            </div>
                            <div class="dropdown-shortcuts-list scrollable-container">
                            <div class="row row-bordered overflow-visible g-0">
HTML;

                    if ($_loginInfo['role'] == 'User') {
                        echo <<<HTML
                        <div class="dropdown-shortcuts-item col">
                            <span class="dropdown-shortcuts-icon bg-label-secondary text-heading rounded-circle mb-3"><i class="mdi mdi-monitor mdi-24px"></i></span>
                            <a href="/user-zone/dashboard" class="stretched-link">Dashboard</a>
                            <small>Main Page</small>
                        </div>
                        <div class="dropdown-shortcuts-item col">
                            <span class="dropdown-shortcuts-icon bg-label-secondary text-heading rounded-circle mb-3"><i
                                    class="mdi mdi-trophy-outline mdi-24px"></i></span>
                            <a href="/scoreboard" class="stretched-link">Scoreboard</a>
                            <small>Leaderboard</small>
                        </div>
                    </div>
                    <div class="row row-bordered overflow-visible g-0">
                        <div class="dropdown-shortcuts-item col">
                            <span class="dropdown-shortcuts-icon bg-label-secondary text-heading rounded-circle mb-3"><i class="mdi mdi-comment-quote-outline mdi-24px"></i></span>
                            <a href="/user-zone/feedback" class="stretched-link">Feedback</a>
                            <small>Your Opinion/Feedback</small>
                        </div>
                        <div class="dropdown-shortcuts-item col">
                            <span class="dropdown-shortcuts-icon bg-label-secondary text-heading rounded-circle mb-3"><i class="mdi mdi-help-circle-outline mdi-24px"></i></span>
                            <a href="/faq" class="stretched-link">FAQs</a>
                            <small class="text-muted mb-0">FAQs & Articles</small>
                        </div>
                    </div>

HTML;
                    }
                    if ($_loginInfo['role'] == 'Admin') {
                        echo <<<HTML
                <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary text-heading rounded-circle mb-3"><i class="mdi mdi-monitor mdi-24px"></i></span>
                    <a href="/user-zone/dashboard" class="stretched-link">Admin Dashboard</a>
                    <small>Main Page</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary text-heading rounded-circle mb-3"><i
                            class="mdi mdi-trophy-outline mdi-24px"></i></span>
                    <a href="/scoreboard" class="stretched-link">Scoreboard</a>
                    <small>Leaderboard</small>
                </div>
                <!-- <div class="row row-bordered overflow-visible g-0"> -->
                    <!-- <div class="dropdown-shortcuts-item col"> -->
                        <!-- <span class="dropdown-shortcuts-icon bg-label-secondary text-heading rounded-circle mb-3"><i class="mdi mdi-cog-outline mdi-24px"></i></span> -->
                        <!-- <a href="/account" class="stretched-link">Setting</a> -->
                        <!-- <small>Account Settings</small> -->
                    <!-- </div> -->
                    <!-- <div class="dropdown-shortcuts-item col"> -->
                        <!-- <span class="dropdown-shortcuts-icon bg-label-secondary text-heading rounded-circle mb-3"><i class="mdi mdi-help-circle-outline mdi-24px"></i></span> -->
                        <!-- <a href="/faq" class="stretched-link">FAQs</a> -->
                        <!-- <small class="text-muted mb-0">FAQs & Articles</small> -->
                    <!-- </div> -->
                <!-- </div> -->
HTML;
                    }

                    echo <<<HTML
        </div>
    </div>
</li>
<!-- User -->
<li class="nav-item navbar-dropdown dropdown-user dropdown">
    <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
            <img src="{$_loginInfo["displaypic"]}" alt class="w-px-40 h-auto rounded-circle userAvatar" style="height: 100% !important;width: 100% !important;">
        </div>
    </a>
    <ul class="dropdown-menu dropdown-menu-end mt-3 py-2">
        <li>
            <a class="dropdown-item pb-2 mb-1" href="/account">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-2 pe-1">
                        <div class="avatar avatar-online">
                            <img src="{$_loginInfo["displaypic"]}" alt class="w-px-40 h-auto rounded-circle userAvatar" style="height: 100% !important;width: 100% !important;">
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{$_loginInfo["username"]}</h6>
                        <small class="text-muted">{$_loginInfo["role"]}</small>
                    </div>
                </div>
            </a>
        </li>
        <li>
            <div class="dropdown-divider my-0"></div>
        </li>
        <li><a class="dropdown-item" href="/account"><i class="mdi mdi-account-outline me-1 mdi-20px"></i><span class="align-middle">My Profile</span></a></li>
HTML;
                    if ($_loginInfo["role"] == "User") {
                        echo '<li><a class="dropdown-item" href="/user-zone/certificate"><i class="mdi mdi-trophy-award me-1 mdi-20px"></i><span class="align-middle">Certificate</span></a></li>';
                        echo '<li><a class="dropdown-item" href="/user-zone/activity"><i class="mdi mdi-chart-timeline-variant me-1 mdi-20px"></i><span class="align-middle">Activity</span></a></li>';
                    }
                    echo <<<HTML
    <li><a class="dropdown-item" href="/logout"><i class="mdi mdi-logout me-1 mdi-20px"></i><span class="align-middle">Log Out</span></a></li>
    </ul>
    </li>
    <!--/ User -->
HTML;
                } ?>
        </ul>
    </div>
</nav>