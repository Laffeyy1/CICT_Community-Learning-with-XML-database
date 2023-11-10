<?php
session_start();

$xml = simplexml_load_file("xml/users.xml");

$error = array();

$current_datetime = date('Y-m-d H:i:s');
$current_date = date('Y-m-d', strtotime($current_datetime));
$current_time = date('H:i:s', strtotime($current_datetime));

if(isset($_POST['submit_u'])){
    
    $uid = $_POST['update_uid'];

    // Check for duplicate email and username
    foreach ($xml->user as $user) {
        if ($user['uid'] != $uid) {
            if ($user->email == $_POST['update_email']) {
                $error[] = "Email already exists <br>";
            }
            if ($user->username == $_POST['update_username']) {
                $error[] = "Username already exists <br>";
                break;
            }
        }
    }

    // Update user data if no duplicates found
    if (!in_array("Email already exists", $error) && !in_array("Username already exists", $error)) {
        foreach ($xml->user as $user) {
            if ($user['uid'] == $uid) {
                // Update the user's data
                $user->firstName = $_POST['update_fName'];
                $user->lastName = $_POST['update_lName'];
                $user->email = $_POST['update_email'];
                $user->role = $_POST['update_role'];
                $user->password = $_POST['update_password'];

                // Save the updated XML data
                if ($xml->asXML('xml/users.xml')) {
                    $error[] = "User data updated successfully <br>";
                } else {
                    $error[] = "Error updating user data <br>";
                }

                // Stop searching for users
                break;
            }
        }
    }
    else{
    }
}

if(isset($_POST['submit_c'])){
    $fname = $_POST['create_fName'];
    $lname = $_POST['create_lName'];
    $email = $_POST['create_email'];
    $username = $_POST['create_username'];
    $password = $_POST['create_password'];
    $role = $_POST['create_role'];

    // Check if email and username are not taken
    foreach ($xml->user as $user) {
        if ($user->email == $email) {
            $error[] = "Email is already taken";
        }
        if ($user->username == $username) {
            $error[] = "Username is already taken";
        }
    }

    if (!in_array("Email is already taken", $error) && !in_array("Username is already taken", $error)) {
        // Generate new unique user ID
        $maxUid = 0;
        foreach ($xml->user as $user) {
            $uid = (int)$user['uid'];
            if ($uid > $maxUid) {
                $maxUid = $uid;
            }
        }
        $newUid = $maxUid + 1;

        // Create new user element
        $newUser = $xml->addChild('user');
        $newUser->addAttribute('uid', $newUid);
        $newUser->addChild('firstName', $fname);
        $newUser->addChild('lastName', $lname);
        $newUser->addChild('username', $username);
        $newUser->addChild('email', $email);
        $newUser->addChild('role', $role);
        $newUser->addChild('password', $password);
        $newUser->addChild('dateCreated', $current_date);

        // Save the updated XML data
        if ($xml->asXML('xml/users.xml')) {
            $error[] = "New user added successfully";
        } else {
            $error[] = "Error adding new user";
        }
    }   

}

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
        unset($userToDelete[0]);

        // Save the updated XML data
        if ($xml->asXML('xml/users.xml')) {
            $error[] = "User deleted successfully <br>";
        } else {
            $error[] = "Error deleting user <br>";
        }
    }
}

// Define the default filter option and date range
$_SESSION['filter'] = "all";
$_SESSION['start_date'] = null;
$_SESSION['end_date'] = null;

$filter = "all";
$start = null;
$end = null;

// Check if a filter option or custom date range was selected and update the $filter, $start and $end variables
if (isset($_POST["day"])) {
    $filter = "day";
    $_SESSION['filter'] = "day";
} elseif (isset($_POST["week"])) {
    $filter = "week";
    $_SESSION['filter'] = "week";
} elseif (isset($_POST["month"])) {
    $filter = "month";
    $_SESSION['filter'] = "month";
} elseif (isset($_POST["year"])) {
    $filter = "year";
    $_SESSION['filter'] = "year";
} elseif (isset($_POST["custom"])) {
    $start = new DateTime($_POST["start_date"]);
    $end = new DateTime($_POST["end_date"]);
    $filter = "custom";
    $_SESSION['filter'] = "custom";
    $_SESSION['start_date'] = new DateTime($_POST["start_date"]);
    $_SESSION['end_date'] = new DateTime($_POST["end_date"]);
}

