<?php
include 'config/database.php';
session_start();

$title = $body = '';
$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];
$user_type = $_SESSION['user_type'];


$dirname = uniqid();
$curdir = getcwd();
$uploadOk = 1;
$target_file = '';
$target_dir = '';
$err = '';

if (isset($_POST["submit"])) {

    if (!file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
        //no upload image

        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $body = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $log = " Posted";
        $alog = "Uploaded ";
        
        mkdir('post/' . $dirname . '/files', 0777, true);

        $insert = "INSERT INTO post_form(title, body, location,auth_fname,auth_lname,auth_role) VALUES('$title','$body','$dirname','$fname','$lname','$user_type')";
        mysqli_query($conn, $insert);

        $ulog = "INSERT INTO `user_logs`(`fname`, `lname`, `role`, `action`) VALUES ('$fname','$lname','$user_type','$user_type$log')";
        mysqli_query($conn, $ulog);

        //has no image but has file

        $total = count($_FILES['files']['name']);

        $plog = "Post";
        $plog = "INSERT INTO `reports`(`user_type`, `activity`) VALUES ('$user_type','$plog')";
        mysqli_query($conn, $plog);

        $actlog = "INSERT INTO `activity_logs`(`name`, `action`) VALUES ('System','$alog$total Files')";
        mysqli_query($conn, $actlog);

        $ulog = "INSERT INTO `user_logs`(`fname`, `lname`, `role`, `action`) VALUES ('$fname','$lname','$user_type','$user_type$alog$total Files')";
        mysqli_query($conn, $ulog);
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

                    //Handle other code here
                    
                }
            }
        }
        header('location:home.php');
    }

} else {


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
    <title>Create Post</title>
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
                    <a class="nav-link" href="home.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About Us</a>
                </li>
            </ul>
            <!--Search-->
            <form class="d-flex w-50" id="sech">
                <input class="form-control mr-sm-2 " type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0 " type="submit"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                    </svg></button>
            </form>
        </div>

        <!--Notification-->
        <div class="dropdown">
            <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                <i class="bi bi-bell-fill"></i>
            </a>
            <ul class="dropdown-menu" id="notif_drop">
                <li><a class="dropdown-item" href="#">Some Notification</a></li>
                <li><a class="dropdown-item" href="#">Some Notification</a></li>
                <li><a class="dropdown-item" href="#">Some Notification</a></li>
            </ul>
        </div>

        <!--Profile-->
        <div class="dropdown">
            <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
            </a>
            <ul class="dropdown-menu" id="profile_drop">
                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                <li><a class="dropdown-item" href="logout.php?logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="row" id="about">
        <div class="col">

        </div>
        <div class="col bg-white">
            <div class="pb-2 pt-2 mb-2 mt-2 border-bottom border-dark">
                <h5> Create Post</h5>
            </div>
            <div>
                <form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    Title:
                    <input type="text" class="w-100 pb-1 pt-1 px-2 mb-2 mt-2 border border-dark" placeholder="Title..." name="title" required>
                    Text:
                    <textarea class="w-100 pb-1 pt-1 px-2 mb-2 mt-2 border border-dark" id="Text1" cols="40" rows="5" placeholder="Text..." name="body" required></textarea>
                    <label class="form-label" for="customFile">Attach Image:</label>
                    <input type="file" class="form-control mb-3 <?php echo !$err ? null : 'is-invalid'; ?>" id="customFile" name="image">
                    <label class="form-label" for="customFile">Attach Files:</label>
                    <input type="file" class="form-control mb-3" id="customFile" name="files[]" multiple="multiple">
                    <div>
                        <?php
                                echo '<span class="text-danger text-center">' . $err . '</span>';
                        ?>
                    </div>
                    <div class="d-flex justify-content-end mb-3"><a href="home.php" class="btn mx-2">Cancel</a><input type="submit" name="submit" class="btn btn-danger mx-2" value="Post"></div>
                </form>
            </div>
        </div>
        <div class="col">

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>