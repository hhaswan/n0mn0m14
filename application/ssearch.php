<?php
require_once("config/View.php");
require_once("config/Connection.php");
$tablename = $_POST['tablename'];
$d->selectTable($tablename);
//echo $d->sql;
$num = mysql_num_fields($d->result);
echo "
<button type='button' id='input' class='btn btn-info abcrud' data-target='#formModal' data-toggle='modal'>Tambah Data</button>"; 
echo "<button class='btn btn-default' onclick=PrintElem('.container-isi')>Print</button>";
echo "<select class='form-control' id='colsearch' title='Pilih kolom pencarian'>";
for($i=0;$i<$num;$i++){
	if($i == 1) $selected = 'selected';
	else $selected = '';
	echo "<option $selected value=".mysql_field_name($d->result,$i).">".konversiGarisBawah(mysql_field_name($d->result,$i))."</option>";
}
echo "</select>";
echo "<input id='key' class='form-control' type='search' placeholder='Cari $tablename ...'/>";
?>

<script>
function PrintElem(elem){
	Popup($(elem).html());
	//Popup($('<div/>').append($(elem).clone()).html());
}

function Popup(data) 
{
    var mywindow = window.open('', 'Print A Page ', 'height=400,width=600');
    mywindow.document.write('<html><head><title>Page#</title>');
    
	//chrome: tidak diperlukan, firefox: tidak ngefek
	//mywindow.document.write('<link rel="stylesheet" href="../assets/css/bootstrap.min.css"/>');
	mywindow.document.write('<link href="../assets/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>');
	mywindow.document.write('<link href="../assets/css/style.css" rel="stylesheet"/>');
   
    mywindow.document.write('</head><body >');
    mywindow.document.write(data);
    mywindow.document.write('</body></html>');
	mywindow.document.close();
	myDelay = setInterval(checkReadyState,10);
	function checkReadyState(){
		if(mywindow.document.readyState == "complete") {
			clearInterval(myDelay);
			mywindow.focus();
			mywindow.print();
			mywindow.close();
		}
	}
    return true;
}
</script> 
