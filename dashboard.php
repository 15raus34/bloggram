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
    if (isset($_POST['savePost'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];

        $sql = "INSERT INTO `userposts` (`username`, `title`, `description`, `likes`, `createdtime`) VALUES ('$username', '$title', '$description', '0', current_timestamp())";

        $result = mysqli_query($con, $sql);
        if ($result) {
            header("location:./dashboard.php");
        }
    }

    if (isset($_POST['updateUserDetail'])) {
        $_SESSION['userposition'] = $userposition = $_POST['position'];
        $_SESSION['phone_no'] = $userphone_no = $_POST['phno'];
        $_SESSION['securitycode'] = $userSecurityCode = $_POST['securitycode'];

        $securityCodeHash = password_hash($userSecurityCode, PASSWORD_DEFAULT);

        $sql = "UPDATE `userdetails` SET `userposition` = '$userposition', `phone_no` = '$userphone_no', `securitycode` = '$securityCodeHash' WHERE `userdetails`.`username` = '$username'";

        $result = mysqli_query($con, $sql);
        if ($result) {
            header("location:./dashboard.php");
        }
    }
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
    
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['updatePostID'])) {
        $_SESSION['updatingPostId']=$updatingPostId = $_GET['updatePostID'];
        $fetchupdatingPost = "SELECT * from `userposts` WHERE `id` = '$updatingPostId'";
        $resultOffetchupdatingPost = mysqli_query($con, $fetchupdatingPost);
        $row = mysqli_fetch_assoc($resultOffetchupdatingPost);
        $_SESSION['title'] = $row['title'];
        $_SESSION['description'] = $row['description'];
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
            <h1>Your Post</h1>
            <div class="yourPost">
                <?php
                $fetchSpecificPost = "SELECT * from userposts WHERE `username`= '$username'";
                $resultOfFetchSpecificPost = mysqli_query($con, $fetchSpecificPost);
                $numOfSpecificPost = mysqli_num_rows($resultOfFetchSpecificPost);

                if ($numOfSpecificPost > 0) {
                    while ($row = mysqli_fetch_assoc($resultOfFetchSpecificPost)) {
                        $specificPostKoID = $row['id'];
                        $specificPostKoTitle = $row['title'];
                        $specificPostKoDescription = $row['description'];

                        echo "<div class='userblog cardup'>
                            <div class='userblog-profile d-flex'>
                                <img src='./img/" . strtolower($usergender) . ".png' alt='logo' />
                                <div class='userblog-profile-nameposition'>
                                    <h3>$name</h3>
                                    <h5>$userposition</h5>
                                </div>
                            </div>
                            <div class='userblog-post'>
                                <p>
                                $specificPostKoDescription
                                </p>
                            </div>
                            <hr />
                            <div class='userblog-interaction d-flex'>
                                <form method='get'>
                                    <a class='btn userinteract-btn' href='dashboard.php?updatePostID=$specificPostKoID' class='btn userinteract-btn' name='editOwnPost'>
                                    <i class='fa-solid fa-pen-to-square'></i>Edit
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
                <img src=<?php echo "'./img/" . strtolower($usergender) . ".png'" ?> alt="">
                <div class="namePosition">
                    <h2><?php echo $_SESSION['name'] ?></h2>
                    <span><?php echo $_SESSION['userposition'] ?></span>
                </div>
            </div>
            <hr>
            <div class="followFollowing d-flex">
                <div class="followers d-flex">
                    <span>0</span>
                    <p>Followers</p>
                </div>
                <div class="following d-flex">
                    <span>0</span>
                    <p>Following</p>
                </div>
            </div>
            <hr>
            <form method="POST">
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
                </div>
                <div class="updateDelAcnt d-flex">
                    <?php if (!isset($_SESSION['userposition']) || !isset($_SESSION['phone_no']) || !isset($_SESSION['securitycode'])) {
                        echo "<button type='submit' name='updateUserDetail'>Save</button>";
                    }
                    ?>
                    <button type="delete">Delete Account</button>
                </div>
            </form>
        </div>
    </section>
    <script src="https://kit.fontawesome.com/4187f8db55.js" crossorigin="anonymous"></script>
</body>

</html>