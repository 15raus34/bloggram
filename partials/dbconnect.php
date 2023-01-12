<?php 
    $server = "localhost";
    $username = "root";
    $password = "";
    $database = "bloggram1534";

    $con = mysqli_connect($server,$username,$password,$database);
     
    if(!$con){
        echo "DataBase Disconnected";
    }
?>