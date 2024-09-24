<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(404);
    include($_SERVER["DOCUMENT_ROOT"] . "/404.html");
    exit();
}


if ($timeLineData['status']) : ?>
    <!-- Activity Timeline -->
    <div class="card card-action mb-4">
        <div class="card-header align-items-center">
            <h5 class="card-action-title mb-0 ps-3"><i class='mdi mdi-chart-timeline-variant mdi-24px me-2'></i>Activity Timeline & Log's</h5>
        </div>

        <div class="card-body pt-3 pb-0 ps-5">
            <ul class="timeline card-timeline mb-0">
                <?php

                function updateDetail($activity, $field, $label)
                {
                    if (str_replace('"', '', explode(" ", $activity['detail'])[1]) == $field) {
                        preg_match_all('/"([^"]+)"/', $activity['detail'], $matches);
                        $last_two_values = array_slice($matches[1], -2);
                        $activity['detail'] = ucfirst($label) . ' updated from "' . $last_two_values[0] . '" to "' . $last_two_values[1] . '"';
                    }
                    return $activity;
                }

                // Initialize an empty array to store timestamps for challenge access
                $challengeAccessTimes = [];

                foreach ($timeLineData['data'] as $index => $activity) :
                    // For "Challenge Access" activities, store the timestamp
                    if ($activity['activity_type'] === 'Challenge Access') {
                        $challengeAccessTimes[$activity['detail']] = strtotime($activity['timestamp']);
                    }

                    $timelinePointClass = '';
                    $scoreText = '';
                    $emoji = '';

                    switch ($activity['activity_type']) {

                        case 'Login Attempted':
                            if ($activity['detail'] === 'New User Added') {
                                $activity['activity_type'] = 'Welcome! New user created an account';
                                $timelinePointClass = 'bg-primary';
                            } else if ($activity['detail'] === 'Password reset by user') {
                                $activity['activity_type'] = 'Update Password';
                                $timelinePointClass = 'bg-warning';
                            } else if ($activity['detail'] === 'Password changed by user') {
                                $activity['activity_type'] = 'Update Password';
                                $timelinePointClass = 'bg-warning';
                            }
                            break;
                        case 'Login Success':
                            $timelinePointClass = 'bg-success';
                            break;
                        case 'Challenge Access':
                            $timelinePointClass = 'bg-info';
                            break;
                        case 'Correct flag submitted':
                            $timelinePointClass = 'bg-success';
                            $pointsGained = $activity['points_gained'];
                            $totalScore = $activity['total_score'];
                            $challenge = $activity['detail'];

                            // Calculate time taken to solve the challenge
                            if (isset($challengeAccessTimes['Visited ' . $challenge])) {
                                $challengeAccessTime = $challengeAccessTimes['Visited ' . $challenge];
                                $timeTaken = strtotime($activity['timestamp']) - $challengeAccessTime;

                                // Initialize the timeTakenText variable
                                $timeTakenText = "";

                                // Calculate hours
                                $hours = floor($timeTaken / 3600);
                                if ($hours > 0) {
                                    $timeTakenText .= $hours . " hour" . ($hours > 1 ? "s" : "") . " ";
                                }

                                // Calculate remaining minutes
                                $minutes = floor(($timeTaken % 3600) / 60);
                                if ($minutes > 0 || $hours > 0) {
                                    $timeTakenText .= $minutes . " minute" . ($minutes > 1 ? "s" : "") . " ";
                                }

                                // Calculate remaining seconds
                                $seconds = $timeTaken % 60;
                                $timeTakenText .= $seconds . " second" . ($seconds != 1 ? "s" : "");
                            } else {
                                $timeTakenText = 'Unknown';
                            }

                            $scoreText = "Points Gained: $pointsGained, Total Score: $totalScore, Time Taken: $timeTakenText";
                            $emoji = '🎉'; // Party pop emoji
                            $activity['activity_type'] = 'Flag Correctly Submitted';
                            break;
                        case 'Wrong flag submitted':
                            $timelinePointClass = 'bg-danger';
                            break;
                            // case 'Password changed by user':
                            // $timelinePointClass = 'bg-warning';
                            // break;
                            // case 'Password reset by user':
                            // $timelinePointClass = 'bg-warning';
                            // break;
                        case 'Field Updated':
                            $activity['activity_type'] = 'User Detail Updated';

                            $activity = updateDetail($activity, 'name', 'Username');
                            $activity = updateDetail($activity, 'phoneNumber', 'Phone number');
                            $activity = updateDetail($activity, 'location', 'CDAC Center');
                            $activity = updateDetail($activity, 'designation', 'Designation');

                            $timelinePointClass = 'bg-info';
                            break;
                        case 'Invalid password change attempt by user':
                            $timelinePointClass = 'bg-danger';
                            break;
                        case 'Invalid Login Attempt':
                            $timelinePointClass = 'bg-danger';
                            break;
                        default:
                            $timelinePointClass = 'bg-info';
                            break;
                    }
                ?>
                    <li class="timeline-item timeline-item-transparent">
                        <span class="timeline-point <?php echo $timelinePointClass; ?>"></span>
                        <div class="timeline-event">
                            <div class="timeline-header mb-2 pb-1">
                                <h6 class="mb-0"><?php echo $activity['activity_type']; ?><?php echo $emoji; ?></h6>
                                <small class="text-muted"><?php echo date('F j, Y, g:i:s a', strtotime($activity['timestamp'])); ?></small>
                            </div>
                            <p class="mb-2"><?php echo $activity['detail']; ?></p>
                            <?php if ($scoreText !== '') : ?>
                                <p class="mb-2"><?php echo $scoreText; ?></p>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <!--/ Activity Timeline -->
<?php else : ?>
    <!-- No Data Message -->
    <div class="card card-action mb-4">
        <div class="card-header align-items-center">
            <h5 class="card-action-title mb-0"><i class='mdi mdi-alert-octagon mdi-24px me-2'></i>No Activity Data Available</h5>
        </div>
        <div class="card-body pt-3 pb-3">
            <p class="mb-0">There is no activity data available at the moment.</p>
        </div>
    </div>
    <!--/ No Data Message -->
<?php endif; ?>