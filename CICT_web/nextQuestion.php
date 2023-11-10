<?php
session_start();
$xml = simplexml_load_file("xml/quizzes.xml");
$num_items = count($xml->quiz->item);
$random_item_index = rand(0, 15 - 1);

$_SESSION["random_item_index"] = $random_item_index;

header("Location: takeQuiz.php");
exit();

if (!isset($_SESSION['uid'])) {
    // Redirect to login page
    header('Location: login.php');
    exit;
}

?>