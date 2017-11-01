<?php
session_start();
$_SESSION = array();
header("location:../system/login.php");
?>