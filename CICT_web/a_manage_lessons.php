<?php
session_start();
$xml = simplexml_load_file("xml/lessons.xml");

$selectedLesson = "1001";

//check the selected lesson of admin
if (isset($_POST["lesson"])) {
    $selectedLesson = $_POST["lesson"];
    $_SESSION["selectedLesson"] = $_POST["lesson"];
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
    <div class="container-fluid mt-5">
        <div class="row min-vh-100 mt-5" id="center_body">
            <div class="col-3 mt-5">
                <nav class="sidebar bg-white pb-2 rounded" style="height:800px;">
                    <div class="list-group list-group-flush mx-3 mt-4">
                        <div class="rounded overflow-auto w-100" style="height:800px;">
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
                                ?>
                                    <ul class="list-unstyled ps-0">
                                        <li class="mb-1">
                                            <button class="btn btn-toggle d-inline-flex rounded border-0 collapsed fw-bold" data-bs-toggle="collapse" data-bs-target="#lesson<?php echo $lid;?>" aria-expanded="<?php echo $show === 'show' ? 'true' : 'false';?>">
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
                                                            <li><button type="submit" class="list-group-item list-group-item-action py-2 ripple border border-white <?php echo $class ?>" value="<?php echo $subLid?>" name="lesson" <?php echo $show?>><?php echo $subTitle?></button></li>
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
            <div class="col mt-5">
                <div class="container mt-3">
                    <h5>Manage Lessons
                        <button type="button" id="deleteBtn"class="btn btn-danger pull-right mx-3" data-bs-toggle="modal" data-bs-target="#delete">
                            Delete
                        </button>
                        <button type="button" id="deleteBtn"class="btn border border-dark pull-right mx-3" onclick="location.href='a_edit_lesson.php'">
                            Edit
                        </button>
                        <button type="button" id="deleteBtn"class="btn btn-success pull-right mx-3" onclick="location.href='a_create_lesson.php'">
                            Create
                        </button>
                    </h5>
                </div>
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
                                        $image = isset($sublesson->image) ? $sublesson->image : '';

                                        if (!empty($image)) {
                                            echo '<img class="img-fluid" src="'. $image .'">';
                                        }
                                        ?>
                                        </div>
                                        <?php }?>                 
                                    <?php endforeach ?>
                                <?php endforeach ?>
                            <?php endforeach ?>
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
                                <p class="h5">Are you sure you want to delete this lesson?</p><br>
                                <?php foreach($xml->unit as $unit) :?>
                                    <?php foreach($unit->lesson as $lesson) : 
                                        $title = $lesson->title;
                                        $lid = $lesson->lid;
                                        $body = $lesson->body;
                                        if ($selectedLesson == $lid) {?>
                                            <p class="h5 text-danger" id="deleteTxt"><?php echo $selectedLesson;?> • <?php echo $title;?></p>
                                            <div class="modal-footer my-3">
                                            <button type="button" class="btn border" data-bs-dismiss="modal">Close</button>
                                            <form action="" method="post">
                                            <div class="row my-2">
                                                <input type="hidden" name="delete_uid" class="form-control border" id="delete_uid" value="<?php echo $selectedLesson;?>">
                                                <input type="submit" name="submit_d" value="Delete" class="btn btn-danger">
                                            </form>
                                        </div>
                                        <?php }?>
                                        <?php foreach($lesson->sublesson as $sublesson) : 
                                            $title = $sublesson->title;
                                            $lid = $sublesson->lid;
                                            $body = $sublesson->body;
                                            if ($selectedLesson == $lid) {?>
                                            <p class="h5 text-danger" id="deleteTxt"><?php echo $selectedLesson;?> • <?php echo $title;?></p>
                                            <div class="modal-footer my-3">
                                            <button type="button" class="btn border" data-bs-dismiss="modal">Close</button>
                                            <form action="" method="post">
                                            <div class="row my-2">
                                                <input type="hidden" name="delete_uid" class="form-control border" id="delete_uid" value="<?php echo $selectedLesson;?>">
                                                <input type="submit" name="submit_d" value="Delete" class="btn btn-danger">
                                            </form>
                                        </div>
                                        </div>
                                        <?php }?>                 
                                        <?php endforeach ?>
                                    <?php endforeach ?>
                                <?php endforeach ?>
                            </div>
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