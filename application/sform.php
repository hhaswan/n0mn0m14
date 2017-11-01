<?php
require_once("config/View.php");
require_once("config/Connection.php");
$tablename = $_POST['table'];
$primary = $_POST['primary'];
$action = $_POST['action'];
$datetime=false;
$tempnilai = array();

if(isset($_POST['data'])){
  $data = $_POST['data']; /* [0]->[name]='kodebarang',[value]='abc' [1]->[name]='TglInput|t',[value]='2012-12-20' [2]->[name]='TglInput|j',[value]='21:56:30' */
  for($i = 0; $i < count($data); $i++){
	$data_ar=$data[$i]; 
	foreach($data_ar as $id => $nil){
		//id = nama
		if($id == 'name'){
			//cek inputan datetime
			$namatj = explode("|",$nil);
			if(count($namatj)>1){
			  $datetime = true;
			  if(!in_array($namatj[0],$nama)) $nama[]=$namatj[0];
			}
			else {
				$datetime=false;
				$nama[]=$nil;
				$nilaitj = '';
			}
		}
		//id = value
		else {
			if($datetime) {
				array_push($tempnilai,$nil);
				if(count($tempnilai) == 2) {
					$nilai[] = (implode(" ",$tempnilai));
					$tempnilai = array();
					$datetime = false;
				}
			}
			else {
				//cek data class autonumber
				$nilx = str_replace(',','',$nil);
				if(is_numeric($nilx)) $nil = $nilx;
				//$nilai[]= $nil;
				$nilai[]= mysql_real_escape_string($nil);
			}
		}
	}
  }
}

//update
if ($action == 'edit') {
	if(!empty($_POST['oldprimary'])) $oldprimary = $_POST['oldprimary'];
	else $oldprimary = '';
	$str_klausa = buatStringSet($primary,$nama,$nilai,$oldprimary);
	$sql = "update ".$tablename." set ". $str_klausa;
}

//input
else if ($action == 'input') {
	$str_nilai = buatStringNilai($nilai);
	$str_kolom = buatStringKolom($nama);
	$sql = "insert into $tablename ($str_kolom) values ($str_nilai)";
}

//delete
else if ($action == 'delete') {
	$nilai = $_REQUEST['iddelete'];
	$sql = "delete from ".$tablename." where ".$primary."='".$nilai."'";
}

$result = mysql_query($sql);
if($result) echo "Proses ". ucwords($action) ." Tabel $tablename Berhasil Diproses";
else echo "Proses ". ucwords($action) ." GAGAL Diproses. Pesan Error : ".mysql_error();
//echo "<p>$sql</p>";

?>
<script>
	$(function(){
		$(".container-isi").load("ptable.php?tablename=<?php echo $tablename ?>");
	})
</script>
