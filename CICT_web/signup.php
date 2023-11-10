<?php
session_start();
$xml = simplexml_load_file("xml/users.xml");

$highestID = 0;

foreach ($xml->user as $user) {
    $currentID = (int) $user->attributes()['uid'];
    if ($currentID > $highestID) {
        $highestID = $currentID;
    }  
}
$newID = $highestID + 1;
if(isset($_POST['submit'])){

    $username = $_POST["username"];
    $firstName = $_POST["Fname"];
    $lastName = $_POST["Lname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (!preg_match("/^[A-Za-z0-9]+$/", $firstName)) {
        // Username does not match the pattern, redirect to signup page
        $error[] = "First Name should only contain alphabets and numbers!<br>";
        
    }
    if (!preg_match("/^[A-Za-z0-9]+$/", $lastName)) {
        // Username does not match the pattern, redirect to signup page
        $error[] = "Last Name should only contain alphabets and numbers!<br>";
    }

    if (!preg_match("/^[A-Za-z0-9]+$/", $username)) {
        // Username does not match the pattern, redirect to signup page
        $error[] = "Username should only contain alphabets and numbers!<br>";
    }

    if ($password != $confirm_password) {
    // Passwords do not match, redirect to signup page
        $error[] = "Password doesn't match<br>";
    }

    $xml = simplexml_load_file("xml/users.xml");

    foreach ($xml->user as $user) {
        if ($user->username == $username) {
            // User already exists, redirect to signup page
            $error[] = "Username already exists<br>";
        }  
    }

    foreach ($xml->user as $user) {
        if ($user->email == $email) {
            // Email already exists, redirect to signup page
            $error[] = "Email already exists<br>";
        }  
    }
    $xmlActivityLog = simplexml_load_file("xml/activity_logs.xml");
    if (empty($error)){
        $user = $xml->addChild("user");

        $current_datetime = date('Y-m-d H:i:s');
        $current_date = date('Y-m-d', strtotime($current_datetime));

        $user->addAttribute("uid", $newID);
        $user->addChild("firstName", $firstName);
        $user->addChild("lastName", $lastName);
        $user->addChild("username", $username);
        $user->addChild("email", $email);
        $user->addChild("password", $password);
        $user->addChild("role", "student");
        $user->addChild("profilePicture","uploads/default.png");
        $user->addChild("dateCreated", $current_date);
        $user->addChild("quiz")->addAttribute("qNo", "1");
        $user->addChild("quiz")->addAttribute("qNo", "2");
        $user->addChild("quiz")->addAttribute("qNo", "3");
        $user->addChild("quiz")->addAttribute("qNo", "4");
        $user->addChild("quiz")->addAttribute("qNo", "5");
        $user->addChild("quiz")->addAttribute("qNo", "6");
        $user->addChild("quiz")->addAttribute("qNo", "7");
        $user->addChild("quiz")->addAttribute("qNo", "8");
        $user->addChild("quiz")->addAttribute("qNo", "9");
        $user->addChild("quiz")->addAttribute("qNo", "10");
        $user->addChild("quiz")->addAttribute("qNo", "11");
        $user->addChild("assess")->addAttribute("aNo", "1");
        $user->addChild("assess")->addAttribute("aNo", "2");
        $xml->asXML("xml/users.xml");

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
        header("Location: login.php");
        $error[] = "added";

        $_SESSION["isRegistered"] = "true";
    }

};

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Sign up</title>
        <link rel="stylesheet" href="styles/style.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>
    </head>
    </head>
    <body onload="active_signup()">
</div>
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
            <div class="signup">
                <h1>Sign Up</h1>
                <form action="" method="post">
                <div class="error_pop">
                    <?php 

                        if(isset($error)){
                            foreach($error as $error){
                                echo $error;
                            }
                        }

                    ?>
                </div>
                    <div class="names">
                        <div class="nametxt_field">
                            <label >First Name:</label>
                            <input type="text" name="Fname" required>
                        </div>
                        <div class="nametxt_field">
                            <label >Last Name:</label>
                            <input type="text" name="Lname" required>
                        </div>
                    </div>
                    <div class="txt_field">
                        <label >Username:</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="txt_field">
                        <label >Email:</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="txt_field">
                        <label >Password:</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="txt_field">
                        <label >Confirm Password:</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <div class="remember">
                        <input type="checkbox" required>I Agree to Terms of Service
                    </div>
                    <div class="remember">
                        <input type="checkbox" required>I Agree to Data Privacy Policy
                    </div>
                    <input type="submit" name="submit" value="Sign Up" data-modal-target="staticModal" data-modal-toggle="staticModal">
                    <div class="have_account">Already have an account? <a href="login.php">LOGIN</a></div>
                </form>
            </div>
            
        </div>
        <script src="scripts/script.js"></script>
    </body>
</html>