<?php
session_start();
$xml = simplexml_load_file("xml/assessments.xml");
$_SESSION["selectedQuestion"] = null;

if (isset($_POST["assess"])) {
    $_SESSION["selectedAssess"] = $_POST["assess"];
}

if (isset($_POST["question"])) {
    $_SESSION["selectedQuestion"] = $_POST["question"];
}

$selectedAssess = isset($_SESSION["selectedAssess"]) ? $_SESSION["selectedAssess"] : null;
$selectedQuestion = isset($_SESSION["selectedQuestion"]) ? $_SESSION["selectedQuestion"] : null;
$disabled = (!isset($_SESSION["selectedQuestion"]) || empty($_SESSION["selectedQuestion"])) ? "disabled" : "";
//delete lesson
if (isset($_POST['submit_d'])) {

    $deleteAssess = $_SESSION["selectedAssess"];
    $deleteQuestion = $_POST["delete_uid"];

    // Find the assessment with the given assessNo and the question with the given question number
    $assessmentToDelete = null;
    $questionToDelete = null;

    foreach ($xml->assessment as $assessment) {
        if ($assessment['assessNo'] == $deleteAssess) {
            foreach($assessment->item as $item) {
                if ($item['itemNo'] == $deleteQuestion) {
                    // Found the question to delete
                    $questionToDelete = $item;
                    break;
                }
            }
            if ($questionToDelete) {
                // Remove the question from the assessment
                unset($questionToDelete[0]);
                // Re-index item numbers
                $itemNo = 1;
                foreach ($assessment->item as $item) {
                    $item['itemNo'] = $itemNo;
                    $itemNo++;
                }
                // Save the changes to the XML file
                $xml->asXML('xml/assessments.xml');
                // Display success message
                $error[] = "Question deleted successfully <br>";
                break;
            }
        }
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
                    <a class="nav-link border-bottom border-dark" href="a_manage_users.php">Back <span class="sr-only">(current)</span></a>
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
        <div class="col-3">
            <nav class="sidebar bg-white pb-2 h-100">
                <div>
                    <div class="list-group list-group-flush mx-3 mt-4">
                    <?php foreach($xml->assessment as $assessment) : 
                        $assessNo = $assessment["assessNo"];
                        $class = ($selectedAssess == $assessNo) ? "bg-danger text-white" : "";
                    ?>
                        <a href="#" class="list-group-item list-group-item-action py-2 ripple disabled text-dark mt-3" aria-current="true">
                            <h5>Unit <?php echo $assessNo?></h5>
                        </a>
                            <form method="post">
                                <button type="submit" class="list-group-item list-group-item-action py-2 ripple border border-top-0 border-end-0 border-start-0 border-bottom-1 border-dark <?php echo $class ?>" value="<?php echo $assessNo?>" name="assess">Assessment No. <?php echo $assessNo?></button>
                            </form>
                    <?php endforeach ?>
                    </div>
                </div>
            </nav>
        </div>
        <div class="col">
            <div class="container mt-3">
                <h5>Manage Assessments
                    <button type="button" id="deleteBtn"class="btn btn-danger pull-right mx-3" data-bs-toggle="modal" data-bs-target="#delete" <?php echo $disabled ?>>
                        Delete
                    </button>
                    <button type="button" id="deleteBtn"class="btn border border-dark pull-right mx-3" onclick="location.href='a_edit_assessment.php'" <?php echo $disabled ?>>
                        Edit
                    </button>
                    <button type="button" id="deleteBtn"class="btn btn-success pull-right mx-3" onclick="location.href='a_create_assessment.php'">
                        Create
                    </button>
                    <?php
                    if (isset($error)) {
                        foreach ($error as $error) {
                            echo $error;
                        }
                    }
                    ?>
                </h5>
            </div>
            <div class="container bg-white mt-4 py-2 rounded">
                <div class="row">
                    <div class="col py-2">
                        <div class="col text-end">
                        <div class=" rounded overflow-auto w-100 text-start" style="height:700px;">
                        <?php foreach($xml->assessment as $assessment) :
                                    $assessNo = $assessment["assessNo"]; 
                                    if ($selectedAssess == $assessNo) {// move this line outside the inner loop
                            ?>
                                <?php foreach($assessment->item as $item) :
                                        $itemNo = $item["itemNo"];
                                        $question = $item->question;
                                        $ans1 = $item->choice1;
                                        $ans2 = $item->choice2;
                                        $ans3 = $item->choice3;
                                        $ans4 = $item->choice4;
                                        
                                            $class = ($selectedQuestion == $itemNo) ? "bg-danger text-white" : ""; // change this line
                                ?>
                                    <div class="post-text pt-2 mx-2">
                                        <form method="post" class="mb-2">
                                            <button type="submit" class="list-group-item list-group-item-action py-2 ripple fw-bold <?php echo $class ?>" value="<?php echo $itemNo?>" name="question"><?php echo $question?></button>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" />
                                                <label class="form-check-label" for="inlineRadio1"><?php echo $ans1?></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2" />
                                                <label class="form-check-label" for="inlineRadio2"><?php echo $ans2?></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3" />
                                                <label class="form-check-label" for="inlineRadio3"><?php echo $ans3?></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option4"/>
                                                <label class="form-check-label" for="inlineRadio4"><?php echo $ans4?></label>
                                            </div>
                                        </form>
                                        <div class="border border-top-0 border-end-0 border-start-0 border-bottom-1 border-dark mb-2"></div>
                                    </div>
                                
                            <?php endforeach ?>
                            <?php }?>
                        <?php endforeach ?>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- delete modal -->
            <div class="modal fade bd-example-modal-lg" id="delete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel" ><label id="delete_id"></label></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body text-center">
                            <p class="h5">Are you sure you want to delete this question?</p><br>
                            <?php foreach($xml->assessment as $assessment) :
                                    $assessNo = $assessment["assessNo"]; 
                                    if ($selectedAssess == $assessNo) {// move this line outside the inner loop
                            ?>
                                <?php foreach($assessment->item as $item) :
                                        $itemNo = $item["itemNo"];
                                        $question = $item->question;
                                        
                                    if ($selectedQuestion == $itemNo) {
                                ?>     
                                <p class="h5 text-danger" id="deleteTxt"><?php echo $itemNo;?> • <?php echo $question;?></p>
                                            <div class="modal-footer my-3">
                                                <button type="button" class="btn border" data-bs-dismiss="modal">Close</button>
                                                <form action="" method="post">
                                                    <div class="row my-2">
                                                        <input type="hidden" name="delete_uid" class="form-control border" id="delete_uid" value="<?php echo $selectedQuestion;?>">
                                                        <input type="submit" name="submit_d" value="Delete" class="btn btn-danger">
                                                    </div>
                                                </form>
                                            </div>  
                                            <?php }?>    
                            <?php endforeach ?>
                            <?php }?>
                        <?php endforeach ?>
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