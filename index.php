<?php
session_start();

if (!$_SESSION['loggedIn']) {
  header("location:./login.php");
}
include './partials/dbconnect.php';
$userid = $_SESSION['id'];
$name = $_SESSION['name'];
$username = $_SESSION['username'];
$userposition = $_SESSION['userposition'];
$usergender = $_SESSION['usergender'];

if(isset($_SESSION['tempID'])){
  echo "<script>location.hash = '#".$_SESSION['tempID']."';</script>";
  unset($_SESSION['tempID']);
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {

  //To Like The Post via GET Request [INTERACTION]
  if (isset($_GET['likePostID'])) {
    $likePostKoId = $_GET['likePostID'];
    $fetchLikePost = "SELECT * from userposts WHERE `id`= '$likePostKoId'";
    $resultOfFetchLikePost = mysqli_query($con, $fetchLikePost);
    $detailOfLikePost = mysqli_fetch_assoc($resultOfFetchLikePost);

    $unserializedLikePost = unserialize($detailOfLikePost['likes']);
    array_push($unserializedLikePost, $userid);
    $serializedLikePost = serialize($unserializedLikePost);

    $updateLikeInPost = "UPDATE `userposts` SET `likes` = '$serializedLikePost' WHERE `userposts`.`id` = '$likePostKoId'";
    $resultOfUpdateLikeInPost = mysqli_query($con, $updateLikeInPost);
    if ($resultOfUpdateLikeInPost) {
      $_SESSION['tempID'] = "post".$likePostKoId;
      header("location:http://localhost/blogphp/index.php");
    }
  }

  //To Dislike The Post via GET Request [INTERACTION]
  if (isset($_GET['disLikePostID'])) {
    $disLikePostKoId = $_GET['disLikePostID'];
    $fetchDisLikePost = "SELECT * from userposts WHERE `id`= '$disLikePostKoId'";
    $resultOfFetchDisLikePost = mysqli_query($con, $fetchDisLikePost);
    $detailOfDisLikePost = mysqli_fetch_assoc($resultOfFetchDisLikePost);

    $unserializedDisLikePost = unserialize($detailOfDisLikePost['likes']);
    $unserializedDisLikePost = array_diff($unserializedDisLikePost, array($userid));
    $serializedDisLikePost = serialize($unserializedDisLikePost);

    $updateDisLikeInPost = "UPDATE `userposts` SET `likes` = '$serializedDisLikePost' WHERE `userposts`.`id` = '$disLikePostKoId'";
    $resultOfUpdateDisLikeInPost = mysqli_query($con, $updateDisLikeInPost);
    if ($resultOfUpdateDisLikeInPost) {
      $_SESSION['tempID'] = "post".$disLikePostKoId;
      header("location:http://localhost/blogphp/index.php");
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
  <title>Blog</title>

  <link rel="stylesheet" href="./css/utils.css">
  <link rel="stylesheet" href="./css/navbar.css" />
  <link rel="stylesheet" href="./css/maininterface.css" />
</head>

<body>
  <?php include './partials/navbar.php'; ?>
  <section id="main-interface">
    <div class="sub-interface sub-interface-userdetail">
      <div class="upper-brief-detail cardup">
        <div class="col-color"></div>
        <img src="./img/<?php echo strtolower($usergender) ?>.png" alt="logo" />
        <div class="userdetails d-flex">
          <div class="name-profession d-flex">
            <h2><?php echo $name ?></h2>
            <p><?php echo $userposition ?></p>
          </div>
          <hr />
          <div class="userfollow d-flex">
            <p>Following</p>
            <span>10</span>
            <br />
            <p>Followers</p>
            <span>10</span>
          </div>
        </div>
      </div>
      <div class="lower-suggestion cardup">
        <h3>Suggestions</h3>
        <div class="suggest-user">
          <?php
          $fetchUserSuggesion = "SELECT * from userdetails WHERE `username`!= '$username'";
          $resultOfFetchUserSuggesion = mysqli_query($con, $fetchUserSuggesion);
          $numOfFetchUserSuggesion = mysqli_num_rows($resultOfFetchUserSuggesion);

          if ($numOfFetchUserSuggesion > 0) {
            while ($detailOfFetchUserSuggesion = mysqli_fetch_assoc($resultOfFetchUserSuggesion)) {
              $specificSuggestUserName = $detailOfFetchUserSuggesion['name'];
              $specificSuggestUserPosition = $detailOfFetchUserSuggesion['userposition'];
              $specificSuggestUserGender = $detailOfFetchUserSuggesion['usergender'];

              echo "<div class='suggest-user-1 d-flex'>
                  <img src='./img/" . strtolower($specificSuggestUserGender) . ".png' alt='suser' />
                  <div class='minidetail'>
                  <a href='#'>$specificSuggestUserName</a>
                  <p>$specificSuggestUserPosition</p>
                </div>
              </div>
              <hr />";
            }
          }

          ?>
        </div>
      </div>
    </div>
    <!-- ----------------------------------------------------------------- -->
    <div class="sub-interface sub-interface-blog">
      <?php
      $fetchPost = "SELECT * from userposts WHERE `username`!= '$username'";
      $resultOfFetchPost = mysqli_query($con, $fetchPost);
      $numOfPost = mysqli_num_rows($resultOfFetchPost);

      if ($numOfPost > 0) {
        while ($row = mysqli_fetch_assoc($resultOfFetchPost)) {
          $specificUserKoName = $row['username'];
          $id = $row['id'];
          $title = $row['title'];
          $description = $row['description'];

          $didILike = in_array($userid, unserialize($row['likes']));
          $likes = count(unserialize($row['likes']));

          $fetchSpecificUser = "SELECT * from userdetails WHERE `username`= '$specificUserKoName'";
          $resultOfFetchSpecificUser = mysqli_query($con, $fetchSpecificUser);
          $detailOfSpecificUser = mysqli_fetch_assoc($resultOfFetchSpecificUser);

          $SpecificUserName = $detailOfSpecificUser["name"];
          $SpecificUserPosition = $detailOfSpecificUser["userposition"];
          $SpecificUserGender = $detailOfSpecificUser["usergender"];
          echo "<div class='userblog cardup' id='post".$id."'>
            <div class='userblog-profile d-flex'>
              <img src='./img/" . strtolower($SpecificUserGender) . ".png' alt='logo' />
              <div class='userblog-profile-nameposition'>
                <h3>$SpecificUserName</h3>
                <h5>$SpecificUserPosition</h5>
              </div>
            </div>
            <div class='userblog-post'>
              <h1>$title</h1>
              <p>$description
              </p>
            </div>
            <hr />
            <form>
              <div class='userblog-interaction d-flex'>";
          echo (!$didILike) ? "
                <a href='http://localhost/blogphp/index.php?likePostID=$id' class='btn userinteract-btn' id='like'>
                  <i class='fa-sharp fa-solid fa-heart'></i>Like &nbsp;<span>$likes</span>
                </a>" : "<a href='http://localhost/blogphp/index.php?disLikePostID=$id' class='btn userinteract-btn' id='disLike'>
                <i class='fa-sharp fa-solid fa-heart'></i>Liked &nbsp;<span>$likes</span>
              </a>";
          echo "
                <a class='btn userinteract-btn' id='follow'>
                  <i class='fa-solid fa-cloud-bolt'></i>Follow
                </a>
              </div>
            </form>
          </div>";
        }
      }
      ?>
    </div>
    <div class="sub-interface sub-interface-news">
      <div class="news-upper cardup">
        <div class="slogan d-flex">
          <img src="./img/logo.png" alt="logo" height="40vh" />
          <p>BLOG GRAM</p>
          <span>we blog to perfection</span>
        </div>
        <hr />
        <div class="signupnow d-flex">
          <a href="dashboard.php"><button class="btn userinteract-btn">Dashboard</button></a>
        </div>
      </div>
      <div class="news-lower cardup">
        <h3>Do You Know?</h3>
        <hr />
        <div class="news-lower-list">
          <div class="newscard">
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nemo
            ipsa mollitia eius modi minima magni?
          </div>
          <div class="newscard">
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nemo
            ipsa mollitia eius modi minima magni?
          </div>
          <div class="newscard">
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nemo
            ipsa mollitia eius
          </div>
          <div class="newscard">
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nemo
            ipsa mollitia eius modi minima magni?
          </div>
          <div class="newscard">
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nemo
            ipsa mollitia eius modi minima magni?
          </div>
        </div>
      </div>
    </div>
  </section>
  <script src="https://kit.fontawesome.com/4187f8db55.js" crossorigin="anonymous"></script>
</body>

</html>