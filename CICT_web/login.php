<?php
session_start();

$xml = simplexml_load_file("xml/users.xml");
$xmlActivityLog = simplexml_load_file("xml/activity_logs.xml");

if (isset($_POST['submit'])) {
    $username = $_POST["email"];
    $password = $_POST["password"];

    foreach ($xml->user as $user) {
        if (($user->username == $username || $user->email == $username) && $user->password == $password) { 
            $_SESSION["uid"] = (string) $user["uid"];
            $_SESSION["username"] = (string) $user->username;
            $role = $user->role;
            if($user = $_SESSION["uid"]){
                
                if($role == "admin"){
                    $logs = simplexml_load_file('xml/activity_logs.xml');

                    $current_datetime = date('Y-m-d H:i:s');
                    $current_date = date('Y-m-d', strtotime($current_datetime));
                    $current_time = date('H:i:s', strtotime($current_datetime));
                    
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
                    $activityLog->addChild("action", "Login");
                    $activityLog->addChild("date", $current_date);
                    $activityLog->addChild("time", $current_time);

                    $logs->asXML("xml/activity_logs.xml");

                    header("Location: a_manage_users.php");
                    exit();
                }

                elseif($role == "student"){
                    $logs = simplexml_load_file('xml/activity_logs.xml');

                    $current_datetime = date('Y-m-d H:i:s');
                    $current_date = date('Y-m-d', strtotime($current_datetime));
                    $current_time = date('H:i:s', strtotime($current_datetime));
                    
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
                    $activityLog->addChild("action", "login");
                    $activityLog->addChild("date", $current_date);
                    $activityLog->addChild("time", $current_time);

                    $logs->asXML("xml/activity_logs.xml");

                    header("Location: home.php");
                    exit();
                }
                else{
                    $error[] = "No roles assigned to this user.";
                    break;
                }
            }
        }else{
            $error[] = "Incorrect login credential!";
        }
    }
}


?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="styles/style.css">
</head>

<body onload="active_login()">
    <div class="container">
        <div class="header">
            <nav>
                <ul class="nav_links">
                    <li><a href="i_about.php">About us</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Sign up</a></li>
                </ul>
            </nav>
        </div>

        <div class="login">
            <h1>Login</h1>
            <form action="" method="post">
                <div class="error_pop">
                    <?php
                    if (isset($error)) {
                        foreach ($error as $error) {
                            echo $error;
                        }
                    }
                    ?>
                </div>
                <br>
                <div class="txt_field">
                    <label>Email / Username:</label>
                    <input type="text" name="email" required>
                </div>
                <div class="txt_field">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <input type="submit" name="submit" value="Login">
                <div class="need_account">Need an account? <a href="signup.php">SIGN UP</a></div>
            </form>
        </div>
    </div>
    <script src="scripts/script.js"></script>
</body>

</html>