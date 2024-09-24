<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/template/head.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/common/functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/common/variables.php";

$remark = ["status" => true];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['captcha']) && isset($_POST['challenge'])) {
    $captcha = $_POST["captcha"];
    if (empty($captcha)) {
        $remark = ["status" => false, "type" => "delete", "message" => "Captcha verification failed,"];
    } elseif (!isset($_SESSION['captcha'])) {
        $remark = ["status" => false, "type" => "delete", "message" => "Captcha verification failed,"];
    } elseif ($captcha != $_SESSION['captcha']) {
        $remark = ["status" => false, "type" => "delete", "message" => "Captcha verification failed,"];
    }

    if ($remark['status']) {
        try {
            $sql = "DELETE FROM `challenges` WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_POST['challenge']);
            if ($stmt->execute()) {
                $remark = ["status" => true, "type" => "delete", "message" => "Challenge Deleted Successfully"];
            }
            $stmt->close();
        } catch (Exception $e) {
            handle_error($e);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addNewChallenge'])) {
    $question = $_POST['challengeTitle'];
    $description = $_POST['description'];
    $category = $_POST['challengeCategory'];
    $flag = $_POST['challengeFlag'];
    $score = $_POST['challengeScore'];

    if (strlen(htmlspecialchars(strip_tags($description))) <= 1) {
        $remark = ["status" => false, "message" => "Please enter the challenge description,"];
    } elseif (strlen($question) <= 1) {
        $remark = ["status" => false, "message" => "Please enter a challenge name,"];
    } elseif (strlen($category) < 1) {
        $remark = ["status" => false, "message" => "Please select a category,"];
    } elseif ($score == "") {
        $remark = ["status" => false, "message" => "Please enter a score for solving the challenge,"];
    } elseif (strlen($flag) <= 1) {
        $remark = ["status" => false, "message" => "Please enter the correct flag,"];
    }

    if ($remark['status']) {
        try {
            $sql = "INSERT INTO `challenges`(`title`, `description`, `flag`, `score`, `cat_id`) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssii", $question, $description, $flag, $score, $category);
            if ($stmt->execute()) {
                $remark = ["status" => true, "type" => "added", "message" => "New Challenge Added Successfully"];
            }
            $stmt->close();
        } catch (Exception $e) {
            handle_error($e);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['questionID']) && isset($_POST['updateChallenge'])) {

    $newArray = array();

    foreach ($_POST as $key => $value) {
        $newKey = preg_replace('/[0-9]+/', '', $key);
        $newArray[$newKey] = $value;
    }

    $questionID = $_POST['questionID'];
    $question = $newArray['questions'];
    $description = $newArray['description'];
    $category = $newArray['category'];
    $flag = $newArray['flag'];
    $score = $newArray['score'];

    if (strlen(htmlspecialchars(strip_tags($description))) <= 1) {
        $remark = ["status" => false, "message" => "Please enter the challenge description,"];
    } elseif (strlen($question) <= 1) {
        $remark = ["status" => false, "message" => "Please enter a challenge name,"];
    } elseif (strlen($category) < 1) {
        $remark = ["status" => false, "message" => "Please select a category,"];
    } elseif ($score == "") {
        $remark = ["status" => false, "message" => "Please enter a score for solving the challenge,"];
    } elseif (strlen($flag) <= 1) {
        $remark = ["status" => false, "message" => "Please enter the correct flag,"];
    }

    if ($remark['status']) {
        try {
            $sql = "UPDATE challenges SET title=?, score=?,description=?, flag=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sissi", $question, $score, $description, $flag, $questionID);
            if ($stmt->execute()) {
                $remark = ["status" => true, "message" => "Successfully updated"];
            }
            $stmt->close();
        } catch (Exception $e) {
            handle_error($e);
        }
    }
}
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

                        <?php
                        try {
                            $sql = "SELECT ch.id, ch.title, ch.description, ch.flag, ch.score, cat.cat_id AS cat_id, cat.name AS cat_name FROM category AS cat LEFT JOIN challenges AS ch ON ch.cat_id = cat.cat_id ORDER BY cat.cat_id, ch.id;";
                            $result = mysqli_query($conn, $sql);
                            if (!$result) {
                                throw new Exception(mysqli_error($conn));
                            }
                            $challenges = mysqli_fetch_all($result, MYSQLI_ASSOC);
                            $count = mysqli_num_rows($result);
                            $categories = getCategoryList($conn);
                            if ($count > 0) {
                                $prev_cat_name = null;
                                foreach ($challenges as $challenge) {
                                    $cat_name = $challenge['cat_name'];
                                    $cat_id = $challenge['cat_id'];
                                    $addNewQs = '<div class="col-md-6 col-xl-4 d-flex justify-content-center align-items-center cardContainer cursor-pointer">
                                            <div class="card border border-secondary mb-3 mx-auto" style="height: 150px;">
                                                <div class="card-body text-center">
                                                    <h1 style="font-size: 3rem;">+</h1>
                                                    <h5 class="card-title">Add A New Challenge</h5>
                                                </div>
                                            </div>
                                        </div>';
                                    if ($prev_cat_name !== $cat_name) {
                                        if ($prev_cat_name !== null) {
                                            echo $addNewQs;
                                            echo "</div>"; // Close previous category container
                                        }
                                        $prev_cat_name = $cat_name;
                                        echo '<h5 class="pb-1 mb-4 underline">' . $cat_name . '</h5>';
                                        echo '<div class="row mb-5" data-categoryId="' . $cat_id . '">';
                                    }
                                    if ($challenge['id']) {
                        ?>
                                        <div class="col-md-6">
                                            <div class="card text-center mb-3" data-id="<?php echo $challenge['id']; ?>">
                                                <div class="card-header">
                                                    <ul class="nav nav-tabs" role="tablist">
                                                        <li class="nav-item">
                                                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tab-view<?php echo $challenge['id']; ?>" aria-controls="navs-tab-view<?php echo $challenge['id']; ?>" aria-selected="true">Show Question</button>
                                                        </li>
                                                        <li class="nav-item">
                                                            <button type="button" class="nav-link <?php if ($comp_state == 'going') echo 'disabled'; ?>" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tab-edit<?php echo $challenge['id']; ?>" aria-controls="navs-tab-edit<?php echo $challenge['id']; ?>" aria-selected="false">Edit Question</button>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="card-body">
                                                    <div class="tab-content p-0">
                                                        <div class="tab-pane fade show active text-start" id="navs-tab-view<?php echo $challenge['id']; ?>" role="tabpanel">
                                                            <h4 class="card-title text-start mb-2 pb-2"><?php echo ($challenge['title'] . " <sup class='fs-6 fw-light text-success'>(+" . $challenge['score'] . ")</sup>"); ?></h4>
                                                            <?php echo ($challenge['description']); ?>
                                                            <p class="text-start h6 fst-italic mb-0 pb-0"><b>Correct Flag:</b> <?php echo ($challenge['flag']); ?></p>
                                                        </div>
                                                        <div class="tab-pane fade" id="navs-tab-edit<?php echo $challenge['id']; ?>" role="tabpanel">
                                                            <form id="updateChallenge<?php echo $challenge['id']; ?>" name="updateChallenge<?php echo $challenge['id']; ?>" method="POST" action="<?php echo pathinfo($_SERVER["PHP_SELF"], PATHINFO_FILENAME); ?>" onsubmit="return false">
                                                                <?php
                                                                $currentCategory = $challenge['cat_id'];
                                                                echo '<div class="form-floating form-floating-outline  mb-4">
                                                                <select id="category' . $challenge['id'] . '" name="category' . $challenge['id'] . '" class="select2 form-select">
                                                                    <option value="">Select Challenge Category</option>';
                                                                foreach ($categories as $category) {
                                                                    echo '<option value="' . $category['value'] . '"' . ($category['value'] == $currentCategory ? ' selected' : '') . '>' . $category['name'] . '</option>';
                                                                }
                                                                echo '</select>
                                                                    <label for="category' . $challenge['id'] . '">Challenge Category</label>
                                                                </div>';
                                                                ?>
                                                                <div class="form-floating form-floating-outline mb-4">
                                                                    <input id="questions<?php echo $challenge['id']; ?>" name="questions<?php echo $challenge['id']; ?>" value="<?php echo htmlspecialchars_decode($challenge['title']); ?>" class="form-control" type="text" placeholder="Please enter challenge name" aria-describedby="questions<?php echo $challenge['id']; ?>" autocomplete="off" required />
                                                                    <label for="questions<?php echo $challenge['id']; ?>">Challenge Name</label>
                                                                </div>
                                                                <div class="input-group input-group-merge mb-4">
                                                                    <span class="input-group-text">Obtain Score</span>
                                                                    <div class="form-floating form-floating-outline">
                                                                        <input type="number" class="form-control" id="score<?php echo $challenge['id']; ?>" name="score<?php echo $challenge['id']; ?>" value="<?php echo (int)$challenge['score']; ?>" placeholder="Please enter Score For Solve This Challenge " aria-label="Score For Solve This Challenge" aria-describedby="score<?php echo $challenge['id']; ?>">
                                                                        <label for="score<?php echo $challenge['id']; ?>">Score For Solve This Challenge</label>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-4 editor">
                                                                    <div id="snow-toolbar<?php echo $challenge['id']; ?>">
                                                                        <span class="ql-formats">
                                                                            <select class="ql-font" name="ql-font"></select>
                                                                            <select class="ql-size" name="ql-size"></select>
                                                                        </span>
                                                                        <span class="ql-formats">
                                                                            <button class="ql-bold"></button>
                                                                            <button class="ql-italic"></button>
                                                                            <button class="ql-underline"></button>
                                                                            <button class="ql-strike"></button>
                                                                        </span>
                                                                        <span class="ql-formats">
                                                                            <select class="ql-color" name="ql-color"></select>
                                                                            <select class="ql-background" name="ql-background"></select>
                                                                        </span>
                                                                        <span class="ql-formats">
                                                                            <button class="ql-script" value="sub"></button>
                                                                            <button class="ql-script" value="super"></button>
                                                                        </span>
                                                                        <span class="ql-formats">
                                                                            <button class="ql-link"></button>
                                                                        </span>
                                                                    </div>
                                                                    <div id="snow-editor<?php echo $challenge['id']; ?>" class="text-start">
                                                                        <?php echo htmlspecialchars_decode($challenge['description']); ?>
                                                                    </div>
                                                                </div>
                                                                <div class="form-floating form-floating-outline mb-4">
                                                                    <input id="flag<?php echo $challenge['id']; ?>" value="<?php echo htmlspecialchars_decode($challenge['flag']); ?>" name="flag<?php echo $challenge['id']; ?>" class="form-control" type="text" placeholder="Please enter correct flag" aria-describedby="flag<?php echo $challenge['id']; ?>" autocomplete="off" required />
                                                                    <label for="flag<?php echo $challenge['id']; ?>">Correct Flag</label>
                                                                </div>
                                                                <div class="col-12 text-start qs-update">
                                                                    <button type="submit" id="update-questions<?php echo $challenge['id']; ?>" class="btn btn-label-primary waves-effect">Update Challenge</button>
                                                                    <button type="button" id="delete-questions<?php echo $challenge['id']; ?>" class="btn btn-label-danger waves-effect" onclick="deleteChallenge(<?php echo $challenge['id']; ?>)">Delete Challenge</button>
                                                                </div>
                                                                <input type="hidden" name="questionID" value="<?php echo $challenge['id']; ?>">
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                        <?php
                                    }
                                }
                                echo $addNewQs;
                                echo "</div>"; // Close last category container
                            } else {
                                echo '
                                <div class="container d-flex justify-content-center align-items-center" style="height: 70vh;">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <div class="mt-3">
                                                <h4 class="card-title">Please add a category</h4>
                                                <p class="card-text pt-3 pb-1">There are currently no categories available.</p>
                                                <p class="card-text pt-0">Please add a category from the <a href="all-category">Category Management</a> section.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
                            }
                        } catch (Exception $e) {
                            handle_error($e);
                        }
                        require_once($_SERVER['DOCUMENT_ROOT'] . '/template/toast.php');
                        require_once($_SERVER['DOCUMENT_ROOT'] . '/template/modal.php');
                        captchaModal();
                        ?>
                        <div class="modal fade" id="modal-add-challenge" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5">Add New Challenge</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="challengeCancel()"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="addNewChallenge" name="addNewChallenge" method="POST" action="<?php echo pathinfo($_SERVER["PHP_SELF"], PATHINFO_FILENAME); ?>" onsubmit="return false">
                                            <?php
                                            echo '<div class="form-floating form-floating-outline  mb-4">
                                            <select id="challengeCategory" name="challengeCategory" class="select2 form-select">
                                                <option value="">Select Challenge Category</option>';
                                            foreach ($categories as $category) {
                                                echo '<option value="' . $category['value'] . '"' . '>' . $category['name'] . '</option>';
                                            }
                                            echo '</select>
                                                <label for="challengeCategory">Challenge Category</label>
                                            </div>';
                                            ?>
                                            <div class="form-floating form-floating-outline mb-4">
                                                <input id="challengeTitle" name="challengeTitle" class="form-control" type="text" placeholder="Please enter challenge name" aria-describedby="challengeTitle" autocomplete="off" required />
                                                <label for="challengeTitle">Challenge Name</label>
                                            </div>
                                            <div class="input-group input-group-merge mb-4">
                                                <span class="input-group-text">Obtain Score</span>
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" class="form-control" id="challengeScore" name="challengeScore" placeholder="Please enter Score For Solve This Challenge " aria-label="Score For Solve This Challenge" aria-describedby="challengeScore">
                                                    <label for="challengeScore">Score For Solve This Challenge</label>
                                                </div>
                                            </div>
                                            <div class="mb-4">
                                                <div id="new-challenge-snow-toolbar" class="text-center">
                                                    <span class="ql-formats">
                                                        <select class="ql-font" name="ql-font"></select>
                                                        <select class="ql-size" name="ql-size"></select>
                                                    </span>
                                                    <span class="ql-formats">
                                                        <button class="ql-bold"></button>
                                                        <button class="ql-italic"></button>
                                                        <button class="ql-underline"></button>
                                                        <button class="ql-strike"></button>
                                                    </span>
                                                    <br>
                                                    <span class="ql-formats">
                                                        <select class="ql-color" name="ql-color"></select>
                                                        <select class="ql-background" name="ql-background"></select>
                                                    </span>
                                                    <span class="ql-formats">
                                                        <button class="ql-script" value="sub"></button>
                                                        <button class="ql-script" value="super"></button>
                                                    </span>
                                                    <span class="ql-formats">
                                                        <button class="ql-link"></button>
                                                    </span>
                                                </div>
                                                <div id="new-challenge-snow-editor" class="text-start">
                                                </div>
                                            </div>
                                            <div class="form-floating form-floating-outline mb-4">
                                                <input id="challengeFlag" value="" name="challengeFlag" class="form-control" type="text" placeholder="Please enter correct flag" aria-describedby="challengeFlag" autocomplete="off" required />
                                                <label for="challengeFlag">Correct Flag</label>
                                            </div>
                                            <!-- <div class="modal-footer"> -->
                                            <div class="col-12 text-start qs-update">
                                                <button type="submit" id="addChallenge" class="btn btn-label-primary waves-effect">Add New Challenge</button>
                                                <button type="button" name="close-modal" class="btn btn-label-danger waves-effect" onclick="challengeCancel()">Cancel</button>
                                            </div>
                                            <!-- </div> -->
                                        </form>
                                    </div>
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
    <!-- / scripts -->

    <?php
    if ($remark['status'] && isset($remark['type']) && isset($remark['message'])) {
        echo "<script type='text/javascript'>showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Action Successful','" . $remark['message'] . "');</script>";
    } elseif (isset($remark['message']) && isset($remark['type'])) {
        echo "<script type='text/javascript'>showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Error!!','" . $remark['message'] . " please try again');</script>";
    } elseif ($remark['status'] && isset($remark['message'])) {
        echo "<script type='text/javascript'>showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Challenge Updated Successfully','Challenge Details Updated Successfully');</script>";
    } elseif (isset($remark['message'])) {
        echo "<script type='text/javascript'>showToast(5000, 'mdi-alert-circle', 'animate__shakeX', 'text-danger', 'Incomplete Challenge!!','" . $remark['message'] . " and try again');</script>";
    }
    ?>
</body>

</html>