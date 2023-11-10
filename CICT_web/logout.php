<?php
session_start();
$logs = simplexml_load_file('xml/activity_logs.xml');
$username = $_SESSION["username"];

$current_datetime = date('Y-m-d H:i:s');
$current_date = date('Y-m-d', strtotime($current_datetime));
$current_time = date('H:i:s', strtotime($current_datetime));

$highest_id = 0;
foreach ($logs->xpath('//*[@id]') as $element) {
    $id = (int) $element['id'];
    if ($id > $highest_id) {
        $highest_id = $id;
    }
}

// Increment the highest ID by 1
$new_id = $highest_id + 1;

// Use the new ID for a new element
$activityLog = $logs->addChild("activityLog");
$activityLog->addAttribute("id", $new_id);
$activityLog->addChild("username", $username);
$activityLog->addChild("action", "Logout");
$activityLog->addChild("date", $current_date);
$activityLog->addChild("time", $current_time);

$logs->asXML("xml/activity_logs.xml");


// Remove all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit();
?>