<?php
session_start();
$user_id = $_SESSION["uid"];
$xml = simplexml_load_file('xml/users.xml');

$firstName = "";
$lastName = "";
$username = "";
$email = "";
$role = "";

foreach($xml->user as $user){
    if($user["uid"] == $user_id){
        $firstName = $user->firstName;
        $lastName = $user->lastName;
        $username = $user->username;
        $email = $user->email;
        $role = $user->role;
    }
}
$_SESSION['firstName'] = (string)$firstName;
$_SESSION['lastName'] = (string)$lastName;

$quiz_count = 0;
$assess_count = 0;
$quiz_score = 0;
$assess_score = 0;

foreach ($xml->user as $user) {
    if ((string) $user['uid'] === $user_id) {
        foreach ($user->quiz as $quiz) {
            if (!empty($quiz)) {
                $quiz_count++;
            }
        }

        foreach ($user->assess as $assess) {
            if (!empty($assess)) {
                $assess_count++;
            }
        }

        break;
    }
}


//change password
if(isset($_POST['submit_u'])){
    
    $error = array(); // Initialize error array
    
    // Check for duplicate email and username
    foreach ($xml->user as $user) {

        if ($user['uid'] == $user_id) {
            if ($user->password != $_POST['old_pass']) {
                $error[] = "Old password doesn't match. <br>";
            }
            if ($_POST['new_pass'] != $_POST['confirm_pass']) {
                $error[] = "Your New password and confirm passowrd doesn't match. <br>";
            }
        }
    }

    // Update user data if no duplicates found
    if (empty($error)) {
        foreach ($xml->user as $user) {
            if ($user['uid'] == $user_id) {
                // Update the user's data
                $user->password = $_POST['new_pass'];
                
                // Save the updated XML data
                if ($xml->asXML('xml/users.xml')) {
                    $error[] = "User password updated successfully. <br>";
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
                    $activityLog->addChild("action", "User".$user_id." password changed");
                    $activityLog->addChild("date", $current_date);
                    $activityLog->addChild("time", $current_time);

                    $logs->asXML("xml/activity_logs.xml");
                } else {
                    $error[] = "Error updating password. <br>";
                }

                // Stop searching for users
                break;
            }
        }
    }
}

