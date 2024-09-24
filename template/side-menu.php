<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(404);
    include($_SERVER["DOCUMENT_ROOT"] . "/404.html");
    exit();
}

// require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

if ($page == 'registration' || $page == 'reset-password') {
    if ($_loginInfo != false) {
        header("Location: /login");
        die();
    }
} elseif ($_loginInfo or $page == 'login') {
    if ($_loginInfo) {
        $role = $_loginInfo['role'];
    }
} else {
    header("Location: /");
    die();
}
function renderAdminMenu($page)
{
?>
    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme side-menu">
        <ul class="menu-inner py-1">
            <div class="logo-container pb-2">
                <img src="/assets/img/CDAC-CTF.png" alt="ctf-logo">
            </div>

            <!-- Admin Dashboard -->
            <li class="menu-item <?php if ($page == 'admin-dashboard') {
                                        echo 'active';
                                    } ?>">
                <a href="/admin-zone/admin-dashboard" class="menu-link">
                    <i class="menu-icon mdi tf-icons mdi-monitor-dashboard"></i>
                    <div>Admin Dashboard</div>
                </a>
            </li>

            <!-- Manage Notices  -->
            <!-- <li class="menu-item">
                <!-- <a href="/admin-zone/notification-manager" class="menu-link"> -->
            <!-- <i class="menu-icon mdi tf-icons mdi-bulletin-board"></i> -->
            <!-- <div>Bulletin Management</div> -->
            <!-- </a> -->
            <!-- </li> -->

            <!-- Users Management -->
            <li class="menu-item <?php if ($page == 'notification-manager' || $page == 'all-notice') {
                                        echo 'active open';
                                    } ?>">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon mdi tf-icons mdi-bulletin-board"></i>
                    <div>Bulletin Management</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item <?php if ($page == 'notification-manager') {
                                                echo 'active';
                                            } ?>">
                        <a href="/admin-zone/notification-manager" class="menu-link">
                            <div>Add-Edit Notification</div>
                        </a>
                    </li>
                    <li class="menu-item <?php if ($page == 'all-notice') {
                                                echo 'active';
                                            } ?>">
                        <a href="/admin-zone/all-notice" class="menu-link">
                            <div>Show All Notification</div>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- CDAC Center Management -->
            <li class="menu-item <?php if ($page == 'all-center') {
                                        echo 'active';
                                    } ?>">
                <a href="/admin-zone/all-center" class="menu-link">
                    <i class="menu-icon mdi tf-icons mdi-office-building-outline"></i>
                    <div>CDAC Center Management</div>
                </a>
            </li>

            <li class="menu-item <?php if ($page == 'all-positions') {
                                        echo 'active';
                                    } ?>">
                <a href="/admin-zone/all-positions" class="menu-link">
                    <i class="menu-icon mdi tf-icons mdi-account-group-outline"></i>
                    <div>Designation Management</div>
                </a>
            </li>

            <!-- Users Management -->
            <li class="menu-item <?php if ($page == 'all-users' || $page == 'user-activity') {
                                        echo 'active open';
                                    } ?>">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon mdi tf-icons mdi-account-outline"></i>
                    <div>Users Management</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item <?php if ($page == 'all-users') {
                                                echo 'active';
                                            } ?>">
                        <a href="/admin-zone/all-users" class="menu-link">
                            <div>Show All Users</div>
                        </a>
                    </li>
                    <li class="menu-item <?php if ($page == 'user-activity') {
                                                echo 'active';
                                            } else {
                                                echo 'disabled';
                                            } ?>">
                        <a href="javascript:void(0);" class="menu-link">
                            <div>Individual User</div>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Category Management -->
            <li class="menu-item <?php if ($page == 'all-category') {
                                        echo 'active';
                                    } ?>">
                <a href="/admin-zone/all-category" class="menu-link">
                    <i class="menu-icon mdi tf-icons mdi-shape-plus-outline"></i>
                    <div>Category Management</div>
                </a>
            </li>

            <!-- Question Management -->
            <li class="menu-item <?php if ($page == 'all-questions') {
                                        echo 'active';
                                    } ?>">
                <a href="/admin-zone/all-questions" class="menu-link">
                    <i class="menu-icon mdi tf-icons mdi-file-question-outline"></i>
                    <div>Questions Management</div>
                </a>
            </li>

            <!-- View Feedbacks  -->
            <li class="menu-item <?php if ($page == 'all-feedback') {
                                        echo 'active';
                                    } ?>">
                <a href="/admin-zone/all-feedback" class="menu-link">
                    <i class="menu-icon mdi tf-icons mdi-comment-quote-outline"></i>
                    <div>View Feedbacks</div>
                </a>
            </li>

            <!-- Manage Certificates  -->
            <li class="menu-item <?php if ($page == 'manage-certificates') {
                                        echo 'active';
                                    } ?>">
                <a href="/admin-zone/manage-certificates" class="menu-link">
                    <i class="menu-icon mdi tf-icons mdi-certificate-outline"></i>
                    <div>Manage Certificates</div>
                </a>
            </li>

        </ul>
    </aside>
<?php
}

function renderUserMenu($loginInfo, $userScore, $userSolve, $challengesCount, $userRank, $usersCount, $compState)
{
?>
    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme side-menu">
        <ul class="menu-inner py-1">
            <h2 class="ctf-org-name">CDAC CTF Challenge</h2>
            <div class="logo-container">
                <img src="/assets/img/CDAC-CTF.png" alt="ctf-logo">
            </div>
            <span class="ctf-username" style="font-size: 16px;"><?php echo ucwords($loginInfo['username']) ?><br>(<?php echo $loginInfo['email'] ?>)</span>
            <div class="score">
                <h1 class="score" id="user_score"><?php echo $userScore ?></h1>
            </div>
            <div class="status">
                <div class="col">
                    <h3 id="user_solve"><?php echo $userSolve ?> / <?php echo $challengesCount ?></h3>
                    <p>Solved</p>
                </div>
                <div class="col">
                    <h3 id="user_count"><?php echo $userRank ?> / <?php echo $usersCount ?></h3>
                    <p>Rank</p>
                </div>
            </div>
            <?php if ($compState == 'going') : ?>
                <p class="ctf-username" style="margin-top: 15%; font-size: 14px;">Time Remaining<br><span id="days">!</span><span id="hours">!</span><span id="mins">!</span><span id="secs">!</span></p>
            <?php endif; ?>
        </ul>
    </aside>
<?php
}

// Usage:
if ($role == 'Admin') {
    renderAdminMenu($page);
}

if ($role == 'User') {
    renderUserMenu($_loginInfo, $user_score, $user_solve, $challenges_count, $user_rank, $users_count, $comp_state);
}
?>