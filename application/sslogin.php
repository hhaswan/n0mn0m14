<?php
require_once("config/Host.php");
require_once("../system/Databasenomnom.php");
//SESSION token dicek validitasnya 
session_start();
if(isset($_SESSION['utoken'])){
	mysql_select_db($_DBNOMNOM);
	$sql = "select token from _login where token = '$_SESSION[utoken]'";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) != 1){
		echo "<script>window.location.href = '../system/login.php' ;</script>";
	}
}
else{
	echo "<script>window.location.href = '../system/login.php' ;</script>";
}
?>