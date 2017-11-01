<?php
session_start();
require_once("Baseconfig.php");	//berisi $hostfilename dan $dbfilename
require_once($hostfilename);		//berisi variabel _MYSQLHOST| _MYSQLUSER | _MYSQLPASS
mysql_connect("$_MYSQLHOST","$_MYSQLUSER","$_MYSQLPASS");

require_once("Databasenomnom.php");	//berisi variabel _DBNOMNOM
mysql_select_db($_DBNOMNOM);

if(isset($_POST['menuid'])){
	$id = $_POST['menuid'];
	$caption = $_POST['captionval'];
	$sql = "update _menu set caption = '$caption' where id = $id";
	if(mysql_query($sql)) echo "1";
	else echo "0";
}

if(isset($_POST['act'])){
	$dbid = $_SESSION['dbid'];
	if($_POST['act'] == 'check'){
		$sql = "select id from _menu where id_dblisted = $dbid";
		$rs = mysql_query($sql);
		if(mysql_num_rows($rs) == 0) echo "check0";
		else echo "check1";
	}
	else if($_POST['act'] == 'del'){
		$sql = "delete from _menu where id_dblisted = $dbid";
		if(mysql_query($sql)){
			echo mysql_affected_rows(). " menu deleted !";
		}
		else echo "Delete Failed";
	}
	
	else if($_POST['act'] == '#listtable'){
		$sql = "SHOW FULL TABLES IN ".$_SESSION['dbname'];
		$rs = mysql_query($sql);
		$namakolom = "Tables_in_".$_SESSION['dbname'];
		while($data = mysql_fetch_assoc($rs)){
			if($data['Table_type'] == 'BASE TABLE') $tipe = "Table";
			else if($data['Table_type'] = 'VIEW') $tipe = "View";
			echo "<li class='list-group-item'><input type='text' value='...' class='form-control'/><span class='cedit'>Edit $tipe Alias</span> : $data[$namakolom] </li>";
		}
	}
	else if($_POST['act'] == '#listmenu'){
	  $sql0 = "select * from _menu where id_dblisted = $dbid and level=0 and id_parent = 0 order by order_number";
	  $rs0 = mysql_query($sql0);			
	//level parent utama sejajar home dan logout
	  if(mysql_num_rows($rs0) > 0) {
		while($data0 = mysql_fetch_assoc($rs0)){
		//editing mungkin dilakukan dengna memanfaatkan id_parent, js belum jalan
			echo "<li class='list-group-item'><input type='text' value='$data0[caption]' class='form-control'/><span class='cedit'>$data0[caption]</span>";
			$sql1 = "select * from _menu where id_parent = $data0[id] and level=1 order by order_number";
			$rs1 = mysql_query($sql1);
			if(mysql_num_rows($rs1) > 0) {
			  echo "<ul>";
			  while($data1 = mysql_fetch_assoc($rs1)){
				echo "<li class='list-group-item'><input type='text' value='$data1[caption]' class='form-control'/><span class='cedit'>$data1[caption]</span> : $data1[link_table]";
					$sql2 = "select * from _menu where id_parent = $data1[id] and level=2 order by order_number";
					$rs2 = mysql_query($sql2);
					if(mysql_num_rows($rs2) > 0) {
					echo "<ul>";
					 while($data2 = mysql_fetch_assoc($rs2)){
						echo "<li class='list-group-item'><input type='text' value='$data2[caption]' class='form-control'/><span class='cedit'>$data2[caption]</span> : $data2[link_table]</li>";
					 }
					 echo "</ul>";
					}
				echo "</li>";
			  }
			  echo "</ul>";
			}
			
			echo "</li>";
		}
	  }
	}
}
//insert ke table _menu
if(isset($_POST['listmenu'])){
	//print_r($_POST['listmenu']);
	/* */
	$z=0;
	$dbid = $_SESSION['dbid'];
	foreach($_POST['listmenu'] as $listmenu => $numbering){
		$numbers = explode(".",$numbering);
		$menus = explode(":",$listmenu);
		$level = $numbers[0]; $orderno = $numbers[1];
		$caption = trim($menus[0]);
		
		if(empty($menus[1])) $tablelink = "#";
		else $tablelink = trim($menus[1]);
		if($tablelink == "#"){
			$parentid = 0;
			$sql = "insert into _menu values (null,'$caption',$level,$orderno,$parentid,'$tablelink',$dbid)";
			if(mysql_query($sql)){
				$sql = "select id from _menu where caption = '$caption' and id_dblisted = $dbid";
				$rs = mysql_query($sql);
				$data = mysql_fetch_assoc($rs);
				$_SESSION['nextid'] = $data['id'];
				$z++;
			}
		}
		else{
			$parentid = $_SESSION['nextid'];
			$sql = "insert into _menu values (null,'$caption',$level,$orderno,$parentid,'$tablelink',$dbid)";
			if(mysql_query($sql)) $z++;
		}
	}
	echo $z. " menu executed";
	/**/
}
?>