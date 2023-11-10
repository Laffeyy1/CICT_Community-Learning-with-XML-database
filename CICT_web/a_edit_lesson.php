<?php
session_start();
$xml = simplexml_load_file("xml/lessons.xml");

$selectedLesson = $_SESSION["selectedLesson"];

$dirname = uniqid();
$curdir = getcwd();
$uploadOk = 1;
$target_file = '';
$target_dir = '';
$err = '';

if (isset($_POST["submit"])) {
    $target_dir = "lessons/";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        // rest of your code
    } // <--- add this closing bracket
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $error = array(); // Initialize error array
    // rest of your code
    
        // Update user data if no duplicates found
        if (empty($error)) {
            $title = $_POST["title"];
            $body = $_POST["body"];
    
            //has no image but has file
            foreach ($xml->unit as $unit) {
                foreach($unit->lesson as $lesson){
                    if($selectedLesson == $lesson->lid){
                        // Found the lesson to update
                        $lesson->title = $_POST["title"];
                        $lesson->body = $_POST["body"];
    
                    }
                    else{
                        foreach($lesson->sublesson as $subLesson){
                            if($selectedLesson == $subLesson->lid){
                                // Found the sublesson to delete
                                $subLesson->title = $_POST["title"];
                                $subLesson->body = $_POST["body"];

                                // Create directory for user files if it doesn't already exist
                                $dirname = $subLesson['username'];
                                $target_dir = "lessons/$dirname/";
                                
                                if (!file_exists($target_dir)) {
                                    mkdir($target_dir, 0777, true);
                                }
                
                                // Handle profile picture upload
                                if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])){
                                    
                                    $subLesson->image = $target_file;
                                    $check = getimagesize($_FILES["image"]["tmp_name"]);
                                    if($check !== false) {
                                        echo "File is an image - " . $check["mime"] . ".";
                                        $uploadOk = 1;
                                    } else {
                                        echo "File is not an image.";
                                        $uploadOk = 0;
                                    }
                                }
                                // Save the updated XML data
                                if ($xml->asXML('xml/lessons.xml')) {
                                    $error[] = "User data updated successfully. <br>";
                                    header('Location: a_manage_lessons.php');
                                } else {
                                    $error[] = "Error updating user data. <br>";
                                }
                                break;
                            }
                        }
                    }
                }
            }
        }

            // Check if file already exists
        if (file_exists($target_file)) {
            $error[] = "Sorry, file already exists.";
            $uploadOk = 0;
        }
        
        
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            $error[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $error[] = "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $error[] = "The file ". htmlspecialchars( basename( $_FILES["image"]["name"])). " has been uploaded.";
            } else {
                $error[] = "Sorry, there was an error uploading your file.";
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
                    <form action="" method="post" enctype="multipart/form-data">
                        <?php foreach($xml->unit as $unit) :?>
                                <?php foreach($unit->lesson as $lesson) : 
                                        $title = $lesson->title;
                                        $lid = $lesson->lid;
                                        $body = $lesson->body;
                                        if ($selectedLesson == $lid) {?>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text" id="inputGroup-sizing-lg">Title: </span>
                                        <input type="text" name="title" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg" value="<?php echo $title?>" required>
                                    </div>
                                                    
                                    <div class=" rounded overflow-auto w-100 text-start" style="height:650px;">
                                        <div class="post-text pt-2">
                                            <div class="mb-3">
                                                <label for="body" class="form-label">Body:</label>
                                                <textarea class="form-control" name="body" id="body" rows="15" required><?php echo $body?></textarea>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="formFile" class="form-label">Attach Image:</label>
                                            <input class="form-control" name="image" type="file" id="formFile">
                                        </div>
                                    </div>
                                    <?php }?>
                                        <?php foreach($lesson->sublesson as $sublesson) : 
                                            $title = $sublesson->title;
                                            $lid = $sublesson->lid;
                                            $body = $sublesson->body;
                                            if ($selectedLesson == $lid) {?>

                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text" id="inputGroup-sizing-lg">Title: </span>
                                                <input type="text" name="title" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg" value="<?php echo $title?>" required>
                                            </div>
                                                            
                                            <div class=" rounded overflow-auto w-100 text-start" style="height:650px;">
                                                <div class="post-text pt-2">
                                                    <div class="mb-3">
                                                        <label for="body" class="form-label">Body:</label>
                                                        <textarea class="form-control" name="body" id="body" rows="15" required><?php echo $body?></textarea>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="formFile" class="form-label">Attach Image:</label>
                                                    <input class="form-control" name="image" type="file" id="formFile">
                                                </div>
                                            </div>
                                        <?php }?>                 
                                    <?php endforeach ?>
                                <?php endforeach ?>
                            <?php endforeach ?>
                            <input type="submit" class="btn btn-danger" name="submit">
                            <?php if (isset($error)) {
                        foreach ($error as $error) {
                            echo $error;
                        }
                        } ?>
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