<?php
session_start();

$xml = simplexml_load_file("xml/activity_logs.xml");
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
$logs = array();
foreach ($xml->activityLog as $log) {
    $dateCreated = new DateTime($log->date);
    switch ($filter) {
        case "day":
            if ($dateCreated->format("Ymd") == (new DateTime())->format("Ymd")) {
                $logs[] = $log;
            }
            break;
        case "week":
            $startOfWeek = (new DateTime())->modify('this week')->format("Ymd");
            $endOfWeek = (new DateTime())->modify('this week +6 days')->format("Ymd");
            if ($dateCreated->format("Ymd") >= $startOfWeek && $dateCreated->format("Ymd") <= $endOfWeek) {
                $logs[] = $log;
            }
            break;
        case "month":
            if ($dateCreated->format("Ym") == (new DateTime())->format("Ym")) {
                $logs[] = $log;
            }
            break;
        case "year":
            if ($dateCreated->format("Y") == (new DateTime())->format("Y")) {
                $logs[] = $log;
            }
            break;
        case "custom":
            if ($dateCreated >= $start && $dateCreated <= $end) {
                $logs[] = $log;
            }
            break;
        default:
            $logs[] = $log;
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
            <ul class="navbar-nav">
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
                    <div class="list-group list-group-flush mx-3 mt-4" >
                        <a href="#" class="list-group-item list-group-item-action py-2 ripple disabled text-dark" aria-current="true">
                            <i class="fas fa-tachometer-alt fa-fw me-3"></i>
                            <h5>Admin Dashboard</h5>
                        </a>
                        <a href="a_manage_users.php" class="list-group-item list-group-item-action py-2 ripple"><span>Manage Users</span></a>
                        <a href="a_manage_lessons.php" class="list-group-item list-group-item-action py-2 ripple"><span>Manage Lessons</span></a>
                        <a href="a_manage_assessments.php" class="list-group-item list-group-item-action py-2 ripple"><span>Manage Assessments</span></a>
                        <a href="a_manage_quiz.php" class="list-group-item list-group-item-action py-2 ripple"><span>Manage Quizzes</span></a>
                        <a href="a_activity_logs.php" class="list-group-item list-group-item-action py-2 ripple bg-danger text-white"><span>Activity Logs</span></a>
                    </div>
                </div>
            </nav>
        </div>
        <div class="col">
            <div class="container mt-3">
                <h5 class="mt-5">Activity Logs</h5>
                <div class="container bg-white mt-4 py-2 rounded">
                    <div class="row">
                        <div class="col py-2">
                        <button type="button" class="btn border pull-left mx-2">
                            <a href="printActivityLogs.php" target="_blank" class="text-decoration-none text-black">Print</a>
                            </button>
                            
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
                        </div>
                        <div class="rounded overflow-auto w-100" style="height:700px;"> 
                        <table class="table table-hover text-center " id="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">User</th>
                                    <th scope="col">Action</th>
                                    <th scope="col">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log) : ?>
                                    <tr style="transform: rotate(0);" class="">
                                        <td><?php echo $log["id"]?></td>
                                        <td><?php echo $log->username?></td>
                                        <th><?php echo $log->action?></th>
                                        <td style="width: 25%"><?php echo $log->date?> | <?php echo $log->time?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

</body>

</html>

