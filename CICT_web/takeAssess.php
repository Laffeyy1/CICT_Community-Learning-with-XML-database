<?php
session_start();
$xmlUsers = simplexml_load_file('xml/users.xml');
$username = $_SESSION["username"];

$random_item_index = $_SESSION["random_item_index"];
$user_id = $_SESSION["uid"];

$selectedAssess = intval($_SESSION["selectedAssess"]);

$user = $xmlUsers->xpath("//user[@uid='$user_id']")[0];

echo $_SESSION["taken"];
echo $_SESSION["correctAns"];

$xml = simplexml_load_file("xml/assessments.xml");

// Select a random item

$random_item = $xml->assessment[$selectedAssess]->item[$random_item_index];


if (!isset($_SESSION["taken"])) {
    $_SESSION["taken"] = 0;
}

if (!isset($_SESSION["correctAns"])) {
    $_SESSION["correctAns"] = 0;
}


if (isset($_POST["next"])) {
    $selected_answer = $_POST['choice'];
    $correct_answer = $random_item->answer;

    if ($selected_answer == $correct_answer) {
        $_SESSION["taken"]++;
        $_SESSION["correctAns"]++;
    } else {
        $_SESSION["taken"]++;
    }

    if ($_SESSION["taken"] == 10) {

        $no = $_SESSION["selectedAssess"];
        $assess = $user->xpath("assess[@aNo='$selectedAssess']")[0];
        if ($assess['aNo'] == $_SESSION["selectedAssess"]) {
            $assess[$selectedAssess] = $_SESSION["correctAns"];
        } else {

            $new_assess = $user->addChild('assess');
            $new_assess['aNo'] = $selectedAssess;
            $new_assess->addAttribute("score", $_SESSION["correctAns"]);
        }

        $logs = simplexml_load_file('xml/activity_logs.xml');

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
        $activityLog->addChild("action", "Assessment No. ".$selectedAssess." Taken" );
        $activityLog->addChild("date", $current_date);
        $activityLog->addChild("time", $current_time);

        $logs->asXML("xml/activity_logs.xml");
        // save the updated XML file
        $xmlUsers->asXML('xml/users.xml');

        header("Location: assessments.php");
        exit;
    }
    else{
        header("Location: nextQuestionA.php");
        exit;
    }

}

if (!isset($_SESSION['uid'])) {
    // Redirect to login page
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Home</title>
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-expand-md navbar-light bg-body border-bottom p-0 ps-5" id="navbarz">

    <!--Logo-->
    <div>
        <a class="navbar-brand" href="#">
            <img src="image/logo_black.png" height="80">
        </a>
    </div>

    <!--Nav-->
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav">
        </ul>

    </div>

    </nav>
    <div class="container-fluid">
    <div class="row min-vh-100" id="center_body">
    <div class="col">
        <div class="container mt-3">
            <h5>Assessment No. </h5>
        </div>
        <div class="container bg-white mt-4 py-2 rounded d-flex justify-content-center align-items-center h-75">
        <div class="row w-100">
    <div class="col py-2">
        <div class="col text-end">
            <form action="" method="post">     
                <div class="rounded overflow text-start">
                    <div class="row mb-5">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text" id="inputGroup-sizing-lg">Question: </span>
                            <label name="question" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg"><?php echo $random_item->question?></label>
                        </div>
                    </div>
                    <div class="row mb-5"><!-- Add text-center class here -->
                        <center>
                            <div class="form-check form-check-inline fs-lg">
                                <input class="form-check-input h4" type="radio" name="choice" id="inlineRadio1" value="<?php echo $random_item->choice1?>" />
                                <label class="form-check-label h4" for="inlineRadio1"><?php echo $random_item->choice1?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input h4" type="radio" name="choice" id="inlineRadio2" value="<?php echo $random_item->choice2?>" />
                                <label class="form-check-label h4" for="inlineRadio2"><?php echo $random_item->choice2?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input h4" type="radio" name="choice" id="inlineRadio3" value="<?php echo $random_item->choice3?>" />
                                <label class="form-check-label h4" for="inlineRadio3"><?php echo $random_item->choice3?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input h4" type="radio" name="choice" id="inlineRadio4" value="<?php echo $random_item->choice4?>" />
                                <label class="form-check-label h4" for="inlineRadio4"><?php echo $random_item->choice4?></label>
                            </div>
                        </center>
                    </div>
                </div>
                <input type="submit" class="btn btn-danger btn-lg" name="next" value="Next">
            </form>
        </div>
    </div>
</div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/45b063e61e.js" crossorigin="anonymous"></script>
</body>

</html>