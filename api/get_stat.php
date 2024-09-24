<?php

// require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php');

// Check if user is logged in
// if (!$_loginInfo) {
// header("Location: /");
// exit();
// }

require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

function generateAdaptiveColors($count)
{
    $colors = array();

    // Generate unique adaptive colors
    for ($i = 0; $i < $count; $i++) {
        $hue = fmod((360 / $count) * $i, 360); // Vary hue for uniqueness
        $saturation = 70; // Saturation
        $lightness = 60; // Default lightness

        // Adjust lightness based on average luminance
        if (isset($averageLuminance) && $averageLuminance < 0.5) {
            $lightness = 40; // Dark theme
        }

        $color = hslToHex($hue, $saturation, $lightness);
        array_push($colors, $color);
    }

    // Calculate average luminance
    $averageLuminance = calculateAverageLuminance($colors);

    return $colors;
}

// Convert HSL to HEX
function hslToHex($h, $s, $l)
{
    $h /= 360;
    $s /= 100;
    $l /= 100;
    $r = $g = $b = 0;
    $c = (1 - abs(2 * $l - 1)) * $s;
    $x = $c * (1 - abs(fmod($h * 6, 2) - 1));
    $m = $l - $c / 2;
    if ($h < 1 / 6) {
        $r = $c;
        $g = $x;
    } elseif ($h < 2 / 6) {
        $r = $x;
        $g = $c;
    } elseif ($h < 3 / 6) {
        $g = $c;
        $b = $x;
    } elseif ($h < 4 / 6) {
        $g = $x;
        $b = $c;
    } elseif ($h < 5 / 6) {
        $r = $x;
        $b = $c;
    } else {
        $r = $c;
        $b = $x;
    }
    $r = ($r + $m) * 255;
    $g = ($g + $m) * 255;
    $b = ($b + $m) * 255;
    return sprintf("#%02x%02x%02x", $r, $g, $b);
}

// Calculate average luminance
function calculateAverageLuminance($colors)
{
    $totalLuminance = 0;

    foreach ($colors as $color) {
        list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
        $r /= 255;
        $g /= 255;
        $b /= 255;
        $luminance = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b; // ITU-R BT.709
        $totalLuminance += $luminance;
    }

    return $totalLuminance / count($colors);
}

function getRegisterActiveLoginUserStat($conn)
{
    try {
        $sql = "SELECT
                    (SELECT COUNT(*) FROM `users` WHERE role = 'user') as registered_user,
                    (SELECT COUNT(*) FROM `users` WHERE role = 'user' AND status = 'true') as active_user,
                    (SELECT COUNT(DISTINCT logs__auth.`users_id`) 
                        FROM `logs__auth`
                        JOIN users ON logs__auth.`users_id` = users.id
                        WHERE logs__auth.`remark` = 'Login Success' AND users.role = 'user') AS logged_in_user";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $result->free_result();
            $stmt->close();

            return [
                'status' => true,
                'labels' => ['Register User', 'Active User', 'Login Users'],
                'values' => [$row['registered_user'], $row['active_user'], $row['logged_in_user']]
            ];
        } else {
            $result->free_result();
            $stmt->close();
            return [
                'status' => false
            ];
        }
    } catch (Exception $e) {
        // Handle any errors
        handle_error($e);
    }
}

function centerWiseStats($conn)
{
    try {
        $sql = "SELECT `center_id`, `center`, COUNT(users.id) as loc_user FROM `list__center` LEFT JOIN users ON list__center.center_id=users.location GROUP BY `center_id`;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) > 0) {
            $centers = [];
            $userCounts = [];

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $centers[] = $row['center'];
                $userCounts[] = $row['loc_user'];
            }

            $stmt->close();

            return [
                'status' => true,
                'labels' => $centers,
                'values' => $userCounts,
                'hexs' => generateAdaptiveColors(sizeof($centers))
            ];
        } else {
            $stmt->close();
            return [
                'status' => false,
            ];
        }
    } catch (Exception $e) {
        // Handle any errors
        handle_error($e);
    }
}

function challengeStats($conn)
{
    try {
        $sql = "SELECT c.id, c.title, COUNT(DISTINCT CASE WHEN uq.profession = 'student' THEN lq.u_id END) AS viewed_student, COUNT(DISTINCT CASE WHEN uq.profession = 'employee' THEN lq.u_id END) AS viewed_employee, COUNT(DISTINCT CASE WHEN uf.profession = 'student' THEN lf.u_id END) AS solved_student, COUNT(DISTINCT CASE WHEN uf.profession = 'employee' THEN lf.u_id END) AS solved_employee FROM challenges c LEFT JOIN logs__qs lq ON lq.c_id = c.id LEFT JOIN users uq ON lq.u_id = uq.id LEFT JOIN logs__flag lf ON c.id = lf.c_id AND lf.flag_status = 1 LEFT JOIN users uf ON lf.u_id = uf.id GROUP BY c.id, c.title;";
        // $sql = "SELECT c.id, c.title, COUNT(DISTINCT lq.u_id) AS viewed, COUNT(DISTINCT lf.u_id) AS solved FROM challenges c LEFT JOIN logs__qs lq ON c.id = lq.c_id LEFT JOIN logs__flag lf ON c.id = lf.c_id AND lf.flag_status = 1 GROUP BY c.id, c.title;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) > 0) {
            $ch_title = [];
            $ch_view = [];
            $ch_solve = [];

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $ch_title[] = $row['title'];
                $ch_Sview[] = $row['viewed_student'];
                $ch_Ssolve[] = $row['solved_student'];
                $ch_Eview[] = $row['viewed_employee'];
                $ch_Esolve[] = $row['solved_employee'];
                // viewed_student
                // viewed_employee
                // solved_student
                // solved_employee
            }

            $stmt->close();

            return [
                'status' => true,
                'labels' => $ch_title,
                // 'values' => array('view' => $ch_view, 'solve' => $ch_solve)
                'values' => array('Student_view' => $ch_Sview, 'Employee_view' => $ch_Eview, 'Student_solve' => $ch_Ssolve, 'Employee_solve' => $ch_Esolve)
            ];
        } else {
            $stmt->close();
            return [
                'status' => false,
            ];
        }
    } catch (Exception $e) {
        // Handle any errors
        handle_error($e);
    }
}

