<nav>
    <div class="nav-left-items d-flex">
        <img src="./img/logonobg.png" alt="logo" />
        <span>BLOG <br>GRAM</span>
    </div>
    <div class="nav-right-items d-flex">
        <div class="user-pic">
            <img src="./img/<?php $usergender = $_SESSION['usergender'];echo strtolower($usergender)?>.png" alt="logo" />
        </div>
        <h3><?php echo $_SESSION['username'] ?></h3>
        <a href='./partials/logout.php'><button class='btn'>Logout</button></a>

    </div>
</nav>