<?php
session_start();

$empty=false;
$login = true;

if (isset($_SESSION['loggedIn'])) {
  header("location:./index.php");
  exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  include './partials/dbconnect.php';
  $username = $_POST["username"];
  $password = $_POST["password"];
  $userCheck = "SELECT * from userdetails WHERE `username` = '$username'";
  $resultOfUserCheck = mysqli_query($con, $userCheck);
  $numOfUser = mysqli_num_rows($resultOfUserCheck);
  if ($username == "" || $password == "") {
    $empty = true;
  } else if ($numOfUser == 1) {
    while ($row = mysqli_fetch_assoc($resultOfUserCheck)) {
      if (password_verify($password, $row['password'])) {
        $login = true;
        $_SESSION['loggedIn'] = true;

        $fetchLoggedInUserKoFollowFollowing = "SELECT * from userfollowfollowing WHERE `id`= '" . $row['id'] . "'";
        $resultOfFetchLoggedInUserKoFollowFollowing = mysqli_query($con, $fetchLoggedInUserKoFollowFollowing);
        $detailOfLoggedInUserKoFollowFollowing = mysqli_fetch_assoc($resultOfFetchLoggedInUserKoFollowFollowing);

        $loggedInUserKoFollowing = count(unserialize($detailOfLoggedInUserKoFollowFollowing['following']));
        $loggedInUserKoFollow = count(unserialize($detailOfLoggedInUserKoFollowFollowing['follow']));

        $_SESSION['id'] = $row["id"];
        $_SESSION['name'] = $row["name"];
        $_SESSION['username'] = $row["username"];
        $_SESSION['useremail'] = $row["useremail"];
        $_SESSION['usergender'] = $row["usergender"];
        $_SESSION['userposition'] = $row["userposition"];
        $_SESSION['phone_no'] = $row["phone_no"];
        $_SESSION['securitycode'] = $row["securitycode"];
        $_SESSION['numberOfFollowing'] = $loggedInUserKoFollowing;
        $_SESSION['numberOfFollow'] = $loggedInUserKoFollow;


        $usergenderforpp = strtolower($_SESSION['usergender']);
        if ($username != "admin") {
          $_SESSION['profilePicLocation'] =  file_exists("./img/profilepictures/$username.jpg") ? "./img/profilepictures/$username.jpg" : "./img/$usergenderforpp.png";
        } else {
          $_SESSION['profilePicLocation'] = "./img/logo.png";
        }


        if ($username == "admin") {
          header("location:admin.php");
          exit();
        } else {
          header("location:./index.php");
        }
      } else {
        $login = false;
      }
    }
  } else {
    $login = false;
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
      if (!$login) {
        echo
        "<div class='alert alert-danger' role='alert'>
        <strong>Please</strong> Enter Correct Credentials.
      </div>";
      }
      ?>
      <form action="#" method="post">
        <div class="formcontent">
          <label>Username</label>
          <input type="text" name="username" placeholder="ENTER YOUR USERNAME" />
        </div>
        <div class="formcontent">
          <label>Password</label>
          <input type="password" name="password" placeholder="ENTER YOUR PASSWORD" />
        </div>
        <div class="formcontent formcontent-btn d-flex">
          <button class="btn" type="submit" name="submit">Submit</button>
          <div class="sign-forget d-flex">
            <span><a href="signup.php">Redirect to Sign Up</a></span>
            <span><a href="./partials/forgetpassword.php">Forget Password?</a></span>
          </div>
        </div>
      </form>
    </div>
  </section>

  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>
</body>

</html>