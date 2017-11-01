<?php
function cekPrimary($flag) {
	$sprimary = false;
	$ff=explode(' ',$flag);
	foreach($ff as $key=>$val) {
		if($val == 'primary_key') $sprimary = true;
	}
	return $sprimary;
}

//memberikan spasi antara dua kata pada nama field yang capital
function konversiHurufPertama($labelfield) {
	$hasil= preg_replace("/(?<=[a-zA-Z])(?=[A-Z])/", " ", $labelfield);
	return ucwords($hasil);
}

//mengganti _ dengan spasi
function konversiGarisBawah($labelfield) {
	$hasil = preg_replace("/_/", " ", $labelfield);
	return ucwords($hasil);
}
//insert
//fungsi mengambil nilai dari attribut name 
function buatStringNilai($data){
	$nilainya='';
	for($i = 0;$i < count($data); $i++){
	$tempnilai = $data[$i];
		if (trim($tempnilai) != '')
			$nilainya .= "'".$tempnilai."',";
		else 
			$nilainya .= "null,";
	}
	$nilainya = substr($nilainya,0,strlen($nilainya) - 1);
	return $nilainya;
}
//update
//fungsi mengambil name yang juga merupakan nama kolom 
function buatStringKolom($data){
	$nilainya='';
	for($i = 0;$i < count($data); $i++){
		$nilainya .="".$data[$i].",";
	}
	$nilainya = substr($nilainya,0,strlen($nilainya) - 1);
	return $nilainya;
}
//fungsi mengambil kolom dan nilanya beserta klausa dan primary juga diupdate
function buatStringSet($primary,$nama,$nilai,$oldprimary){
	$hasil= '';
	for($i = 0; $i < count($nama); $i++){
	$tempnilai = $nilai[$i];
		if($nama[$i] == $primary) {
			$nama_id = $nama[$i]; 
			if(empty($oldprimary)) $nilai_id = $tempnilai;
			else $nilai_id = $oldprimary; 
		}
		if (trim($tempnilai) != '')
			$hasil .= $nama[$i]."= '".$tempnilai."',";
		else 
			$hasil .= $nama[$i]."= null,";
	}
	$hasil = substr($hasil,0,strlen($hasil) - 1);
	$hasil .=" where ".$nama_id."= '".$nilai_id."'";	
	return $hasil;
}
function buat_listId($nama,$tabel,$id,$id_nilai = 0,$id_tampil = 0){
	$sql = "select * from ".$tabel;
	$sql_exe = mysql_query($sql);
	if($sql_exe){
		$hasil .= "<select name=".$nama.">";
		$baris = mysql_num_rows($sql_exe);
		if( $baris > 0 ) {
		while ($data = mysql_fetch_row($sql_exe)){
			if($id == $data[$id_nilai]){
				$selected = "selected";
				}
			else {
				$selected = "";
				}
			$hasil .="<option value=".$data[$id_nilai]." ".$selected." >".$data[$id_tampil]."</option>";
			}	
		  }
		$hasil .="</select>";
	}	
	return $hasil;		
}

function startcount() {
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$start = $time;
}

function finishcount() {
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$total_time = round(($finish - $start), 4);
	echo 'Page generated in '.$total_time.' seconds.';
}
?>