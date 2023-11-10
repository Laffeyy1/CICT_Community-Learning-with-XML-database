<?php
session_start();

$user_id = $_SESSION["uid"];

$xmlUser = new DOMDocument();
$xml = simplexml_load_file("xml/lessons.xml");
// $user =$_SESSION["user"];
$selectedLesson = "1001";

//check the selected lesson
if (isset($_POST["lesson"])) {
    $selectedLesson = $_POST["lesson"];
    $_SESSION["selectedLesson"] = $_POST["lesson"];
}

$xmlUser = simplexml_load_file('xml/users.xml');

$i = 1;

$quiz_count = 1;

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

if (isset($_POST['yes'])){
    header('Location: logout.php');
    exit;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
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
                    <a class="nav-link border-bottom border-dark" href="home.php">Home <span class="sr-only">(current)</span></a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link" href="quiz.php">Quizzes<span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="assessments.php">Assessment<span class="sr-only"></span></a>
                </li>
            </ul>
        </div>


        <!--Profile-->
        <div class="dropdown">
            <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                <?php
                    $profilePic = isset($user->profilePicture) ? $user->profilePicture : '';

                    if (!empty($profilePic)) {
                        echo '<img src="' . $profilePic . '" alt="Profile Picture" class="img-fluid rounded-circle border border-dark border-2" style="width: 40px; height:40px;">';
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
    <div class="container-fluid">
    <div class="row min-vh-100" id="center_body">
        <div class="col-3">
            <nav class="sidebar bg-white pb-2 rounded" style="height:850px;">
                <div class="list-group list-group-flush mx-3 mt-4">
                    <div class=" rounded overflow-auto w-100 t" style="height:800px;">
                    <?php foreach($xml->unit as $unit) : 
                        $unitNo = $unit["unitNo"];?>
                        <a href="#" class="list-group-item list-group-item-action py-2 ripple disabled text-dark mt-3" aria-current="true">
                            <h5>Unit <?php echo $unitNo?></h5>
                        </a>
                        <?php foreach($unit->lesson as $lesson) : 
                            $title = $lesson->title;
                            $lid = $lesson->lid;
                            $class = ($selectedLesson == $lid) ? "bg-danger text-white" : "";
                            $show = ($selectedLesson == $lid) ? "show" : "";
                            if( $i <= $quiz_count){
                                $quizlid = $i + 1000;
                                $i = $i + 1;
                            }
                            $locked = ($lid == $quizlid) ? false : true; // set locked state based on lesson ID and quiz count
                        ?>
                            <ul class="list-unstyled ps-0">
                                <li class="mb-1">
                                    <button class="btn btn-toggle d-inline-flex rounded border-0 collapsed fw-bold" data-bs-toggle="collapse" data-bs-target="#lesson<?php echo $lid;?>" aria-expanded="<?php echo $show === 'show' ? 'true' : 'false';?>" <?php if ($locked) echo "disabled"; ?>>
                                        <?php echo $title?>
                                    </button>
                                    <div class="collapse show" id="lesson<?php echo $lid;?>">
                                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">                                 
                                            <form method="post">
                                                <?php foreach($lesson->sublesson as $sublesson) :
                                                    $subTitle = $sublesson->title;
                                                    $subLid = $sublesson->lid;
                                                    $class = ($selectedLesson == $subLid) ? "bg-danger text-white" : "";
                                                    $show = ($selectedLesson == $subLid) ? "show" : "";
                                                    
                                                ?>
                                                    <li><button type="submit" class="list-group-item list-group-item-action py-2 ripple border border-white <?php echo $class ?>" value="<?php echo $subLid?>" name="lesson" <?php if ($locked) echo "disabled"; ?> <?php echo $show?>><?php echo $subTitle?></button></li>
                                                <?php endforeach ?>
                                            </form>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        <?php endforeach ?>
                    <?php endforeach ?>
                    </div>
                </div>
            </nav>
        </div>
        <div class="col">
            <div class="container bg-white mt-4 py-2 rounded">
                <div class="row">
                    <div class="col py-2">
                        <div class="col text-end">
                        <?php foreach($xml->unit as $unit) :?>
                            <?php foreach($unit->lesson as $lesson) : 
                                    $title = $lesson->title;
                                    $lid = $lesson->lid;
                                    $body = $lesson->body;
                                    if ($selectedLesson == $lid) {?>

                                <div class="fw-bold">
                                    <a class="text-decoration-none text-black pull-left" href="#">
                                        <h2><?php echo $title?></h2>
                                    </a>
                                </div>
                                <div class=" rounded overflow-auto w-100 text-start" style="height:700px;">
                                <div class="post-text pt-2">
                                    <?php echo $body?>
                                </div>
                                <?php
                                    $image = isset($lesson->lesson) ? $lesson->image : '';

                                    if (!empty($image)) {
                                        echo '<img class="img-fluid" src="'. $image .'">';
                                    }
                                ?>
                                </div>
                                <?php }?>
                                    <?php foreach($lesson->sublesson as $sublesson) : 
                                        $title = $sublesson->title;
                                        $lid = $sublesson->lid;
                                        $body = $sublesson->body;
                                        if ($selectedLesson == $lid) {?>
                                    <div class="fw-bold">
                                        <a class="text-decoration-none text-black pull-left" href="#">
                                            <h2><?php echo $title?></h2>
                                        </a>
                                    </div>
                                    <div class=" rounded overflow-auto w-100 text-start" style="height:700px;">
                                    <div class="post-text pt-2">
                                        <?php echo $body?>
                                    </div>
                                    <?php
                                        $image = isset($sublesson->lesson) ? $sublesson->image : '';

                                        if (!empty($image)) {
                                            echo '<img class="img-fluid" src="'. $image .'">';
                                        }
                                    ?>
                                    </div>
                                    <?php }?>                 
                                <?php endforeach ?>
                            <?php endforeach ?>
                        <?php endforeach ?>
                        <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    ...
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary">Save changes</button>
                                </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="./scripts/users.js"></script>
</body>
</html>