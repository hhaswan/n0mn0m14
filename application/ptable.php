<?php
require_once("config/View.php");
require_once("config/Connection.php");
$tablename = "";
$primarycolname = "";
if(isset($_GET['tablename'])) {
	$tablename = $_GET['tablename'];
	$table = konversiGarisBawah($tablename);
	
	//bagian ini untuk pencarian
	if(isset($_GET['columnsearch'])){
		$column = $_GET['columnsearch'];
		if(!empty($_GET['key'])){
			$key = $_GET['key'];
			$d->searchKey($tablename,$column,$key);
		}
		else $d->selectTable($tablename);
	}
	else $d->selectTable($tablename);
	$num = mysql_num_fields($d->result);
	//echo $d->sql;
	echo "<div class='hasil'>"; 
	echo "<fieldset>";
	echo "<legend>Data $table </legend>";
	echo "<div>";
	echo "<table class='main'>";
	//header Tabel
	echo "<tr>";
	echo "<td>Action</td>";
	//sebaiknya semua tabel punya primary key, kalau tidak header0 harus diaktifkan
	$header0 = mysql_field_name($d->result, 0); 
	for ($i=0;$i<$num;$i++){
		$header = mysql_field_name($d->result, $i);
		$flag = mysql_field_flags($d->result, $i);
		echo "<td>";
		echo konversiGarisBawah($header);
		if(cekPrimary($flag)) $primarycolname=$header;
		else $primarycolname=$header0;
		echo "</td>";
	}
	echo "</tr>";
	//Isi tabel 
	
	while($data=mysql_fetch_array($d->result)) {
		/*
		<button type='button' id='edit' class='btn btn-info abcrud' data-target='#formModal' data-toggle='modal'>Edit</button>
		<button type='button' id='delete' class='btn btn-danger abcrud' data-target='#formModal' data-toggle='modal'>Hapus</button>
		*/
		//<span class = 'fa fa-refresh blue'></span>
		echo "<tr id=$data[$primarycolname]>";
		echo "<td>
		<span id='edit' class = 'fa fa-edit blue abcrud' data-target='#formModal' data-toggle='modal'></span>
		<span id='delete' class = 'fa fa-trash-o red abcrud' data-target='#formModal' data-toggle='modal'></span>
		<span id='view' class = 'fa fa-eye blue abcrud' data-target='#formModal' data-toggle='modal'></span>
		
		</td>";
		
		for($i=0;$i<$num;$i++) { 
			echo "<td>";
			$htmltext = htmlspecialchars($data[$i]);
			$maxchar = 100;
			echo $text = (strlen($htmltext) > $maxchar) ?  substr($htmltext,0,$maxchar)."<b> ...</b>" : $htmltext;
			echo "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
	echo "</div>";
	echo "</fieldset>";
  echo "</div>"; //tutup hasil 
}
else{ ?> 
	<p class='pmain'>Click menu above to show data</p>
	<p class='pmain'>
	<a href="../system/usrv.php" class="btn btn-danger">Change Host</a>
	<a href="../system/udb.php" class="btn btn-info">Set Database</a>
	<a href="../system/udbmenu.php" class="btn btn-success">Menu</a>
	</p>
<?php
}
?>
<script>
$('button.abcrud').click(actiondb);
$('span.abcrud').click(actiondb);

function removeClassTable() {
	$('.main').find('.sedang_diedit').removeClass();
}

function actiondb(){
	var action = $(this).attr('id');
	//ada action edit input delete
	removeClassTable();
	//edit dan input form diload pform kemudian sform
	//delete langsung ke sform
	if(action == 'input'){
		$.post("pform.php",{table: '<?php echo $tablename ?>',primary: '<?php echo $primarycolname ?>', action:action},function(output){
			$('#formModal').modal('show').html(output);
		});
	}
	else if(action == 'edit' || action == 'view'){
		var root = $(this).parent().parent(); //tr id=''
		root.children('td').addClass("sedang_diedit");
		var id = $(root).attr('id');
		$.post("pform.php",{table: '<?php echo $tablename ?>',primary: '<?php echo $primarycolname ?>', id: id, action:action},function(output){	
			$('#formModal').modal('show').html(output);
		});
	}
	//delete langsung diquery -> sform
	else if(action == 'delete'){
		var root = $(this).parent().parent(); //tr id=''
		var id = $(root).attr('id');
		//root.children('td').addClass("sedang_diedit");
		var status = confirm("Apakah anda yakin menghapus data "+id+"?");
		if(status) {
			$.post("sform.php",{table: '<?php echo $tablename ?>',primary: '<?php echo $primarycolname ?>', iddelete: id, action:action},function(output){
				$('#infoForm').html(output);
			});
		}
	}
	return false;
}

/* tambahan untuk pencarian */
$('#key').keyup(function(e){
	e.preventDefault();
	if(e.which == 13){
		//filterdata();
		var contentkey = $("#key").val(); 
		var colsearch = $("#colsearch").val(); 
		$.get("ptable.php",{tablename: '<?php echo $tablename; ?>', columnsearch:colsearch, key:contentkey},function(output){
		  $(".container-isi").html(output);
		});
	}
});
//$('#key').blur(filterdata);
function filterdata(){
	//var divloading = "<div id='loading' align='center'><img src='img/loadingbig.gif'/></div>";
}
</script>
<style>
span{font-size:1em; font-weight:bold;}
span.abcrud{font-size:1em;}
span.abcrud:hover{cursor:pointer; color: orange;}
.blue{color:#41a8e2;}
.red{color:#fd2e02;}
</style>
