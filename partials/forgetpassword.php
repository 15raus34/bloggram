<?php
session_start();
if (isset($_SESSION['loggedIn'])) {
    header("location:./index.php");
    exit();
}
$exist = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'dbconnect.php';
    $username = $_POST["username"];
    $securitycode = $_POST["securitycode"];
    $newpassword = $_POST["newpassword"];
    $renewpassword = $_POST["renewpassword"];

    $userNameCheck = "SELECT * from `userdetails` WHERE `username` = '$username'";
    $resultOfUserNameCheck = mysqli_query($con, $userNameCheck);
    $numOfUserName = mysqli_num_rows($resultOfUserNameCheck);
    if ($numOfUserName == 1) {
        $exist = true;
        $row = mysqli_fetch_assoc($resultOfUserNameCheck);
        if (($newpassword != "") && ($newpassword == $renewpassword) && (password_verify($securitycode, $row['securitycode'])) && ($exist == true)) {
            $newPasswordHash = password_hash($newpassword, PASSWORD_DEFAULT);
            $sql = "UPDATE `userdetails` SET `password` = '$newPasswordHash' WHERE `userdetails`.`username` = '$username'";
            $result = mysqli_query($con, $sql);
            if ($result) {
                header("location:../login.php");
            }
        }
    } else {
        $exist = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LogIn</title>

    <link rel="stylesheet" href="../css/utils.css" />
    <link rel="stylesheet" href="../css/signup-login.css" />
</head>

<body>
    <section class="container d-flex">
        <div class="welcome-card">
            <img src="../img/logo.png" alt="" />
        </div>
        <div class="form-card cardup">
            <form method="post">
                <div class="formcontent">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="ENTER YOUR USERNAME" />
                </div>
                <div class="formcontent">
                    <label>Security Code</label>
                    <input type="password" name="securitycode" placeholder="ENTER YOUR SECURITY CODE" />
                </div>
                <div class="formcontent">
                    <label>New Password</label>
                    <input type="password" name="newpassword" placeholder="ENTER YOUR NEW PASSWORD" />
                </div>
                <div class="formcontent">
                    <label>New Password</label>
                    <input type="password" name="renewpassword" placeholder="ENTER YOUR NEW PASSWORD AGAIN" />
                </div>
                <div class="formcontent formcontent-btn d-flex">
                    <button class="btn" type="submit" name="submit">Submit</button>
                    <div class="sign-forget d-flex">
                        <span><a href="../signup.php">Redirect to Sign Up</a></span>
                        <span><a href="../login.php">Redirect to Login</a></span>
                    </div>
                </div>
            </form>
        </div>
    </section>
</body>

</html>