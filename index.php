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

if (isset($_SESSION['tempID'])) {
  echo "<script>location.hash = '#" . $_SESSION['tempID'] . "';</script>";
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
      $_SESSION['tempID'] = "post" . $likePostKoId;
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
      $_SESSION['tempID'] = "post" . $disLikePostKoId;
      header("location:http://localhost/blogphp/index.php");
    }
  }

  //To Follow The User via GET Request [INTERACTION]
  if (isset($_GET['followUserID'])) {
    $userKoId = $_GET['followUserID']; //Other User's ID

    //Updating LoggedIn User's Following List
    $fetchLoggedInUserKoFollowing = "SELECT * from userfollowfollowing WHERE `id`= '$userid'";
    $resultOfFetchLoggedInUserKoFollowing = mysqli_query($con, $fetchLoggedInUserKoFollowing);
    $detailOfLoggedInUserKoFollowing = mysqli_fetch_assoc($resultOfFetchLoggedInUserKoFollowing);

    $unserializedLoggedInUserKoFollowing = unserialize($detailOfLoggedInUserKoFollowing['following']);
    array_push($unserializedLoggedInUserKoFollowing, $userKoId);
    $serializedLoggedInUserKoFollowing = serialize($unserializedLoggedInUserKoFollowing);

    $updateLoggedInUserKoFollowing = "UPDATE `userfollowfollowing` SET `following` = '$serializedLoggedInUserKoFollowing' WHERE `userfollowfollowing`.`id` = '$userid'";
    $resultOfLoggedInUserKoFollowing = mysqli_query($con, $updateLoggedInUserKoFollowing);

    $fetchLoggedInUserKoFollowing = "SELECT * from userfollowfollowing WHERE `id`= '$userid'";
    $resultOfFetchLoggedInUserKoFollowing = mysqli_query($con, $fetchLoggedInUserKoFollowing);
    $detailOfLoggedInUserKoFollowing = mysqli_fetch_assoc($resultOfFetchLoggedInUserKoFollowing);

    $_SESSION['numberOfFollowing'] = count(unserialize($detailOfLoggedInUserKoFollowing['following']));

    //Updating Other User's Followers List
    $fetchOtherUserKoFollowers = "SELECT * from userfollowfollowing WHERE `id`= '$userKoId'";
    $resultOfFetchOtherUserKoFollowers = mysqli_query($con, $fetchOtherUserKoFollowers);
    $detailOfFetchOtherUserKoFollowers = mysqli_fetch_assoc($resultOfFetchOtherUserKoFollowers);

    $unserializedOtherUserKoFollowers = unserialize($detailOfFetchOtherUserKoFollowers['follow']);
    array_push($unserializedOtherUserKoFollowers, $userid);
    $serializedOtherUserKoFollowers = serialize($unserializedOtherUserKoFollowers);

    $updateOtherUserKoFollowers = "UPDATE `userfollowfollowing` SET `follow` = '$serializedOtherUserKoFollowers' WHERE `userfollowfollowing`.`id` = '$userKoId'";
    $resultOfOtherUserKoFollowers = mysqli_query($con, $updateOtherUserKoFollowers);
    if ($resultOfLoggedInUserKoFollowing && $resultOfOtherUserKoFollowers) {
      $_SESSION['tempID'] = "post" . $_GET['postID'];
      header("location:http://localhost/blogphp/index.php");
    }
  }

  //To Unfollow The User via GET Request [INTERACTION]
  if (isset($_GET['unFollowUserID'])) {
    $userKoId = $_GET['unFollowUserID']; //Other User's ID

    //Updating LoggedIn User's Following List
    $fetchLoggedInUserKoFollowing = "SELECT * from userfollowfollowing WHERE `id`= '$userid'";
    $resultOfFetchLoggedInUserKoFollowing = mysqli_query($con, $fetchLoggedInUserKoFollowing);
    $detailOfLoggedInUserKoFollowing = mysqli_fetch_assoc($resultOfFetchLoggedInUserKoFollowing);

    $unserializedLoggedInUserKoFollowing = unserialize($detailOfLoggedInUserKoFollowing['following']);
    $unserializedLoggedInUserKoFollowing = array_diff($unserializedLoggedInUserKoFollowing, array($userKoId));
    $serializedLoggedInUserKoFollowing = serialize($unserializedLoggedInUserKoFollowing);

    $updateLoggedInUserKoFollowing = "UPDATE `userfollowfollowing` SET `following` = '$serializedLoggedInUserKoFollowing' WHERE `userfollowfollowing`.`id` = '$userid'";
    $resultOfLoggedInUserKoFollowing = mysqli_query($con, $updateLoggedInUserKoFollowing);

    $fetchLoggedInUserKoFollowing = "SELECT * from userfollowfollowing WHERE `id`= '$userid'";
    $resultOfFetchLoggedInUserKoFollowing = mysqli_query($con, $fetchLoggedInUserKoFollowing);
    $detailOfLoggedInUserKoFollowing = mysqli_fetch_assoc($resultOfFetchLoggedInUserKoFollowing);

    $_SESSION['numberOfFollowing'] = count(unserialize($detailOfLoggedInUserKoFollowing['following']));

    //Updating Other User's Followers List
    $fetchOtherUserKoFollowers = "SELECT * from userfollowfollowing WHERE `id`= '$userKoId'";
    $resultOfFetchOtherUserKoFollowers = mysqli_query($con, $fetchOtherUserKoFollowers);
    $detailOfFetchOtherUserKoFollowers = mysqli_fetch_assoc($resultOfFetchOtherUserKoFollowers);

    $unserializedOtherUserKoFollowers = unserialize($detailOfFetchOtherUserKoFollowers['follow']);
    $unserializedOtherUserKoFollowers = array_diff($unserializedOtherUserKoFollowers, array($userid));
    $serializedOtherUserKoFollowers = serialize($unserializedOtherUserKoFollowers);

    $updateOtherUserKoFollowers = "UPDATE `userfollowfollowing` SET `follow` = '$serializedOtherUserKoFollowers' WHERE `userfollowfollowing`.`id` = '$userKoId'";
    $resultOfOtherUserKoFollowers = mysqli_query($con, $updateOtherUserKoFollowers);
    if ($resultOfLoggedInUserKoFollowing && $resultOfOtherUserKoFollowers) {
      $_SESSION['tempID'] = "post" . $_GET['postID'];
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
        <img src="<?php echo $_SESSION['profilePicLocation'] ?>" alt="logo" />
        <div class="userdetails d-flex">
          <div class="name-profession d-flex">
            <h2><?php echo $name ?></h2>
            <p><?php echo $userposition ?></p>
          </div>
          <hr />
          <div class="userfollow d-flex">
            <p>Following</p>
            <span><?php echo $_SESSION['numberOfFollowing'] ?></span>
            <br />
            <p>Followers</p>
            <span><?php echo $_SESSION['numberOfFollow'] ?></span>
          </div>
        </div>
      </div>
      <div class="lower-suggestion cardup">
        <?php
        $fetchUserSuggesion = "SELECT * from userdetails WHERE `username`!= '$username'";
        $resultOfFetchUserSuggesion = mysqli_query($con, $fetchUserSuggesion);
        $numOfFetchUserSuggesion = mysqli_num_rows($resultOfFetchUserSuggesion);

        if ($numOfFetchUserSuggesion > 0) {
          echo "<h3>Suggestions</h3>
          <div class='suggest-user'>";
          while ($detailOfFetchUserSuggesion = mysqli_fetch_assoc($resultOfFetchUserSuggesion)) {
            $specificSuggestUserName = $detailOfFetchUserSuggesion['name'];
            $specificSuggestUserUserName = $detailOfFetchUserSuggesion['username'];
            $specificSuggestUserPosition = $detailOfFetchUserSuggesion['userposition'];
            $specificSuggestUserGender = strtolower($detailOfFetchUserSuggesion['usergender']);

            if ($specificSuggestUserUserName != "admin") {
              $specificSuggestUserProfilePicLocation =  file_exists("./img/profilepictures/$specificSuggestUserUserName.jpg") ? "./img/profilepictures/$specificSuggestUserUserName.jpg" : "./img/$specificSuggestUserGender.png";
            } else {
              $specificSuggestUserProfilePicLocation = "./img/logo.png";
            }

            echo "<div class='suggest-user-1 d-flex'>
                  <img src='$specificSuggestUserProfilePicLocation' alt='suser' />
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

      $fetchFollowingList = "SELECT * from userfollowfollowing WHERE `username`= '$username'";
      $resultOfFetchFollowingList = mysqli_query($con, $fetchFollowingList);
      $iAmFollowing = unserialize((mysqli_fetch_assoc($resultOfFetchFollowingList))['following']);

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

          $SpecificUserID = $detailOfSpecificUser["id"];
          $SpecificUserName = $detailOfSpecificUser["name"];
          $SpecificUserUserName = $detailOfSpecificUser["username"];
          $SpecificUserPosition = $detailOfSpecificUser["userposition"];
          $SpecificUserGender = strtolower($detailOfSpecificUser["usergender"]);

          $amIFollowing = in_array($SpecificUserID, $iAmFollowing);

          if ($SpecificUserUserName != "admin") {
            $specificUserProfilePicLocation =  file_exists("./img/profilepictures/$SpecificUserUserName.jpg") ? "./img/profilepictures/$SpecificUserUserName.jpg" : "./img/$SpecificUserGender.png";
          } else {
            $specificUserProfilePicLocation = "./img/logo.png";
          }

          echo "<div class='userblog cardup' id='post" . $id . "'>
            <div class='userblog-profile d-flex'>
              <img src='$specificUserProfilePicLocation' alt='logo' />
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
              echo (!$amIFollowing) ? "
                <a href='http://localhost/blogphp/index.php?followUserID=$SpecificUserID&postID=$id' class='btn userinteract-btn' id='follow'>
                  <i class='fa-solid fa-cloud-bolt'></i>Follow
                </a>" : "<a href='http://localhost/blogphp/index.php?unFollowUserID=$SpecificUserID' class='btn userinteract-btn' id='follow'>
                <i class='fa-solid fa-cloud-bolt'></i>Followed
              </a>";
          echo "
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
          <a href="<?php echo ($_SESSION['username'] == "admin") ? "admin.php" : "dashboard.php" ?>"> <button class="btn userinteract-btn">Dashboard</button></a>
        </div>
      </div>
      <div class='news-lower cardup'>
        <?php
        $fetchDoYouKnow = "SELECT * FROM `doyouknow`";
        $resultOfFetchDoYouKnow = mysqli_query($con, $fetchDoYouKnow);
        if (mysqli_num_rows($resultOfFetchDoYouKnow) > 0) {
          echo "<h3>Do You Know?</h3>
          <hr />
          <div class='news-lower-list'>";

          while ($row = mysqli_fetch_assoc($resultOfFetchDoYouKnow)) {
            $description = $row['description'];
            echo "<div class='newscard'>$description</div>";
          }
        }
        ?>
      </div>
    </div>
    </div>
  </section>
  <script src="https://kit.fontawesome.com/4187f8db55.js" crossorigin="anonymous"></script>
</body>

</html>