// Filter the users based on the selected option or custom date range
$users = array();
foreach ($xml->user as $user) {
    $dateCreated = new DateTime($user->dateCreated);
    switch ($filter) {
        case "day":
            if ($dateCreated->format("Ymd") == (new DateTime())->format("Ymd")) {
                $users[] = $user;
            }
            break;
        case "week":
            $startOfWeek = (new DateTime())->modify('this week')->format("Ymd");
            $endOfWeek = (new DateTime())->modify('this week +6 days')->format("Ymd");
            if ($dateCreated->format("Ymd") >= $startOfWeek && $dateCreated->format("Ymd") <= $endOfWeek) {
                $users[] = $user;
            }
            break;
        case "month":
            if ($dateCreated->format("Ym") == (new DateTime())->format("Ym")) {
                $users[] = $user;
            }
            break;
        case "year":
            if ($dateCreated->format("Y") == (new DateTime())->format("Y")) {
                $users[] = $user;
            }
            break;
        case "custom":
            if ($dateCreated >= $start && $dateCreated <= $end) {
                $users[] = $user;
            }
            break;
        default:
            $users[] = $user;
            break;
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

        </div>

        <!--Notification-->

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
    <div>
    <div class="container-fluid">
    <div class="row min-vh-100" id="center_body">
        <div class="col-3">
            <nav class="sidebar bg-white pb-2 h-100">
                <div>
                    <div class="list-group list-group-flush mx-3 mt-4">
                        <a href="#" class="list-group-item list-group-item-action py-2 ripple disabled text-dark" aria-current="true">
                            <i class="fas fa-tachometer-alt fa-fw me-3"></i>
                            <h5>Admin Dashboard</h5>
                        </a>
                        <a href="a_manage_users.php" class="list-group-item list-group-item-action py-2 ripple bg-danger text-white"><span>Manage Users</span></a>
                        <a href="a_manage_lessons.php" class="list-group-item list-group-item-action py-2 ripple"><span>Manage Lessons</span></a>
                        <a href="a_manage_assessments.php" class="list-group-item list-group-item-action py-2 ripple"><span>Manage Assessments</span></a>
                        <a href="a_manage_quiz.php" class="list-group-item list-group-item-action py-2 ripple"><span>Manage Quizzes</span></a>
                        <a href="a_activity_logs.php" class="list-group-item list-group-item-action py-2 ripple"><span>Activity Logs</span></a>
                    </div>
                </div>
            </nav>
        </div>
        <div class="col">
            <div class="container mt-3">
                <h5>Manage Users</h5>
            </div>
            <div class="container bg-white mt-4 py-2 rounded">
                <div class="row">
                    <div class="col py-2">
                        <div class="text-end">                      
                            <div class="btn-group dropdown pull-left">
                                <button type="button" class="btn dropdown-toggle border" data-bs-toggle="dropdown" aria-expanded="false">
                                    Sort by:
                                </button>
                                <form method="post">
                                    <ul class="dropdown-menu">
                                        <div class="form-floating mb-2">
                                            <input type="text" name="start_date" id="start_date" placeholder="Start" class="form-control border">
                                            <label for="start_date">Start:</label>
                                        </div>
                                        <div class="form-floating mb-2">
                                            <input type="text" name="end_date" id="end_date" placeholder="End" class="form-control border">
                                            <label for="end_date">End:</label>
                                        </div>
                                        <li><button type="submit" class="dropdown-item mb-3" href="#"
                                        value="custom" name="custom">Set Date</button></li>
                                        <li><button type="submit" class="dropdown-item" href="#"
                                        value="day" name="day">Today</button></li>
                                        <li><button type="submit" class="dropdown-item" href="#"
                                        value="week" name="week">This Week</button></li>
                                        <li><button type="submit" class="dropdown-item" href="#"
                                        value="month" name="month">This Month</button></li>
                                        <li><button type="submit" class="dropdown-item" href="#"
                                        value="year" name="year">This Year</button></li>
                                        
                                    </ul>
                                </form>
                            </div>
                            <button type="button" class="btn border pull-left mx-2">
                            <a href="printUsers.php" target="_blank" class="text-decoration-none text-black">Print Users</a>
                            </button>
                            <button type="button" class="btn border pull-left mx-2">
                            <a href="printUsersAchievments.php" target="_blank" class="text-decoration-none text-black">Print Users Achievments</a>
                            </button>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#create">
                                Create
                            </button>
                            <button type="button" id="editBtn" class="btn border" data-bs-toggle="modal" data-bs-target="#update" disabled>
                                Update
                            </button>
                            <button type="button" id="deleteBtn"class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete" disabled>
                                Delete
                            </button>
                        </div>
                        <div class="bg-white mt-4 py-2 pt-0 pl-0 d-flex rounded overflow-auto w-100" style="height:700px;">
                            <table class="table table-hover" id="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">First Name</th>
                                            <th scope="col">Last Name</th>
                                            <th scope="col">Username</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Role</th>
                                            <th scope="col">Date Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user) : ?>
                                            <tr style="transform: rotate(0);" class="" data-row="<?php echo $user["uid"] ?>">
                                                <th scope="row" id="uid"><?php echo $user["uid"] ?></th>
                                                <td id="firstName"><?php echo $user->firstName ?></td>
                                                <td id="lastName"><?php echo $user->lastName ?></td>
                                                <td id="username"><?php echo $user->username ?></td>
                                                <td id="email"><?php echo $user->email ?></td>
                                                <td id="role"><?php echo $user->role ?></td>
                                                <td id="date"><?php echo $user->dateCreated ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                            </table>
                        </div>
                        <div class="col text-end">
                        <div class="text-start h5 text-danger">
                                <?php

                                if (isset($error)) {
                                    foreach ($error as $error) {
                                        echo $error;
                                    }
                                }
                                ?>
                            </div>
                            <!-- Modal -->          
                            <!-- create modal -->
                            <div class="modal fade bd-example-modal-lg" id="create" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="staticBackdropLabel">Create</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="msodal-body text-center p-3">
                                            <form action="" method="post">
                                                <div class="row">
                                                    <div class="form-group d-flex flex-column w-50">
                                                        <div class="form-floating">
                                                            <input type="text" name="create_fName" id="create_fName" placeholder="Name" class="form-control border" required>
                                                            <label for="create_fName">First Name:</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group d-flex flex-column w-50">
                                                        <div class="form-floating">
                                                            <input type="text" name="create_lName" id="create_lName" placeholder="Name" class="form-control border" required>
                                                            <label for="create_lName">Last Name:</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row my-2">
                                                    <div class="form-group d-flex flex-column w-100">
                                                        <div class="form-floating">
                                                            <input type="text" name="create_username" class="form-control border" id="create_username" placeholder="Email">
                                                            <label>Username:</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row my-2">
                                                    <div class="form-group d-flex flex-column w-100">
                                                        <div class="form-floating">
                                                            <input type="email" name="create_email" class="form-control border" id="create_email" placeholder="Email">
                                                            <label>Email:</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row justify-content-center my-2">
                                                    <div class="form-group d-flex flex-column w-50">
                                                        <div class="form-floating">
                                                            <input type="password" name="create_password" class="form-control" id="create_password" placeholder="Password">
                                                            <label>Password:</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group d-flex flex-column w-50">
                                                        <div class="form-floating">
                                                            <select class="form-select" id="floatingSelectGrid" name="create_role" class="role" aria-label="Floating label select example">
                                                                <option value="" disabled selected>--Select Role--</option>
                                                                <option value="student">Student</option>
                                                                <option value="admin">Admin</option>
                                                            </select>
                                                            <label for="floatingSelectGrid">Role</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn border" data-bs-dismiss="modal">Cancel</button>
                                                    <input type="submit" name="submit_c" value="Create" class="btn btn-success">
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
                                            <h5 class="modal-title" id="staticBackdropLabel"><label id="userId"></label></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="msodal-body text-center p-3">
                                            <form action="" method="post">
                                                <div class="row my-2">
                                                    <div class="form-group d-flex flex-column w-100">
                                                        <div class="form-floating">
                                                            <input type="hidden" name="update_uid" class="form-control border" id="update_uid" placeholder="UID">
                                                            <label>UID:</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group d-flex flex-column w-50">
                                                        <div class="form-floating">
                                                            <input type="text" name="update_fName" id="update_fName" placeholder="Name" class="form-control border" required>
                                                            <label for="update_fName">First Name:</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group d-flex flex-column w-50">
                                                        <div class="form-floating">
                                                            <input type="text" name="update_lName" id="update_lName" placeholder="Name" class="form-control border" required>
                                                            <label for="update_lName">Last Name:</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row my-2">
                                                    <div class="form-group d-flex flex-column w-100">
                                                        <div class="form-floating">
                                                            <input type="text" name="update_username" class="form-control border" id="update_username" placeholder="Username"  required>
                                                            <label>Username:</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row my-2">
                                                    <div class="form-group d-flex flex-column w-100">
                                                        <div class="form-floating">
                                                            <input type="email" name="update_email" class="form-control border" id="update_email" placeholder="Email"  required>
                                                            <label>Email:</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row justify-content-center my-2">
                                                    <div class="form-group d-flex flex-column w-50">
                                                        <div class="form-floating">
                                                            <input type="password" name="update_password" class="form-control" id="update_password" placeholder="Password" required>
                                                            <label>Password:</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group d-flex flex-column w-50">
                                                        <div class="form-floating">
                                                            <select class="form-select" id="floatingSelectGrid" name="update_role" class="role" aria-label="Floating label select example" required>
                                                                <option value="" disabled selected id="update_Role"></option>
                                                                <option value="student">Student</option>
                                                                <option value="admin">Admin</option>
                                                            </select>
                                                            <label for="floatingSelectGrid">Role</label>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="ud">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn border" data-bs-dismiss="modal">Cancel</button>
                                                    <input type="submit" name="submit_u" value="Update" class="btn btn-warning">
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
                                            <p class="h5">Are you sure you want to delete this user?</p><br>
                                            <p class="h5 text-danger" id="deleteTxt"></p>
                                            <div class="modal-footer my-3">
                                                <button type="button" class="btn border" data-bs-dismiss="modal">Close</button>
                                                <form action="" method="post">
                                                <div class="row my-2">
                                                    <input type="hidden" name="delete_uid" class="form-control border" id="delete_uid">
                                                    <input type="submit" name="submit_d" value="Delete" class="btn btn-danger">
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            
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