<nav>
    <div class="nav-left-items d-flex">
        <a href="http://localhost/blogphp/index.php"><img src="./img/logonobg.png" alt="logo" /></a>
        <span>BLOG <br>GRAM</span>
    </div>
    <div class="nav-right-items d-flex">
        <div class="user-pic">
            <img src="<?php echo $_SESSION['profilePicLocation']?>" alt="logo" />
        </div>
        <h3><?php echo $_SESSION['username'] ?></h3>
        <a href='./partials/logout.php'><button class='btn'>Logout</button></a>

    </div>
</nav>