function challengeAvgLowestStats($conn)
{
    try {
        // $sql = "SELECT combined.challenge_title, ROUND(combined.avg_time_taken_seconds) as avg_time_taken_seconds, sub.viewed, sub.solved, sub.lowest_time_taken_seconds FROM (SELECT c.title AS challenge_title, AVG(TIMESTAMPDIFF(SECOND, lqs.time, lflag.time)) AS avg_time_taken_seconds FROM challenges c INNER JOIN logs__qs lqs ON c.id = lqs.c_id INNER JOIN logs__flag lflag ON lqs.u_id = lflag.u_id AND lqs.c_id = lflag.c_id GROUP BY c.id, c.title) AS combined JOIN (SELECT challenges.title, logs__qs.u_id, logs__qs.time AS viewed, logs__flag.time AS solved, TIMESTAMPDIFF(SECOND, logs__qs.time, logs__flag.time) AS lowest_time_taken_seconds, ROW_NUMBER() OVER (PARTITION BY challenges.title ORDER BY TIMESTAMPDIFF(SECOND, logs__qs.time, logs__flag.time)) AS row_num FROM challenges JOIN logs__qs ON challenges.id = logs__qs.c_id JOIN logs__flag ON logs__qs.u_id = logs__flag.u_id AND logs__qs.c_id = logs__flag.c_id WHERE logs__flag.flag_status = 1) AS sub ON combined.challenge_title = sub.title WHERE sub.row_num = 1;";
        $sql = "SELECT combined.challenge_title, ROUND(combined.avg_time_taken_seconds) AS avg_time_taken_seconds, sub.viewed, sub.solved, sub.lowest_time_taken_seconds, sub.name FROM (SELECT c.title AS challenge_title, AVG(TIMESTAMPDIFF(SECOND, lqs.time, lflag.time)) AS avg_time_taken_seconds FROM challenges c INNER JOIN logs__qs lqs ON c.id = lqs.c_id INNER JOIN logs__flag lflag ON lqs.u_id = lflag.u_id AND lqs.c_id = lflag.c_id GROUP BY c.id, c.title) AS combined JOIN (SELECT challenges.title, logs__qs.u_id, logs__qs.time AS viewed, logs__flag.time AS solved, TIMESTAMPDIFF(SECOND, logs__qs.time, logs__flag.time) AS lowest_time_taken_seconds, ROW_NUMBER() OVER (PARTITION BY challenges.title ORDER BY TIMESTAMPDIFF(SECOND, logs__qs.time, logs__flag.time)) AS row_num, users.name FROM challenges JOIN logs__qs ON challenges.id = logs__qs.c_id JOIN logs__flag ON logs__qs.u_id = logs__flag.u_id AND logs__qs.c_id = logs__flag.c_id JOIN users ON logs__qs.u_id = users.id WHERE logs__flag.flag_status = 1) AS sub ON combined.challenge_title = sub.title WHERE sub.row_num = 1;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) > 0) {
            $challenges = [];
            $avgTimes = [];
            $lowestTimes = [];
            $lowestUserName = [];

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $challenges[] = $row['challenge_title'];
                $avgTimes[] = round((int)$row['avg_time_taken_seconds']/60);
                $lowestTimes[] = (int)$row['lowest_time_taken_seconds'];
                $lowestUserName[] = $row['name'];
            }

            $stmt->close();

            return [
                'status' => true,
                'labels' => $challenges,
                'values' => array('avgTimes' => $avgTimes, 'lowestTimes' => $lowestTimes, 'lowestUserName' => $lowestUserName)
            ];
        } else {
            $stmt->close();
            return [
                'status' => false,
            ];
        }
    } catch (Exception $e) {
        // Handle any errors
        handle_error($e);
    }
}



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $logUserStat = getRegisterActiveLoginUserStat($conn);
    $centerStat = centerWiseStats($conn);
    $challengeStats = challengeStats($conn);
    $challengeAvgLowestStats = challengeAvgLowestStats($conn);

    if ($logUserStat['status'] && $centerStat['status'] && $challengeStats['status'] && $challengeAvgLowestStats['status']) {
        $responseData = array('logUserStat' => array_diff_key($logUserStat, array('status' => 0)), 'centerStat' => array_diff_key($centerStat, array('status' => 0)), 'challengeStats' => array_diff_key($challengeStats, array('status' => 0)), 'challengeAvgLowestStats' => array_diff_key($challengeAvgLowestStats, array('status' => 0)));
        header("Content-Type: application/json");
        echo json_encode($responseData);
    }elseif ($logUserStat['status'] && $centerStat['status']) {
        $responseData = array('logUserStat' => array_diff_key($logUserStat, array('status' => 0)), 'centerStat' => array_diff_key($centerStat, array('status' => 0)));
        $responseData['showMinimalData'] = true;
        header("Content-Type: application/json");
        echo json_encode($responseData);
    }
}
