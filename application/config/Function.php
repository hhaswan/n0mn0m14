<?php
 * @copyright	Copyright (C) 2015 Hardiknas a.k.a ardhedefourteenz, All rights reserved.
 * @author		Hardiknas <ardhea014defourteenz@gmail.com>
 */

//begin piAntiInject
function piAntiInject($data){
	@$filter_sql = stripslashes(strip_tags(htmlspecialchars($data,ENT_QUOTES)));
	@$filter_sql->$mysqli->real_escape_string;
	return $filter_sql;
}
//end piAntiInject

//fungsi identifikasi browser
function cekBrowser() {
	if	(strpos($_SERVER['HTTP_USER_AGENT'], 'Netscape')){
		$browser = 'Netscape';
	} else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')){
		$browser = 'Firefox';
	} else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')){
		$browser = 'Chrome';
	} else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')){
		$browser = 'Opera';
	} else if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')){
		$browser = 'Internet Explorer';
	} else  {
		$browser = 'Other';
	}
	return $browser;
}

//tanggal indo
function tanggalID($tgl){
	$tanggal = substr($tgl,8,2);
	$bulan = piMonthID(substr($tgl,5,2));
	$tahun = substr($tgl,0,4);
	return $tanggal.' '.$bulan.' '.$tahun;		 
}

//fungsi nama bulan
function bulanID($bln){
	switch ($bln){
		case 1: return "Januari";break;
		case 2: return "Februari";break;
		case 3: return "Maret";break;
		case 4: return "April";break;
		case 5: return "Mei";break;
		case 6: return "Juni";break;
		case 7: return "Juli";break;
		case 8: return "Agustus";break;
		case 9: return "September";break;
		case 10: return "Oktober";break;
		case 11:	return "November";break;
		case 12: return "Desember";break;
	}
}

//fungsi nama hari
function hariID($x){
	$hari = array("Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu");
	return $hari[$x];
}


//fungsi hitung umur d-m-Y;
function getUmur($tgl_lahir, $tgl_sekarang){
   list($thn_lahir, $bln_lahir, $tgl_lahir) = explode("-",$tgl_lahir);
   list($tahun_today, $bulan_today, $tanggal_today) = explode("-", $tgl_sekarang);
   
   $harilahir = gregoriantojd($bln_lahir,$tgl_lahir,$thn_lahir);
   //menghitung jumlah hari sejak tahun 0 masehi
   
	$hariini = gregoriantojd($bulan_today,$tanggal_today,$tahun_today);
	//menghitung jumlah hari sejak tahun 0 masehi
	
	$umur = $hariini-$harilahir;
	//menghitung selisih hari antara tanggal sekarang dengan tanggal lahir
	
	$tahun = $umur/365;
	//menghitung usia tahun
	
	$sisa = $umur%365;
	//sisa pembagian dari tahun untuk menghitung bulan
	
	$bulan = $sisa/30;
	//menghitung usia bulan
	
	$hari = $sisa%30;
	//menghitung sisa hari
	
	$rs_usia['y'] = floor($tahun);
	$rs_usia['m'] = floor($bulan);
	$rs_usia['d'] = floor($hari);

	return $rs_usia;	
	//return floor($tahun)." Tahun, ".floor($bulan)." Bulan, $hari Hari";
}

function activityLogs($user,$ip,$host,$agent,$activity) {
	global $mysqli;
	piSQL('ADD','pi_activitylog',"piUname='$user', piIp='$ip', piHost='$host', piAgent='$agent',piActivity='$activity'");
}

function activityLogs($user,$ip,$host,$agent,$activity) {
	global $mysqli;
	piSQL('ADD','pi_activitylog_pendaftar',"piUname='$user', piIp='$ip', piHost='$host', piAgent='$agent',piActivity='$activity'");
}

//fungsi cek sesi
function cekSession($ssi) {
	global $mysqli;
	$ip = $_SERVER['REMOTE_ADDR'];
	$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$s = piSQL('SHOW','user','uSesi',"uUname='$ssi' && 1=1")->fetch_array();
	if ($s['uSesi'] != $_SESSION['piSesi']) {
		ActivityLogs($_SESSION['piUname'],$ip,$hostname,cekBrowser(),'Logout');
		session_destroy();
		echo '<meta http-equiv="refresh" content="0; url=index.php">';
	}
}
?>