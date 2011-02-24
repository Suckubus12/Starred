<?php
session_start();
include 'Starred.class.php';
$level = $_GET['l'];
$page = $_GET['page'];
$user = $_SESSION['username'];
$Starred = new Starred;
echo $Starred->rateit($level,$page,$user);
?>
