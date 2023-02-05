<?php
session_start();

if (!$_SESSION['loggedIn']) {
    header("location:./login.php");
}
$added = false;
$updatedDetail = false;
$edited = false;
$deleted = false;

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
            $added = true;
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
            header("location:./admin.php");
        }
    }

    //To Add Do You Know
    if (isset($_POST['addDoYouKnow'])) {
        $doYouKnow = $_POST['doYouKnow'];

        $sql = "INSERT INTO `doyouknow` (`description`, `timestamp`) VALUES ('$doYouKnow', current_timestamp());";
        $result = mysqli_query($con, $sql);

        if ($result) {
            header("location:./admin.php");
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

            $deleted = true;
        }
    }

    //To Delete The User From Database By Admin
    if (isset($_GET['deleteUserID'])) {
        $id = $_GET['deleteUserID'];

        $fetchUser = "SELECT * FROM `userdetails` WHERE `id` = '$id'";
        $resultOfFetchUser = mysqli_query($con, $fetchUser);
        $row = mysqli_fetch_assoc($resultOfFetchUser);

        $username = $row['username'];

        $sql = "DELETE FROM `userdetails` WHERE `userdetails`.`id` = '$id'";
        $result = mysqli_query($con, $sql);
        $sql = "DELETE FROM `userposts` WHERE `userposts`.`username` = '$username'";
        $result = mysqli_query($con, $sql);

        if (file_exists("./img/profilepictures/$username.jpg")) {
            unlink("./img/profilepictures/$username.jpg");
        }

        if ($result) {
            header("location:./admin.php");
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

            if ($deleted) {
                echo
                "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <strong>Deleted</strong> Post had been deleted.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
            }
            if ($edited) {
                echo
                "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                <strong>Edited</strong> Successfully.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
            }
            if ($added) {
                echo
                "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                <strong>Added</strong> New Post Is Up.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
            }
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
                                <img src='./img/logo.png' alt='logo' />
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
                                    <a class='btn userinteract-btn' href='admin.php?updatePostID=$specificPostKoID' class='btn userinteract-btn' name='editOwnPost'>
                                    <i class='fa-solid fa-pen-to-square'></i>Edit
                                    </a>
                                    <a class='btn userinteract-btn' href='admin.php?deletePostID=$specificPostKoID' class='btn userinteract-btn' name='deleteOwnPost'>
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
                <img src="./img/logo.png" alt="">
                <div class="namePosition">
                    <h2>BLOGGRAM ADMIN</h2>
                    <span>developer | admin</span>
                </div>
            </div>
            <hr>
            <div class="followFollowing d-flex">
                <?php
                $fetchNumberOfUsers = "SELECT * from userdetails";
                $resultOfFetchNumberOfUsers = mysqli_query($con, $fetchNumberOfUsers);
                $_SESSION['numOfUsers'] = mysqli_num_rows($resultOfFetchNumberOfUsers) - 1;
                ?>
                <div class="followers d-flex">
                    <span><?php echo $_SESSION['numOfUsers'] ?></span>
                    <p>Users</p>
                </div>
                <?php
                $fetchNumberOfPosts = "SELECT * from userposts";
                $resultOfFetchNumberOfPosts = mysqli_query($con, $fetchNumberOfPosts);
                $_SESSION['numOfPosts'] = mysqli_num_rows($resultOfFetchNumberOfPosts);
                ?>
                <div class="following d-flex">
                    <span><?php echo $_SESSION['numOfPosts'] ?></span>
                    <p>Posts</p>
                </div>
            </div>
            <hr>
            <form method="GET">
                <div class="otherInfo adminPanelUserInfo">
                    <?php
                    $fetchNumberOfUsers = "SELECT * from userdetails LIMIT 1,18446744073709551615";
                    $resultOfFetchNumberOfUsers = mysqli_query($con, $fetchNumberOfUsers);
                    $i = 0;
                    if (mysqli_num_rows($resultOfFetchNumberOfUsers) > 0) {
                        echo "<table border='1'>
                        <tr>
                            <th>S.No.</th>
                            <th>Users</th>
                            <th>Username</th>
                            <th>Contact No</th>
                            <th>Posts</th>
                            <th>Delete</th>
                        </tr>";

                        while ($row = mysqli_fetch_assoc($resultOfFetchNumberOfUsers)) {
                            $username = $row['username'];
                            $fetchUser = "SELECT * FROM `userposts` WHERE `username` = '$username'";
                            $resultOfFetchUser = mysqli_query($con, $fetchUser);
                            $numOfPosts = mysqli_num_rows($resultOfFetchUser);
                            echo "<tr>
                                <td>" . (++$i) . "</td>
                                <td>" . $row['name'] . "</td>
                                <td>" . $username . "</td>
                                <td>" . $row['phone_no'] . "</td>
                                <td>" . $numOfPosts . "</td>
                                <td><a class='adminPanelDel' href='admin.php?deleteUserID=" . $row['id'] . "' name='deleteOwnPost'>
                                        <i class='fa-solid fa-trash'></i>
                                    </a></td>
                            </tr>";
                        }
                    }
                    ?>
                    </table>
                </div>
            </form>
            <hr>
            <form method="POST">
                <div class="addUpdateFormContent">
                    <label for="description">Do You Know:</label>
                    <textarea type="text" name="doYouKnow" rows="5" placeholder="DO YOU KNOW....."></textarea>
                </div>
                <div class="addUpdateFormContent">
                    <button type='submit' name='addDoYouKnow'>Submit</button>
                </div>
            </form>
        </div>
    </section>
    <script src="https://kit.fontawesome.com/4187f8db55.js" crossorigin="anonymous"></script>
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>