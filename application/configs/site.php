<?php

if (!defined('INCLUDED_AMEMBER_CONFIG'))
    die("Direct access to this location is not allowed");

//add new block at position member/main/left
Am_Di::getInstance()->blocks->remove('member-main-subscriptions');
Am_Di::getInstance()->blocks->remove('member-main-links');

Am_Di::getInstance()->blocks->add('member/main/right', new Am_Widget_ActiveSubscriptions, 200);
Am_Di::getInstance()->blocks->add('member/main/left', new Am_Widget_MemberLinks, 200);
Am_Di::getInstance()->blocks->add(new Am_Block('member/main/right', 'Moodle Completion Status', 'moodle-block-id', null, function () {
    $user = Am_Di::getInstance()->auth->getUser();
    $scheme = Am_Di::getInstance()->request->getScheme(); // http
    $host = Am_Di::getInstance()->request->getHttpHost(); // localhost:8888
    $baseUrl = Am_Di::getInstance()->request->getBaseUrl(); // module400/dashboard
    $modulePath = explode('/', $baseUrl)[1];
    $activeProductIds = $user->getActiveProductIds();
    $moodleApiToken = '5b86722b67947fdf8cd77cb428931840'; // Replace with your token
    $moodleBaseUrl = $scheme . '://' . $host . '/' . $modulePath . '/webservice/rest/server.php';
    $moodleUserId = fetchMoodleUserIdByDetails($user->login, $user->name_f, $user->name_l, $user->email, $moodleBaseUrl, $moodleApiToken);

    if ($moodleUserId) {
        // Initialize the HTML output for the block
        $html = "<div class='moodle-completion-status'>";

        // Display user details
        // $html .= "<h4>Moodle User Details:</h4>";
        // $html .= "Username: " . htmlspecialchars($user->login) . "<br>";
        // $html .= "First Name: " . htmlspecialchars($user->name_f) . "<br>";
        // $html .= "Last Name: " . htmlspecialchars($user->name_l) . "<br>";
        // $html .= "Email: " . htmlspecialchars($user->email) . "<br>";
        // $html .= "Moodle User ID: " . htmlspecialchars($moodleUserId) . "<br><br>";

        // Loop through active products and fetch the completion data
        foreach ($activeProductIds as $productId) {
            // Look for a matching record in the resource access table
            $resourceAccess = Am_Di::getInstance()->resourceAccessTable->findFirstBy([
                'resource_type' => 'integration',
                'fn' => 'product_id',
                'id' => $productId,
            ]);

            if ($resourceAccess) {
                $resourceId = $resourceAccess->resource_id; // Resource ID to be used

                // Now, look up the IntegrationTable using the resourceId
                $integration = Am_Di::getInstance()->integrationTable->findFirstBy(['integration_id' => $resourceId]);

                // If integration is found, extract the gr value from vars
                if ($integration) {
                    $vars = unserialize($integration->vars); // Assuming vars is serialized

                    if (isset($vars['gr'])) {
                        $courseId = $vars['gr']; // Use the gr value as courseId

                        // Fetch completion data from Moodle API
                        $completionData = fetchMoodleCompletionStatus($courseId, $moodleUserId, $moodleBaseUrl, $moodleApiToken);

                        // Calculate progress
                        if (!empty($completionData['statuses'])) {
                            $totalActivities = count($completionData['statuses']);
                            $completedActivities = 0;

                            foreach ($completionData['statuses'] as $status) {
                                if ($status['state'] == 1) {
                                    $completedActivities++;
                                }
                            }

                            $progress = ($totalActivities > 0) ? ($completedActivities / $totalActivities) * 100 : 0;

                            // Display progress
                            $html .= "<h5>Completion Status for Course ID: " . htmlspecialchars($courseId) . "</h5>";
                            $html .= "Progress: " . number_format($progress, 2) . "%<br>";

                            foreach ($completionData['statuses'] as $status) {
                                // $html .= "Activity ID: " . htmlspecialchars($status['cmid']) . "<br>";
                                // $html .= "Module Name: " . htmlspecialchars($status['modname']) . "<br>";
                                // $html .= "Completion State: " . ($status['state'] == 0 ? 'Not Completed' : 'Completed') . "<br><hr>";
                            }
                        }
                        // else {
                        //     $html .= "<p>No completion status data available for Course ID: " . htmlspecialchars($courseId) . "</p>";
                        // }
                    }
                }
            }
        }

        $html .= "</div>";
    } else {
        $html = "<p>No Moodle user found for the provided details.</p>";
    }

    return $html;
}));

// Function to fetch Moodle user ID by username, first name, last name, and email
function fetchMoodleUserIdByDetails($username, $firstName, $lastName, $email, $moodleBaseUrl, $moodleApiToken)
{
    // Build the API request URL
    $url = $moodleBaseUrl . '?wstoken=' . urlencode($moodleApiToken) .
        '&moodlewsrestformat=json&wsfunction=core_user_get_users' .
        '&criteria[0][key]=username&criteria[0][value]=' . urlencode($username) .
        '&criteria[1][key]=firstname&criteria[1][value]=' . urlencode($firstName) .
        '&criteria[2][key]=lastname&criteria[2][value]=' . urlencode($lastName) .
        '&criteria[3][key]=email&criteria[3][value]=' . urlencode($email);

    // Initialize cURL session
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        die("cURL error: " . curl_error($ch));
    }

    // Close cURL session
    curl_close($ch);

    // Decode the response and return the Moodle user ID if found
    $data = json_decode($response, true);
    if (!empty($data['users'][0]['id'])) {
        return $data['users'][0]['id'];
    } else {
        return null; // Return null if no user is found
    }
}

// Function to fetch completion data from Moodle API
function fetchMoodleCompletionStatus($courseId, $userId, $moodleBaseUrl, $moodleApiToken)
{
    // Build the API request URL
    $url = $moodleBaseUrl . '?wstoken=' . urlencode($moodleApiToken) .
        '&moodlewsrestformat=json&wsfunction=core_completion_get_activities_completion_status' .
        '&courseid=' . urlencode($courseId) . '&userid=' . urlencode($userId);

    // Initialize cURL session
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        die("cURL error: " . curl_error($ch));
    }

    // Close cURL session
    curl_close($ch);

    // Decode the response and return it
    return json_decode($response, true);
}


