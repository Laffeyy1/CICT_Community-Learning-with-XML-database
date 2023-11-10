<?php
session_start();
$xml = simplexml_load_file("xml/lessons.xml");


$dirname = uniqid();
$curdir = getcwd();
$uploadOk = 1;
$target_file = '';
$target_dir = '';
$err = '';

if (isset($_POST["submit"])) {

    if (!isset($_FILES['image']) && !isset($_FILES['files'])) {
        //no upload image

        $title = $_POST["title"];
        $body = $_POST["body"];
        $log = " Posted";
        $alog = "Uploaded ";
        $lessonCount;
        mkdir('post/' . $dirname . '/files', 0777, true);

        //has no image but has file
        $foundUnit = false;
        foreach ($xml->unit as $unit) {
            $unitNo = $unit->attributes()['unitNo'];
            if ($unitNo == $_POST["unit"]) {
                // Found the unit to add the lesson to
                $foundUnit = true;

                // Get the number of existing lessons
                $existingLessonCount = count($unit->lesson);

                // Set the new lesson lid as 1 greater than the highest existing lesson lid
                $newLessonLid = $existingLessonCount > 0 ? intval($unit->lesson[$existingLessonCount - 1]->lid) + 1 : 1;
                
                // Create a new <lesson> element
                $newLesson = $unit->addChild('lesson');
                
                // Set the properties for the new lesson
                $newLesson->addChild('lid', $newLessonLid);
                $newLesson->addChild('title', $_POST["title"]);
                $newLesson->addChild('body', $_POST["body"]);
                break;
            }
        }
        
        if (!$foundUnit) {

            // Get the number of existing lessons
            $existingLessonCount = count($unit->lesson);

            // Set the new lesson lid as 1 greater than the highest existing lesson lid
            $newLessonLid = $existingLessonCount > 0 ? intval($unit->lesson[$existingLessonCount - 1]->lid) + 1 : 1;
            
            // Unit not found, create a new one with the specified unit number
            $newUnit = $xml->addChild('unit');
            $newUnit->addAttribute('unitNo', $_POST["unit"]);
            // Add the new lesson to the new unit
            $newLesson = $newUnit->addChild('lesson');
            $newLesson->addChild('lid', $newLessonLid);
            $newLesson->addChild('title', $_POST["title"]);
            $newLesson->addChild('body', $_POST["body"]);
        }

        if (isset($_FILES['files']) && isset($_FILES['files']['name'])) {
            $total = count($_FILES['files']['name']);
            
            // Loop through each file
            for ($i = 0; $i < $total; $i++) {

                //Get the temp file path
                $tmpFilePath = $_FILES['files']['tmp_name'][$i];

                //Make sure we have a file path
                if ($tmpFilePath != "") {
                    //Setup our new file path
                    $newFilePath = "post/" . $dirname . "/files/" . $_FILES['files']['name'][$i];

                    //Upload the file into the temp dir
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        // Successfully uploaded file
                    }
                }
            }
        }

        $xml->asXML('xml/lessons.xml');
        header('location:a_manage_lessons.php');
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
            <li class="nav-item">
                <a class="nav-link border-bottom border-dark" href="a_manage_lessons.php">Back <span class="sr-only">(current)</span></a>
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
            <h5>Edit Lesson </h5>
        </div>
        <div class="container bg-white mt-4 py-2 rounded">
            <div class="row">
                <div class="col py-2">
                    <div class="col text-end">
                    <form action="" method="post">
                                    <div class="input-group input-group-lg mb-2">
                                        <span class="input-group-text" id="inputGroup-sizing-lg">Unit: </span>
                                        <input type="text" name="unit" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg" required>
                                    </div>    
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text" id="inputGroup-sizing-lg">Title: </span>
                                        <input type="text" name="title" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg" required>
                                    </div>    
                                    <div class=" rounded overflow-auto w-100 text-start" style="height:650px;">
                                        <div class="post-text pt-2">
                                            <div class="mb-3">
                                                <label for="body" class="form-label">Body:</label>
                                                <textarea class="form-control" name="body" id="body" rows="15" required></textarea>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="formFile" class="form-label">Attach Image:</label>
                                            <input class="form-control" name="image" type="file" id="formFile">
                                        </div>
                                        <div class="mb-3">
                                            <label for="formFile" class="form-label">Attach File:</label>
                                            <input class="form-control" name="file" type="file" id="formFile">
                                        </div>
                                    </div>            
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