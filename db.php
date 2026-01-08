<?php
$location = "localhost";
$account = "root";
$password = "";
$dbname = "blog";

$link = mysqli_connect($location, $account, $password, $dbname);

if (!$link) {
    die('無法連接資料庫: ' . mysqli_connect_error());
}

mysqli_query($link, "SET NAMES utf8mb4");
?>