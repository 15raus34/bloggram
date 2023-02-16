<?php
session_start();
if (isset($_SESSION['loggedIn'])) {
  header("location:./index.php");
  exit();
}
$exist = false;
$didMatch = false;
$empty = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  include 'partials/dbconnect.php';
  $name = $_POST["name"];
  $useremail = $_POST["useremail"];
  $usergender = $_POST["usergender"];
  $username = $_POST["username"];
  $password = $_POST["password"];
  $repassword = $_POST["repassword"];
  $userNameCheck = "SELECT * from `userdetails` WHERE `username` = '$username'";
  $resultOfUserNameCheck = mysqli_query($con, $userNameCheck);
  $numOfUserName = mysqli_num_rows($resultOfUserNameCheck);
  if ($numOfUserName > 0) {
    $exist = true;
  } else {
    if ($name == "" || $useremail == "" || $username == "" || $password == "") {
      $empty = true;
    } else if ($password != $repassword) {
      $didMatch = true;
    } else if (($password != "") && ($password == $repassword) && ($exist == false)) {
      $passwordHash = password_hash($password, PASSWORD_DEFAULT);
      $sql = "INSERT INTO `userdetails` (`name`, `useremail`, `usergender`, `userposition`, `phone_no`, `username`, `password`,`securitycode`, `createdtime`) VALUES ('$name', '$useremail', '$usergender', 'Bloggram User', NULL, '$username', '$passwordHash', NULL, current_timestamp())";
      $result1 = mysqli_query($con, $sql);

      $serializedArray = serialize(array());
      $sql = "INSERT INTO `userfollowfollowing` (`username`, `follow`, `following`) VALUES ('$username', '$serializedArray', '$serializedArray')";
      $result2 = mysqli_query($con, $sql);
      if ($result1 && $result2) {
        header("location:./login.php");
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SignUp</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

  <link rel="stylesheet" href="css/utils.css" />
  <link rel="stylesheet" href="css/signup-login.css" />
</head>

<body>
  <section class="container d-flex">
    <div class="welcome-card">
      <img src="./img/logo.png" alt="" />
    </div>
    <div class="form-card cardup">
      <?php
      if ($empty) {
        echo
        "<div class='alert alert-danger' role='alert'>
        <strong>Empty</strong> Fields.
      </div>";
      }
      ?>
      <?php
      if ($exist) {
        echo
        "<div class='alert alert-danger' role='alert'>
        <strong>Username</strong> Already Taken.
      </div>";
      }
      ?>
      <?php
      if ($didMatch) {
        echo
        "<div class='alert alert-danger' role='alert'>
        <strong>Passwords</strong> Didn't Match.
      </div>";
      }
      ?>
      <form method="post">
        <div class="formcontent">
          <label>Name</label>
          <input type="text" name="name" placeholder="ENTER YOUR NAME" />
        </div>
        <div class="formcontent">
          <label>Email</label>
          <input type="email" name="useremail" placeholder="ENTER YOUR EMAIL" />
        </div>
        <div class="formcontent">
          <label>Gender</label>
          <span><input type="radio" name="usergender" value="Male" checked />Male</span>
          <span><input type="radio" name="usergender" value="Female" />Female</span>
        </div>
        <div class="formcontent">
          <label>Username</label>
          <input type="text" name="username" placeholder="ENTER YOUR USERNAME" />
        </div>
        <div class="formcontent">
          <label>Password</label>
          <input type="password" name="password" placeholder="ENTER YOUR PASSWORD" />
        </div>
        <div class="formcontent">
          <label>Repassword</label>
          <input type="password" name="repassword" placeholder="ENTER YOUR PASSWORD AGAIN" />
        </div>
        <div class="formcontent formcontent-btn">
          <button class="btn" type="submit" name="submit">Submit</button>
          <span><a href="login.php">Redirect to Login</a></span>
        </div>
      </form>
    </div>
  </section>

  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

</body>

</html>