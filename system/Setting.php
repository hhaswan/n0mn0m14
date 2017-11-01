<?php
require_once("Baseconfig.php");	//berisi $hostfilename dan $dbfilename
require_once("Cipher.php");
if(file_exists($hostfilename)){
	require_once($hostfilename);		//berisi variabel _MYSQLHOST| _MYSQLUSER | _MYSQLPASS
	require_once("Databasenomnom.php");	//berisi variabel _DBNOMNOM
	mysql_connect("$_MYSQLHOST","$_MYSQLUSER","$_MYSQLPASS");
	//$_DBNOMNOM = "nomnomdb";
}
//from udb
if(isset($_POST['dbname'])){
	$dbname = $_POST['dbname'];
	$sql = "SHOW DATABASES LIKE '$dbname'";
	$dbexist = mysql_fetch_array(mysql_query($sql));
	
	/*
	$isAvailable = (empty($dbexist)) ? false : true;
	echo json_encode(array(
	'valid' => $isAvailable,
	));
	*/
	//if(isset($_POST['isSaved'])){
	if(!empty($dbexist)){
		$file=fopen($dbfilename,"w");
fwrite($file,"<?php 
\$_DBNAME = '$dbname';
");
		fclose($file);
		
		//jika ada maka dbname akan diinput ke dblisted dbnomnom
		mysql_select_db($_DBNOMNOM);
		$sql = "select id,db_name from _dblisted where db_name = '$dbname'";
		$dbrec = mysql_fetch_array(mysql_query($sql));
		if(empty($dbrec)) mysql_query("insert into _dblisted values(null,'$dbname',now())");
		session_start();
		$_SESSION['dbname'] = $dbname;
		$_SESSION['dbid'] = $dbid = $dbrec['id'];
		echo "Database $dbname - $dbid activated! Click <a href='udbmenu.php'>here</a> to set your menu or proceed to admin homepage <a href='../application/'> here</a>";
	}
	else
		echo "No such database";
	
}
//from ureg.. Now: insert into db | Later: srvscript with ht 
if(isset($_POST['ureg'])) {
	//echo "<script>window.location.href = 'checksrv/$_POST[key]' </script>";
	$ureg = $_POST['ureg'];
	$pword = $_POST['pword'];
	$umail = $_POST['umail'];
	$key = $_POST['key'];
	mysql_select_db($_DBNOMNOM);
	if(!isset($_POST['isSaved'])){
		$sql = "select uname from _login where uname = '$ureg'";
		$result = mysql_query($sql);
		$numexist = mysql_num_rows($result);
		$isAvailable = ($numexist == 0) ? true : false;
		echo json_encode(array('valid' => $isAvailable));
	}
	else{
		$cipher = new cipher("$key");
		$encrypted_text = $cipher->encrypt($pword);
		$sql = "INSERT INTO `_login` VALUES (null,'$ureg','$encrypted_text','$umail','$key','')";
		if(mysql_query($sql)) echo "1";
		else echo "0";
	}
}

if(isset($_POST['ulogin'])) {
	session_start();
	$ulogin = $_POST['ulogin'];
	$pass = $_POST['pass'];
	$stlogin = false;
	mysql_select_db($_DBNOMNOM);
	$sql = "select * from _login where uname = '$ulogin'";
	$result = mysql_query($sql);
	$data = mysql_fetch_assoc($result);
	$numexist = mysql_num_rows($result);
	if($numexist == 1){
		$keyf = $data['kata_kunci'];
		$cipher = new cipher($keyf);
		$decrypted_text = $cipher->decrypt($data['pword']);
		if($pass == $decrypted_text){
			$waktu = date("H:i:s");
			$token = $cipher->encrypt($pass.$data['id'].$waktu);
			$sql = "update _login set token = '$token' where uname = '$ulogin'";
			$_SESSION['utoken'] = $token;
			if(mysql_query($sql)) $stlogin = true;
		}
	}
	echo $result = ($stlogin) ? "$token" : "0"; //jquery tidak menerima kembalian boolean
}
//kiriman dari usrv
if(isset($_POST['host'])){
	$host = $_POST['host']; $user = $_POST['huser']; $pass = $_POST['hpass']; 
	$prefix = trim($_POST['hprefix']); //can be null
	$con = mysql_connect("$host","$user","$pass");
	$result = ($con) ? "1" : "0"; //jquery tidak menerima kembalian boolean
	echo $result; //kembali ke usrv	
	
	if($result == "1") {
	$file=fopen($hostfilename,"w");
fwrite($file,"<?php 
\$_MYSQLHOST = '$host';
\$_MYSQLUSER = '$user';
\$_MYSQLPASS = '$pass';
");
	
//file system/prefix.txt diinisialisasi 
fclose($file);
$prefixfilename = "prefix.txt";
if(file_exists($prefixfilename)){
//cek prefix lama
	$fh = fopen($prefixfilename,'r');
	while ($oldprefix = fgets($fh)) {
	  if($oldprefix == '')
		$old_table = "nomnomdb";
	  else	
		$old_table = $oldprefix."_nomnomdb";
	}
	fclose($fh);
	
//tulis prefix baru 
	$file=fopen($prefixfilename,"w");
	fwrite($file,$prefix);
	fclose($file);

//file system/Databasenomnom.php diinisialisasi 
	if($prefix == '') $_DBNOMNOM = 'nomnomdb';
	else $_DBNOMNOM = $prefix.'_nomnomdb';
	$file=fopen("Databasenomnom.php","w");
	fwrite($file,"<?php 
\$_DBNOMNOM = '$_DBNOMNOM';
");
	fclose($file);	

//file Database.php diinisialisasi 
	$file=fopen($dbfilename,"w");
fwrite($file,"<?php 
\$_DBNAME = '$_DBNOMNOM';
");
	fclose($file);
	//mysql_select_db($old_table);
	mysql_query("CREATE DATABASE IF NOT EXISTS $_DBNOMNOM;");
	if(mysql_select_db($_DBNOMNOM)){
		mysql_query("RENAME TABLE $old_table._dblisted  TO $_DBNOMNOM._dblisted");
		mysql_query("UPDATE _dblisted SET db_name = '$_DBNOMNOM' WHERE id=1");
		mysql_query("RENAME TABLE $old_table._login  TO $_DBNOMNOM._login");
		mysql_query("RENAME TABLE $old_table._menu  TO $_DBNOMNOM._menu");
		mysql_query("RENAME TABLE $old_table._activitylog  TO $_DBNOMNOM._activitylog");
		mysql_query("DROP DATABASE $old_table");
	}
	
}
else{
	$file=fopen($prefixfilename,"w");
	fwrite($file,$prefix);
	fclose($file);

//file system/Databasenomnom.php diinisialisasi 
	if($prefix == '') $_DBNOMNOM = 'nomnomdb';
	else $_DBNOMNOM = $prefix.'_nomnomdb';
	$file=fopen("Databasenomnom.php","w");
	fwrite($file,"<?php 
\$_DBNOMNOM = '$_DBNOMNOM';
");
	fclose($file);	

//file Database.php diinisialisasi 
	$file=fopen($dbfilename,"w");
fwrite($file,"<?php 
\$_DBNAME = '$_DBNOMNOM';
");
	fclose($file);

  mysql_query("CREATE DATABASE IF NOT EXISTS $_DBNOMNOM;");
  if(mysql_select_db($_DBNOMNOM)){
	mysql_query("CREATE TABLE IF NOT EXISTS `_dblisted` (
		  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  `db_name` varchar(100) NOT NULL,
		  `insert_time` datetime NOT NULL
		) ENGINE= MyISAM;");
	mysql_query("CREATE TABLE IF NOT EXISTS `_login` (
		  `id` tinyint(2) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  `uname` varchar(50) NOT NULL,
		  `pword` varchar(100) NOT NULL,
		  `umail` varchar(100) NOT NULL,
		  `kata_kunci` varchar(100) NOT NULL,
		  `token` varchar(100) NOT NULL
		) ENGINE=MyISAM;");
	mysql_query("CREATE TABLE IF NOT EXISTS `_menu` (
		  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  `caption` varchar(200) NOT NULL,
		  `level` tinyint(3) NOT NULL,
		  `order_number` tinyint(3) NOT NULL,
		  `id_parent` int(11) NOT NULL,
		  `link_table` varchar(255) NOT NULL,
		  `id_dblisted` int(11) NOT NULL
		) ENGINE=MyISAM;");
	mysql_query("CREATE TABLE IF NOT EXISTS `_activitylog` (
		  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  `uname` varchar(100) NOT NULL,
		  `uip` varchar(20) NOT NULL,
		  `utime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `uhost` varchar(100) NOT NULL,
		  `uagent` varchar(17) NOT NULL,
		  `uactivity` text NOT NULL
		) ENGINE=MyISAM;");
	//mencegah database default terupdate 2 kali 
	$rs = mysql_query("select db_name from _dblisted where db_name = '$_DBNOMNOM'");
	if(mysql_num_rows($rs) == 0){
		mysql_query("INSERT INTO _dblisted VALUES(null,'$_DBNOMNOM',now())");
		//level parent:0 submenu:1 subsubmenu:2
		mysql_query("INSERT INTO _menu VALUES(1,'Data Master',0,1,0,'#',1)");
		mysql_query("INSERT INTO _menu VALUES(2,'Manag User ',0,2,0,'#',1)");
		mysql_query("INSERT INTO _menu VALUES(3,'Listed DB',1,1,1,'_dblisted',1)");
		mysql_query("INSERT INTO _menu VALUES(4,'Menu List',1,2,1,'_menu',1)");
		mysql_query("INSERT INTO _menu VALUES(5,'Activity Log',1,3,1,'_activitylog',1)");
		mysql_query("INSERT INTO _menu VALUES(6,'Login',1,1,2,'_login',1)");
	}
  }
  }
}
}

//ex: replaceInFile("\$_MYSQLHOST", " = '$host';", $hostfilename);
function replaceInFile($what, $with, $filename){
	$buffer = "";
	$fp = file($filename);
	foreach($fp as $line){
		//simbol \ yg pertama untuk ignore tanda $_MYSQL dan \ kedua untuk ignore \ pertama
		//simbol pembuka dan penutup / ... / | simbol awal ^.* dan akhir .*$ | /i option insensitive
		$buffer .= preg_replace("/\\".$what.".*$/", $what.$with, $line,-1);
		file_put_contents($filename, $buffer);
	}
}
//echo $newstring = preg_replace("/^.*host.*$/i", "", $string);