//submit edit
if(isset($_POST['submit_c'])){
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $error = array(); // Initialize error array
    
    // Check for duplicate email and username
    foreach ($xml->user as $user) {
        if ($user['uid'] != $user_id) {
            if ($user->email == $_POST['update_email']) {
                $error[] = "Email already exists. Please choose a different email. <br>";
            }
            if ($user->username == $_POST['update_username']) {
                $error[] = "Username already exists. Please choose a different username. <br>";
            }
        }
    }

    // Update user data if no duplicates found
    if (empty($error)) {
        foreach ($xml->user as $user) {
            if ($user['uid'] == $user_id) {
                // Update the user's data
                $user->firstName = $_POST['update_fName'];
                $user->lastName = $_POST['update_lName'];
                $user->email = $_POST['update_email'];
                $user->username = $_POST['update_username'];

                // Create directory for user files if it doesn't already exist
                $dirname = $user['username'];
                $target_dir = "uploads/$dirname/files/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                // Handle profile picture upload
                if(isset($_FILES["profile_picture"]) && !empty($_FILES["profile_picture"]["tmp_name"])){
                    
                    $user->profilePicture = $target_file;
                    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
                    if($check !== false) {
                        echo "File is an image - " . $check["mime"] . ".";
                        $uploadOk = 1;
                    } else {
                        echo "File is not an image.";
                        $uploadOk = 0;
                    }
                }
                // Save the updated XML data
                if ($xml->asXML('xml/users.xml')) {
                    $error[] = "User data updated successfully. <br>";
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
                        $activityLog->addChild("action", "User".$user_id." updated profile");
                        $activityLog->addChild("date", $current_date);
                        $activityLog->addChild("time", $current_time);

                        $logs->asXML("xml/activity_logs.xml");
                } else {
                    $error[] = "Error updating user data. <br>";
                }

                // Stop searching for users
                break;
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
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $error[] = "The file ". htmlspecialchars( basename( $_FILES["profile_picture"]["name"])). " has been uploaded.";
        } else {
            $error[] = "Sorry, there was an error uploading your file.";
        }
    }
}


//delete acc
if(isset($_POST['submit_d'])){
    
    $uid = $_POST['delete_uid'];

    // Find the user with the given uid
    $userToDelete = null;
    foreach ($xml->user as $user) {
        if ($user['uid'] == $uid) {
            $userToDelete = $user;
            break;
        }
    }

    if (!$userToDelete) {
        $error[] = "User not found <br>";
    } else {
        // Remove the user's data
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
        $activityLog->addChild("action", "Account".$uid." deleted");
        $activityLog->addChild("date", $current_date);
        $activityLog->addChild("time", $current_time);

        $logs->asXML("xml/activity_logs.xml");

        unset($userToDelete[0]);

        // Save the updated XML data
        if ($xml->asXML('xml/users.xml')) {
            $error[] = "User deleted successfully <br>";
            header("Location: login.php");
        } else {
            $error[] = "Error deleting user <br>";
        }
    }
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
        <title>Profile</title>
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
                    <a class="nav-link" href="about.php">About Us</a>
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
        
        <div class="row" id="about">
            <div class="col-sm-3">

            </div>

            <div class="col-6">
                    <div class="bg-white">
                        <div class="row">
                            <div class="col-3 text-center mt-4 mb-4">
                                <?php
                                    $profilePic = isset($user->profilePicture) ? $user->profilePicture : '';

                                    if (!empty($profilePic)) {
                                        echo '<img src="' . $profilePic . '" alt="Profile Picture" class="img-fluid rounded-circle border border-dark border-2" style="width: 100px; height:100px;">';
                                    } else {
                                        echo '<i class="bi bi-person-circle" id="profile"></i>';
                                    }
                                ?>
                            </div>
                            <div class="col mt-4">
                                <p>Hello!</p>
                                <h3><?php echo $username?></h3>
                            </div>
                            <div class="col-3 mt-4">
                            <?php
                                foreach ($xml->user as $user) {
                                    $quizzes = [];
                                    $assessments = [];
                                    $notTaken = 0;
                                    if ($user['uid'] == $user_id = $_SESSION["uid"]){
                                        // Iterate through the quizzes
                                        foreach ($user->quiz as $quiz) {
                                            if (empty($quiz)) {
                                                // The quiz is not answered
                                                $notTaken++;
                                                echo "Quiz " . $quiz['qNo'] . " is not answered by " . $user->username . "<br>";
                                            } else {
                                                $quizzes[] = (int)$quiz;
                                            }
                                        }

                                        // Iterate through the assessments
                                        foreach ($user->assess as $assess) {
                                            if (empty($assess)) {
                                                // The assessment is not answered
                                                $notTaken++;
                                            } else {
                                                $assessments[] = (int)$assess;
                                            }
                                        }

                                        // Calculate the total score
                                        $totalScore = array_sum($quizzes) + array_sum($assessments);

                                        // Check if the user gets a passing grade (total score >= 60)
                                        if($notTaken == 0){
                                            if ($totalScore >= 35) {
                                                // The user passed, print a certificate
                                                ?>
                                                <a class="btn btn-danger" href="certificate_generate.php">Print Certificate</a>
                                                <?php
                                            }
                                        }
                                    }
                                    
                                }
                                
                                
                            ?>
                            </div>
                            <div class="px-4" id="border">
                                <div class="border-bottom border-dark"></div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col">
                                <nav class="list-group px-3">
                                    <ul class="list-inline border-bottom border-dark">
                                        <li class="list-inline-item">
                                            <a class="nav-link" href="profile.php">Quizzes</a>
                                        </li>
                                        <li class="list-inline-item border-bottom border-2 border-danger">
                                            <a class="nav-link" href="profile_assess.php">Assessments</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div class="row bg-white d-flex rounded overflow-auto m-2 w-100" style="height: 600px;">
                            <div class="col mx-4">
                                <div>
                                    <?php foreach($xml->user as $user){
                                    $quiz_count = 0;
                                    $assess_count = 0;
                                    $quiz_score = 0;
                                    $assess_score = 0;
                                    if($user["uid"] == $user_id){
            
                                        foreach($user->assess as $assess){
                                            $assess_count++;
                                            echo "Assessment No. ".$assess_count.": ";
                                            if(!empty($assess)) {
                                                $hdrd = $assess * 10;
                                            ?>  
                                            <div class="progress mb-2">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $hdrd?>%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"><?php echo $assess?></div>
                                            </div>
                                            <?php 
                                                $assess_score += intval($assess);
                                            }
                                        }
            
                                        echo "<br><br><br>Assessment score: <b>".$assess_score."</b><br>";
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        
                    </div>
            </div>

            <div class="col-sm-3">
                <div class="bg-white pt-4 px-2">
                    <h6>Information:</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Name: <?php echo $lastName?>, <?php echo $firstName?></li>
                        <li class="list-group-item">Role: <?php echo $role?></li>
                        <li class="list-group-item">Email: <?php echo $email?></li>
                        <li class="list-group-item"><button type="button" class="btn btn-danger w-100 mt-3" data-bs-toggle="modal" data-bs-target="#create">Edit Profile</button></li>
                        <li class="list-group-item"><button type="button" id="editBtn" class="btn btn-secondary w-100 mb-5" data-bs-toggle="modal" data-bs-target="#update">Change Password</button></li>
                        <li class="list-group-item"><button type="button" id="deleteBtn" class="btn btn-warning w-100 mt-5" data-bs-toggle="modal" data-bs-target="#delete">Delete Account</button></li>
                        <li class="list-group-item">
                        <?php if (isset($error)) {
                        foreach ($error as $error) {
                            echo $error;
                        }
                        } ?></li>
                    </ul> 
                </div>
            </div>
            <!-- Modal -->          
            <!-- edit modal -->
            <div class="modal fade bd-example-modal-lg" id="create" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Edit Profile</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="msodal-body text-center p-3">
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="form-group d-flex flex-column w-50">
                                        <div class="form-floating">
                                            <input type="text" name="update_fName" id="create_fName" placeholder="Name" class="form-control border" value="<?php echo $firstName?>" required>
                                            <label for="create_fName">First Name:</label>
                                        </div>
                                    </div>
                                    <div class="form-group d-flex flex-column w-50">
                                        <div class="form-floating">
                                            <input type="text" name="update_lName" id="create_lName" placeholder="Name" class="form-control border" value="<?php echo $lastName?>" required>
                                            <label for="create_lName">Last Name:</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="form-group d-flex flex-column w-100">
                                        <div class="form-floating">
                                            <input type="text" name="update_username" class="form-control border" id="create_username" placeholder="Email"value="<?php echo $username?>" required>
                                            <label>Username:</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="form-group d-flex flex-column w-100">
                                        <div class="form-floating">
                                            <input type="email" name="update_email" class="form-control border" id="create_email" placeholder="Email"value="<?php echo $email?>" required>
                                            <label>Email:</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center my-2">
                                    <div class="mb-3">
                                        <label for="formFile" class="form-label pull-left mt-2">Upload Profile:</label>
                                        <input class="form-control" name="profile_picture" type="file" id="formFile">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn border" data-bs-dismiss="modal">Cancel</button>
                                    <input type="submit" name="submit_c" value="Update" class="btn btn-danger">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- update modal -->
            <div class="modal fade bd-example-modal-lg" id="update" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel"><label id="userId">Change Password</label></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="msodal-body text-center p-3">
                            <form action="" method="post">
                                <div class="row my-2">
                                    <div class="form-group d-flex flex-column w-100 mb-4">
                                        <div class="form-floating">
                                            <input type="password" name="old_pass" class="form-control border" id="old_pass" placeholder="Old Password"  required>
                                            <label>Old Password:</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="form-group d-flex flex-column w-100">
                                        <div class="form-floating">
                                            <input type="password" name="new_pass" class="form-control border" id="new_pass" placeholder="New Password"  required>
                                            <label>New Password:</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="form-group d-flex flex-column w-100">
                                        <div class="form-floating">
                                            <input type="password" name="confirm_pass" class="form-control border" id="confirm_pass" placeholder="Confirm Password"  required>
                                            <label>Confirm New Password:</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn border" data-bs-dismiss="modal">Cancel</button>
                                    <input type="submit" name="submit_u" value="Update" class="btn btn-secondary">
                                </div>
                            </form>
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
                                <p class="h5">Are you sure you want to delete your Account?</p><br>
                                <p class="h5 text-danger" id="deleteTxt"></p>
                                <div class="modal-footer my-3">
                                    <button type="button" class="btn border" data-bs-dismiss="modal">Close</button>
                                    <form action="" method="post">
                                    <div class="row my-2">
                                        <input type="hidden" name="delete_uid" class="form-control border" id="delete_uid" value="">
                                        <input type="submit" name="submit_d" value="Delete" class="btn btn-danger">
                                    </form>
                                </div>
                            </div>
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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>