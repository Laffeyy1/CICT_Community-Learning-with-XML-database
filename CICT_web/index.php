<?php
session_start();

header("Location:login.php");
exit();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="styles/style.css">
    </head>
    <body onload="login()">
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
            
        </div>
        <script src="scripts/script.js"></script>
    </body>
</html>