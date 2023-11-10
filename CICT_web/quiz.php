<?php
session_start();
$user_id = $_SESSION["uid"];
$_SESSION["taken"] = 0;
$_SESSION["correctAns"] = 0;
unset($_SESSION['selected_numbers']);

$xml = simplexml_load_file("xml/quizzes.xml");
$_SESSION["selectedQuestion"] = null;

if (isset($_POST["quiz"])) {
    $_SESSION["selectedQuiz"] = $_POST["quiz"];
}


$selectedQuiz = isset($_SESSION["selectedQuiz"]) ? $_SESSION["selectedQuiz"] : null;
$disabled = (!isset($_SESSION["selectedQuestion"]) || empty($_SESSION["selectedQuestion"])) ? "disabled" : "";

$xmlUser = simplexml_load_file('xml/users.xml');

$i = 1;

$quiz_count = 1;
$_SESSION["quiz_count"] = $quiz_count;

foreach ($xmlUser->user as $user) {
    // check if the user ID matches the one you're looking for
    if ($user['uid'] == $user_id) {
        // loop through each quiz node for this user and count how many have a value
        foreach ($user->quiz as $quiz) {
            if (isset($quiz) && (string)$quiz !== '') {
                $quiz_count++;
            }
        }
        break;
    }
}

if (isset($_POST["take"])) {
    header("Location: nextQuestion.php");
}

if (!isset($_SESSION['uid'])) {
    // Redirect to login page
    header('Location: login.php');
    exit;
}

if (isset($_POST['yes'])){
    header('Location: logout.php');
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
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
                <li class="nav-item">
                    <a class="nav-link" href="home.php">Home <span class="sr-only"></span></a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link  border-bottom border-dark" href="quiz.php">Quizzes<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="assessments.php">Assessment<span class="sr-only"></span></a>
                </li>
            </ul>
        </div>

        <!--Notification-->
        <div class="dropdown">

        </div>

        <!--Profile-->
        <div class="dropdown">
            <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                <?php
                    $profilePic = isset($user->profilePicture) ? $user->profilePicture : '';

                    if (!empty($profilePic)) {
                        echo '<img src="' . $profilePic . '" class="img-fluid rounded-circle border border-dark border-2" style="width: 40px; height:40px;">';
                    } else {
                        echo '<i class="bi bi-person-circle" id="profile"></i>';
                    }
                ?>
            </a>
            <ul class="dropdown-menu" id="profile_drop">
                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#exampleModal">Logout</button></li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid">
    <div class="row min-vh-100" id="center_body">
        <div class="col-3">
            <nav class="sidebar bg-white pb-2 mt-5 rounded">
                <div class="rounded overflow-auto w-100 text-start" style="height:750px;">
                    <div class="list-group list-group-flush mx-3 mt-4">
                        <h3>Take Quiz</h3>
                        <?php foreach($xml->quiz as $quiz) : 
                            $quizNo = $quiz["quizNo"];
                            $class = ($selectedQuiz == $quizNo) ? "bg-danger text-white" : "";
                            if( $i <= $quiz_count){
                                $quizlid = $i;
                                $i = $i + 1;
                            }
                            $locked = ($quizNo == $quizlid) ? false : true; // set locked state based on lesson ID and quiz count
                        ?>
                            <a href="#" class="list-group-item list-group-item-action py-2 ripple disabled text-dark mt-3" aria-current="true">
                                <h5 <?php if ($locked) echo "disabled"; ?>>Lesson <?php echo $quizNo?></h5>
                            </a>
                                <form method="post">
                                    <button type="submit" class="list-group-item list-group-item-action py-2 ripple border border-top-0 border-end-0 border-start-0 border-bottom-1 border-dark <?php echo $class ?>" value="<?php echo $quizNo?>" name="quiz" <?php if ($locked) echo "disabled"; ?>>Quiz No. <?php echo $quizNo?></button>
                                </form>
                        <?php endforeach ?>
                    </div>
                </div>
            </nav>
            
        </div>
        <div class="col">
            <div class="container mt-3">
                
            </div>
            <div class="container bg-white mt-4 py-2 rounded mt-5">
                <div class="row">
                    <div class="col py-2">
                        <div class="col text-end">
                            <div class="rounded overflow-auto w-100 text-start">
                                <h2>Instructions:</h2><br/>
                                <h4>HONESTY AND NON – DISCLOSURE AGREEMENT.</h4><br/>

                                <p>1. I affirm that I will not give or receive any unauthorized help on these examinations/activities and all will be accomplished by myself only.</p>

                                <p>2. I affirm and commit not to confer with my classmates my answers to all examinations/activities and will never divulge the contents of these examinations/activities and anybody who are/will be taking the same subject.</p>

                                <p>3. I understand that if I am caught or traced to have copied from my classmates or plagiarized my answers, that this is equivalent to a failing grade in the particular examination/activity and/or other sanctions as stipulated in the Undergraduate Student Manual.</p>

                                <br/><br/><div class="border border-top-0 border-end-0 border-start-0 border-bottom-1 border-dark mb-2"></div><br/><br/>

                                <?php 
                                $takenQuizzes = array(); // an array to store the quizzes that the user has taken
                                $xmlUser = simplexml_load_file('xml/users.xml'); // load the XML file
                                foreach($xmlUser->user as $user) {
                                    if ((string)$user['uid'] == $user_id) {
                                        // add the quiz numbers and scores to the takenQuizzes array
                                        foreach ($user->quiz as $quiz) {
                                            $quizNo = (string)$quiz['qNo'];
                                            $quizScore = (string)$quiz;
                                            $takenQuizzes[$quizNo] = $quizScore;
                                        }
                                        break;
                                    }
                                }

                                $canTakeQuiz = !array_key_exists($selectedQuiz, $takenQuizzes); // check if the selected quiz has not been taken yet
                                ?>

                                <p><?php 
                                    if (!isset($takenQuizzes[$selectedQuiz]) || $takenQuizzes[$selectedQuiz] == "") {
                                        echo "If you AGREE with the agreement, you can proceed to the Quiz.";
                                        $canTakeQuiz = true;
                                    } else {
                                        echo "You have already taken Quiz $selectedQuiz. Your score is: ";
                                        echo "<b>".$takenQuizzes[$selectedQuiz]."</b>"; // display the score for the selected quiz
                                    }
                                    ?></p>

                                <?php if ($canTakeQuiz) { ?>
                                    <div class="post-text pt-2 mx-2">
                                        <form method="post" class="mb-2">
                                            <input type="submit" class="btn btn-danger pull-right" name="take" value="Take Quiz <?php echo $selectedQuiz?>" <?php echo $canTakeQuiz ? '' : 'disabled' ?>>
                                        </form>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to logout?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form action="" method="post">
                    <button type="submit" name="yes" class="btn btn-danger">Yes</button>
                </form>
            </div>
        </div>
    </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="./scripts/users.js"></script>
</body>
</html>