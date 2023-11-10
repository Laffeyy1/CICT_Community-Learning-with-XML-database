<?php
session_start();
$xml = simplexml_load_file("xml/assessments.xml");

$selectedQuestion = $_SESSION["selectedQuestion"];
$selectedAssess = $_SESSION["selectedAssess"];

if (isset($_POST["submit"])) {

    $title = $_POST["title"];
    $body = $_POST["body"];
    $log = " Posted";
    $alog = "Uploaded ";

    foreach ($xml->assessment as $assessment) {
        if($assessment["assessNo"] == $selectedAssess){
            foreach($assessment->item as $item){
                $itemNo = $item["itemNo"];
                if($selectedQuestion == $itemNo){
                    // Found the lesson to update
                    $item->question = $_POST["question"];
                    $item->choice1 = $_POST["choice1"];
                    $item->choice2 = $_POST["choice2"];
                    $item->choice3 = $_POST["choice3"];
                    $item->choice4 = $_POST["choice4"];
                    $item->answer = $_POST["answer"];
    
                }
            }
        }
    }


    $xml->asXML('xml/assessments.xml');
    header('location:a_manage_assessments.php');
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
            <li class="nav-item">
                <a class="nav-link border-bottom border-dark" href="a_manage_assessments.php">Back <span class="sr-only">(current)</span></a>
            </li>
        </ul>

    </div>

    <!--Profile-->
    <div class="dropdown">
        <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle"></i>
        </a>
        <ul class="dropdown-menu" id="profile_drop">
            <li><a class="dropdown-item" href="a_dashboard.php">Dashboard</a></li>
            <li><a class="dropdown-item" href="logout.php?logout">Logout</a></li>
        </ul>
    </div>
    </nav>
    <div class="container-fluid">
    <div class="row min-vh-100" id="center_body">
    <div class="col">
        <div class="container mt-3">
            <h5>Edit Assessment</h5>
        </div>
        <div class="container bg-white mt-4 py-2 rounded d-flex justify-content-center align-items-center h-75">
            <div class="row">
                <div class="col py-2">
                    <div class="col text-end">
                    <form action="" method="post">
                        <?php foreach($xml->assessment as $assessment) :
                            if($assessment["assessNo"] == $selectedAssess){?>
                                <?php foreach($assessment->item as $item) : 
                                $itemNo = $item["itemNo"];
                                        if ($selectedQuestion == $itemNo) {
                                            $question = $item->question;
                                            $choice1 = $item->choice1;
                                            $choice2 = $item->choice2;
                                            $choice3 = $item->choice3;
                                            $choice4 = $item->choice4;
                                            $answer = $item->answer;?>         
                                        <div class=" rounded overflow w-100 text-start">
                                            <div class="row mb-5">
                                                <div class="input-group input-group-lg">
                                                    <span class="input-group-text" id="inputGroup-sizing-lg">Question: </span>
                                                    <input type="text" name="question" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg" value="<?php echo $question?>" required>
                                                </div>
                                            </div>
                                            <div class="row mb-5">
                                                <div class="col input-group">
                                                    <span class="input-group-text" id="inputGroup-sizing">Choice 1: </span>
                                                    <input type="text" name="choice1" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing" value="<?php echo $choice1?>" required>
                                                </div>
                                                <div class="col input-group">
                                                    <span class="input-group-text" id="inputGroup-sizing">Choice 2: </span>
                                                    <input type="text" name="choice2" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing" value="<?php echo $choice2?>" required>
                                                </div>
                                                <div class="col input-group">
                                                    <span class="input-group-text" id="inputGroup-sizing">Choice 3: </span>
                                                    <input type="text" name="choice3" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing" value="<?php echo $choice3?>" required>
                                                </div>
                                                <div class="col input-group">
                                                    <span class="input-group-text" id="inputGroup-sizing">Choice 4: </span>
                                                    <input type="text" name="choice4" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing" value="<?php echo $choice4?>" required>
                                                </div>
                                            </div>
                                            <div class="row mb-5">
                                                <div class="input-group input-group-lg">
                                                    <span class="input-group-text" id="inputGroup-sizing-lg">Answer: </span>
                                                    <input type="text" name="answer" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg" value="<?php echo $answer?>" required>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }?>
                                <?php endforeach ?>
                                <?php }?>
                            <?php endforeach ?>
                            <input type="submit" class="btn btn-danger" name="submit">
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