<?php
session_start();

if (!$_SESSION['loggedIn']) {
    header("location:./login.php");
}
include './partials/dbconnect.php';
$name = $_SESSION['name'];
$userposition = $_SESSION['userposition'];
$username = $_SESSION['username'];
$usergender = $_SESSION['usergender'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //To Save Post 
    if (isset($_POST['savePost'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];

        $likes = array();
        $serializedLikesArray = serialize($likes);

        $sql = "INSERT INTO `userposts` (`username`, `title`, `description`, `likes`, `createdtime`) VALUES ('$username', '$title', '$description', '$serializedLikesArray', current_timestamp())";

        $result = mysqli_query($con, $sql);
        if ($result) {
            header("location:./dashboard.php");
        }
    }
    //To Update The User's Position, Phone Number & Security Code
    if (isset($_POST['updateUserDetail'])) {
        $_SESSION['userposition'] = $userposition = $_POST['position'];
        $_SESSION['phone_no'] = $userphone_no = $_POST['phno'];
        $_SESSION['securitycode'] = $userSecurityCode = $_POST['securitycode'];

        $securityCodeHash = password_hash($userSecurityCode, PASSWORD_DEFAULT);

        $sql = "UPDATE `userdetails` SET `userposition` = '$userposition', `phone_no` = '$userphone_no', `securitycode` = '$securityCodeHash' WHERE `userdetails`.`username` = '$username'";

        $result = mysqli_query($con, $sql);

        $target_dir = "./img/profilepictures/";
        $file_extension = pathinfo($_FILES["profilePicture"]["name"], PATHINFO_EXTENSION);
        $new_file_name = "$username." . $file_extension;
        $target_file = $target_dir . $new_file_name;

        if ((move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $target_file)) && $result) {
            $_SESSION['profilePicLocation'] = "./img/profilepictures/$username.jpg";
            header("location:./dashboard.php");
        }
    }

    //To Update The Existing Post In Database
    if (isset($_POST['updateExistingPost'])) {
        $id = $_SESSION['updatingPostId'];
        $title = $_POST['title'];
        $description = $_POST['description'];

        $sql = "UPDATE `userposts` SET `title` = '$title', `description` = '$description' WHERE `userposts`.`id` = '$id'";
        $result = mysqli_query($con, $sql);

        if ($result) {
            unset($_SESSION['updatingPostId']);
            unset($_SESSION['title']);
            unset($_SESSION['description']);
            header("location:./dashboard.php");
        }
    }

    //To Delete The User From Database
    if (isset($_POST['deleteAccount'])) {
        $id = $_SESSION['id'];

        $fetchUser = "SELECT * FROM `userdetails` WHERE `id` = '$id'";
        $resultOfFetchUser = mysqli_query($con, $fetchUser);
        $row = mysqli_fetch_assoc($resultOfFetchUser);

        $username = $row['username'];

        $sql = "DELETE FROM `userdetails` WHERE `userdetails`.`id` = '$id'";
        $result = mysqli_query($con, $sql);
        $sql = "DELETE FROM `userposts` WHERE `userposts`.`username` = '$username'";
        $result = mysqli_query($con, $sql);

        if(file_exists("./img/profilepictures/$username.jpg")){
            unlink("./img/profilepictures/$username.jpg");
        }

        if ($result) {
            header("location:./partials/logout.php");
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    //To Get The Data Of Post From The Database. Further Update Is Done via Post Request
    if (isset($_GET['updatePostID'])) {
        $_SESSION['updatingPostId'] = $updatingPostId = $_GET['updatePostID'];
        $fetchupdatingPost = "SELECT * FROM `userposts` WHERE `id` = '$updatingPostId'";
        $resultOffetchupdatingPost = mysqli_query($con, $fetchupdatingPost);
        $row = mysqli_fetch_assoc($resultOffetchupdatingPost);
        $_SESSION['title'] = $row['title'];
        $_SESSION['description'] = $row['description'];
    }

    //To Delete The Existing Post 
    if (isset($_GET['deletePostID'])) {
        $deletingPostId = $_GET['deletePostID'];
        $deletingPost = "DELETE FROM `userposts` WHERE `id` = '$deletingPostId'";
        $resultOfDeletingPost = mysqli_query($con, $deletingPost);
        if ($resultOfDeletingPost) {

            //ReOrder Table(userposts) id After Deleting
            $sql = "SET @count = 0";
            mysqli_query($con, $sql);
            $sql = "UPDATE userposts SET userposts.id = @count:= @count + 1";
            mysqli_query($con, $sql);
            $sql = "ALTER TABLE userposts AUTO_INCREMENT = 1";
            mysqli_query($con, $sql);

            header("location:./dashboard.php");
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

    <link rel="stylesheet" href="css/utils.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/maininterface.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    <?php include './partials/navbar.php'; ?>
    <section class="dashboard-interface d-flex">
        <div class="dashboard-left">
            <div class="addUpdateForm cardup">
                <form method="POST">
                    <div class="addUpdateFormContent">
                        <label for="title">Title:</label>
                        <input type="text" name="title" value="<?php echo isset($_GET['updatePostID']) ? $_SESSION['title'] : ''; ?>">
                    </div>
                    <div class="addUpdateFormContent">
                        <label for="description">Description:</label>
                        <textarea type="text" name="description" rows="5"><?php echo isset($_GET['updatePostID']) ? $_SESSION['description'] : ''; ?></textarea>
                    </div>
                    <div class="addUpdateFormContent">
                        <?php
                        if (!isset($_GET['updatePostID'])) {
                            echo
                            "<button type='submit' name='savePost'>Save</button>";
                        } else {
                            echo "<button type='submit' name='updateExistingPost'>Update</button>";
                        }
                        ?>
                    </div>
                </form>
            </div>
            <?php
            $fetchSpecificPost = "SELECT * from userposts WHERE `username`= '$username'";
            $resultOfFetchSpecificPost = mysqli_query($con, $fetchSpecificPost);
            $numOfSpecificPost = mysqli_num_rows($resultOfFetchSpecificPost);

            $userProfilePicLocation = $_SESSION['profilePicLocation'];

            if ($numOfSpecificPost > 0) {
                echo "
                    <h1>Your Post</h1>
                    <div class='yourPost'>";
                while ($row = mysqli_fetch_assoc($resultOfFetchSpecificPost)) {
                    $specificPostKoID = $row['id'];
                    $specificPostKoTitle = $row['title'];
                    $specificPostKoDescription = $row['description'];

                    $specificPostKoLikes = count(unserialize($row['likes']));

                    echo "<div class='userblog cardup'>
                            <div class='userblog-profile d-flex'>
                                <img src='$userProfilePicLocation' alt='logo' />
                                <div class='userblog-profile-nameposition'>
                                    <h3>$name</h3>
                                    <h5>$userposition</h5>
                                </div>
                            </div>
                            <div class='userblog-post'>
                                <h1>$specificPostKoTitle</h1>
                                <p>
                                $specificPostKoDescription
                                </p>
                            </div>
                            <hr />
                            <div class='userblog-interaction d-flex'>
                                <span class='noOfLikes'>Likes: $specificPostKoLikes</span>
                                <form method='GET'>
                                    <a class='btn userinteract-btn' href='dashboard.php?updatePostID=$specificPostKoID' name='editOwnPost'>
                                    <i class='fa-solid fa-pen-to-square'></i>Edit
                                    </a>
                                    <a class='btn userinteract-btn' href='dashboard.php?deletePostID=$specificPostKoID'  name='deleteOwnPost'>
                                    <i class='fa-solid fa-trash'></i>Delete
                                    </a>
                                    </form>
                            </div>
                        </div>";
                }
            }
            ?>
        </div>
        </div>
        <div class="dashboard-right cardup">
            <div class="picNamePosition d-flex">
                <img src=<?php echo $_SESSION['profilePicLocation'] ?> alt="">
                <div class="namePosition">
                    <h2><?php echo $_SESSION['name'] ?></h2>
                    <span><?php echo $_SESSION['userposition'] ?></span>
                </div>
            </div>
            <hr>
            <div class="followFollowing d-flex">
                <div class="followers d-flex">
                    <span><?php echo $_SESSION['numberOfFollow'] ?></span>
                    <p>Followers</p>
                </div>
                <div class="following d-flex">
                    <span><?php echo $_SESSION['numberOfFollowing'] ?></span>
                    <p>Following</p>
                </div>
            </div>
            <hr>
            <form method="POST" enctype="multipart/form-data">
                <div class="otherInfo">
                    <div class="otherInfoContent">
                        <label>USERNAME</label>
                        <input value="<?php echo $_SESSION['username'] ?>" disabled>
                    </div>
                    <div class="otherInfoContent">
                        <label>EMAIL</label>
                        <input type="email" value="<?php echo $_SESSION['useremail'] ?>" disabled>
                    </div>
                    <div class="otherInfoContent">
                        <label>GENDER</label>
                        <input type="text" placeholder="ENTER YOUR GENDER" value="<?php echo ucfirst($_SESSION['usergender']) ?>" disabled>
                    </div>
                    <div class="otherInfoContent">
                        <label>POSITION</label>
                        <input type="text" name="position" placeholder="ex designer,editor" value="<?php echo $_SESSION['userposition'] ?>" <?php if ($_SESSION['userposition'] != "Bloggram User") {
                                                                                                                                                echo "disabled";
                                                                                                                                            } ?>>
                    </div>
                    <div class="otherInfoContent">
                        <label>PHONE NUMBER</label>
                        <input type="text" name="phno" placeholder="UPDATE YOUR NUMBER" value="<?php echo $_SESSION['phone_no'] ?>" <?php if (isset($_SESSION['phone_no'])) {
                                                                                                                                        echo "disabled";
                                                                                                                                    } ?>>
                    </div>
                    <div class="otherInfoContent">
                        <label>SECURITY CODE</label>
                        <input type="text" name="securitycode" placeholder="UPDATE SECURITY CODE FOR RECOVERY" value="<?php echo $_SESSION['securitycode'] ?>" <?php if (isset($_SESSION['securitycode'])) {
                                                                                                                                                                    echo "disabled";
                                                                                                                                                                } ?>>
                    </div>
                    <?php echo !file_exists("./img/profilepictures/$username.jpg")?"<div class='otherInfoContent'>
                        <label>Upload Profile Picture</label>
                        <input type='file' name='profilePicture' accept='image/jpeg'>
                    </div>":"" ?>
                    
                </div>
                <div class="updateDelAcnt d-flex">
                    <?php if (!isset($_SESSION['userposition']) || !isset($_SESSION['phone_no']) || !isset($_SESSION['securitycode'])) {
                        echo "<button type='submit' name='updateUserDetail'>Save</button>";
                    }
                    ?>
                    <button type="delete" name="deleteAccount">Delete Account</button>
                </div>
            </form>
        </div>
    </section>
    <script src="https://kit.fontawesome.com/4187f8db55.js" crossorigin="anonymous"></script>
</body>

</html>