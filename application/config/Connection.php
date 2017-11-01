<?php
//base untuk semua halaman di nomnomdb
require_once ("Model.php");
require_once ("Host.php");
require_once ("Database.php");
$d = new Model();
$_SRVCON = $d->setConnection($_MYSQLHOST,$_MYSQLUSER,$_MYSQLPASS);
$_DBCON = $d->selectDb($_DBNAME);
?>