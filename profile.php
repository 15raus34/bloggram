<?php
session_start();

if (!$_SESSION['loggedIn']) {
  header("location:./login.php");
}

include './partials/dbconnect.php';

$userid = $_SESSION['id'];
$username = $_SESSION['username'];
$usergender = strtolower($_SESSION['usergender']);

if (!isset($_SESSION['profileId'])) {
  $_SESSION['profileId'] = $_GET['profileId'];
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
      header("location:http://localhost/blogphp/profile.php?profileId=" . $_SESSION['profileId']);
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
      header("location:http://localhost/blogphp/profile.php?profileId=" . $_SESSION['profileId']);
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
      header("location:http://localhost/blogphp/profile.php?profileId=" . $_SESSION['profileId']);
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
      header("location:http://localhost/blogphp/profile.php?profileId=" . $_SESSION['profileId']);
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <link rel="stylesheet" href="css/utils.css">
  <link rel="stylesheet" href="css/navbar.css">
  <link rel="stylesheet" href="css/dashboard.css">
  <link rel="stylesheet" href="css/maininterface.css">
</head>

<body>
  <?php include './partials/navbar.php'; ?>
  <section class="dashboard-interface d-flex">
    <div class="sub-interface sub-interface-blog">
      <?php
      $specificUserID = $_GET['profileId'];

      $fetchSpecificUsername = "SELECT * from userdetails WHERE `id`= '$specificUserID'";
      $resultOfFetchSpecificUsername = mysqli_query($con, $fetchSpecificUsername);
      $SpecificUser = mysqli_fetch_assoc($resultOfFetchSpecificUsername);

      $SpecificUsername = $SpecificUser['username'];
      $SpecificUserKoName = $SpecificUser['name'];
      $SpecificUserPosition = $SpecificUser['userposition'];
      $SpecificUserGender = $SpecificUser['usergender'];
      $SpecificUserGenderForPP = strtolower($SpecificUser['usergender']);

      $fetchPost = "SELECT * from userposts WHERE `username`= '$SpecificUsername'";
      $resultOfFetchPost = mysqli_query($con, $fetchPost);
      $numOfPost = mysqli_num_rows($resultOfFetchPost);

      $fetchFollowingList = "SELECT * from userfollowfollowing WHERE `username`= '$username'";
      $resultOfFetchFollowingList = mysqli_query($con, $fetchFollowingList);
      $iAmFollowing = unserialize((mysqli_fetch_assoc($resultOfFetchFollowingList))['following']);

      $_SESSION['otheruserprofilePicLocation'] =  file_exists("./img/profilepictures/$SpecificUsername.jpg") ? "./img/profilepictures/$SpecificUsername.jpg" : "./img/$SpecificUserGenderForPP.png";

      if ($numOfPost > 0) {
        while ($row = mysqli_fetch_assoc($resultOfFetchPost)) {
          $id = $row['id'];
          $title = $row['title'];
          $description = $row['description'];

          $didILike = in_array($userid, unserialize($row['likes']));
          $likes = count(unserialize($row['likes']));

          echo "<div class='userblog cardup' id='post" . $id . "'>
            <div class='userblog-profile d-flex'>";

            if($SpecificUsername != "admin"){
              echo "<img src='".$_SESSION['otheruserprofilePicLocation']."' alt='logo' />";
            }else{
              echo "<img src='./img/logo.png' alt='logo' />";
            }
              echo "
              <div class='userblog-profile-nameposition'>
                <h3>$SpecificUserKoName</h3>
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
                <a href='profile.php?likePostID=$id' class='btn userinteract-btn' id='like'>
                  <i class='fa-sharp fa-solid fa-heart'></i>Like &nbsp;<span>$likes</span>
                </a>" : "<a href='profile.php?disLikePostID=$id' class='btn userinteract-btn' id='disLike'>
                <i class='fa-sharp fa-solid fa-heart'></i>Liked &nbsp;<span>$likes</span>
              </a>";
              echo 
              "</div>
            </form>
          </div>";
        }
      }
      ?>
    </div>
    <div class="dashboard-right cardup">
      <div class="picNamePosition d-flex">
        <?php
        $fetchProfileUserKoFollowFollowing = "SELECT * from userfollowfollowing WHERE `id`= '$specificUserID'";
        $resultOfFetchProfileUserKoFollowFollowing = mysqli_query($con, $fetchProfileUserKoFollowFollowing);
        $detailOfProfileUserKoFollowFollowing = mysqli_fetch_assoc($resultOfFetchProfileUserKoFollowFollowing);

        $profileUserKoFollowing = count(unserialize($detailOfProfileUserKoFollowFollowing['following']));
        $profileUserKoFollow = count(unserialize($detailOfProfileUserKoFollowFollowing['follow']));

        if($SpecificUsername != "admin"){
          echo "<img src='".$_SESSION['otheruserprofilePicLocation']."' alt='logo' />";
        }else{
          echo "<img src='./img/logo.png' alt='logo' />";
        }
        ?>
        <div class="namePosition">
          <h2><?php echo strtoupper($SpecificUserKoName) ?></h2>
          <span><?php echo $SpecificUserPosition ?></span>
        </div>
        <?php echo "
        <form>
              <div class='userblog-interaction d-flex'>";
        $amIFollowing = in_array($specificUserID, $iAmFollowing);
        echo (!$amIFollowing) ? "
                <a href='profile.php?followUserID=$specificUserID' class='btn userinteract-btn' id='follow'>
                  <i class='fa-solid fa-cloud-bolt'></i>Follow
                </a>" : "<a href='profile.php?unFollowUserID=$specificUserID' class='btn userinteract-btn' id='follow'>
                <i class='fa-solid fa-cloud-bolt'></i>Followed
              </a>";
        echo "
              </div>
            </form>";
        ?>
      </div>
      <hr>
      <div class="followFollowing d-flex">
        <div class="followers d-flex">
          <span><?php echo $profileUserKoFollow ?></span>
          <p>Followers</p>
        </div>
        <div class="following d-flex">
          <span><?php echo $profileUserKoFollowing ?></span>
          <p>Following</p>
        </div>
      </div>
      <hr>
      <form id="form" method="POST" enctype="multipart/form-data">
        <div class="otherInfo">
          <div class="otherInfoContent">
            <label>USERNAME</label>
            <input value="<?php echo $SpecificUserKoName ?>" disabled>
          </div>
          <div class="otherInfoContent">
            <label>GENDER</label>
            <input type="text" placeholder="ENTER YOUR GENDER" value="<?php echo ucfirst($SpecificUserGender) ?>" disabled>
          </div>
          <div class="otherInfoContent">
            <label>POSITION</label>
            <input type="text" name="position" placeholder="ex designer,editor" value="<?php echo $SpecificUserPosition ?>" disabled>
          </div>
        </div>
      </form>
    </div>
  </section>
  <script src="https://kit.fontawesome.com/4187f8db55.js" crossorigin="anonymous"></script>
  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>