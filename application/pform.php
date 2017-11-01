<!doctype>
<?php
error_reporting(E_ALL  & ~E_NOTICE);
ini_set('display_errors', 1);
//include_once("ui.php");
require_once("config/View.php");
require_once("config/Connection.php");
$tablename = $_POST['table'];
$db = $_DBNAME;
$id = '';
$sdisabled = false;
$action = $_POST['action'];
if($action == 'input') {
	$d->selectTable($tablename);
	$bid = 'binsert'; $bval = 'Input'; $bname = 'binsert';
}
else if($action == 'edit') {
	$primary = $_POST['primary'];
	$id = $_POST['id'];
	$d->selectTable($tablename,"where $primary='$id'");	
	$bid = 'bupdate'; $bval = 'Update'; $bname= 'bupdate';
}
else if($action == 'view') {
	$primary = $_POST['primary'];
	$id = $_POST['id'];
	$d->selectTable($tablename,"where $primary='$id'");	
	$sdisabled = true;
}

$data = mysql_fetch_array($d->result);	
$num = mysql_num_fields($d->result);
$curdate = date("Y-m-d"); // tanggal hari ini
$curtime = date("H:i");

echo "<div class='modal-dialog modal-lg'>
    <div class='modal-content'>";
echo "<form id='fdata' method='post' action='#' class='form-horizontal'>";
echo "<div class='modal-header'>
        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
        <h4 class='modal-title' id='myModalLabel' class='heading judul'>Data ".konversiGarisBawah($tablename)."</h4>
      </div>";

/*START MODAL BODY*/
echo "<div class='modal-body'>";
	for ($i=0;$i<$num;$i++) {
	  $columnname = mysql_field_name($d->result, $i);
	  $length = mysql_field_len($d->result, $i);	  	  
	  //urus pattern untuk inputan
	  $pattern = "maxlength='$length'";
	  $patternnumber = "pattern='[0-9]{1,$length}'";
	  $patternemail = "pattern='/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,4}$/'";	  
	  //urus flag untuk tipe data
	  $flag = mysql_field_flags($d->result, $i); 
	  $ff=explode(' ',$flag);
	  $fprimary=$fauto=$fenum=$ffile= false; $fforeign = $fnotnull = $frequired = '';
	  foreach($ff as $key=>$fval) {
		if($fval == 'multiple_key')
			$fforeign = $fval;
		else if($fval == 'not_null') {
			$fnotnull = "<span class='cnotnull'>*</span>";
			$frequired = "required = 'required'";
		}		
		else if($fval == 'enum')
			$fenum = true;
		else if($fval == 'primary_key')
			$fprimary = true;
		else if($fval == 'auto_increment')
			$fauto = true;
		}
		
		$fc=explode('_',$columnname);
		if(count($fc)>1){
		//jika kolom diawali kata file maka tipe input dibuat file
			if($fc[0]=='file') $ffile = true;
		}
	  
	  if($fprimary) {
	    echo "<div class='form-group'>";
		/* START label primary key */
		echo "<label class='col-sm-2 control-label'>". konversiGarisBawah($columnname) ."$fnotnull <span class='smallnote'>Dblclick To Edit</span></label>";
		/* END label primary key */
		echo "<div class='col-sm-10'>";
		if($action == 'input') {
		  if($fauto) {
			$autoid = $d->generateId($tablename,$columnname);
			echo "<input class='primary form-control ' type='text' readonly name='$columnname' value='$autoid' $frequired $pattern/>";
			}
		  else
		    echo "<input class='primary form-control ' type='text' name='$columnname' value='' $frequired $pattern/>";
		}
		else {
			echo "<input class='primary form-control ' type='text' readonly name='$columnname' value='$data[$i]' $frequired $pattern/>";
		}
		echo "</div>";
	  echo "</div>";
	  }
	  //urus nonprimary
	  else {
		if($sdisabled) $frequired .= " disabled = 'disabled'";
		if($action == 'input') $val = '';
		else $val = $data[$i];
		$columnlabel = konversiGarisBawah($columnname);
		echo "<div class='form-group'>";
		
		/*START LABEL Nonprimary */
		echo "<label class='col-sm-2 control-label'>$columnlabel $fnotnull</label>";
		/*EDN LABEL Nonprimary */
		
		/*START INPUT Nonprimary */
		echo "<div class='col-sm-10'>";
		$type  = mysql_field_type($d->result, $i); //string, integer, blob, real		
		//urus foreign key	
		if(!empty($fforeign)){
			//cek berapa banyak foreign key dari tabel yang sekarang
			$sqlf = "SELECT * FROM `information_schema`.`KEY_COLUMN_USAGE` WHERE TABLE_NAME = '$tablename' AND TABLE_SCHEMA = '$db' AND COLUMN_NAME = '$columnname' AND TABLE_SCHEMA = REFERENCED_TABLE_SCHEMA";
			$resultf = mysql_query($sqlf);
			$dataf = mysql_fetch_assoc($resultf);
			$tablereferenced = $dataf['REFERENCED_TABLE_NAME'];
			$columnreferenced = $dataf['REFERENCED_COLUMN_NAME']; //kolom di primary
			$sqlr = "SELECT * FROM $tablereferenced;"; 
			$resultr = mysql_query($sqlr);
			
			//cek no kolom setelah foreign key 
			$jmlkolom = mysql_num_fields($resultr); 
			for($j = 0; $j < $jmlkolom; $j++) { 
			  $namakolom = mysql_field_name($resultr,$j);
			  if($namakolom == $columnreferenced) {
				$nextcolumn = $j+1;
				break;
			  }
			}
			//akhir pengecekan 
			echo "<select class='form-control select' data-live-search='true' name='$columnname' $frequired>";
			echo "<option value=''>Please select...</option>";
			while ($datar = mysql_fetch_array($resultr)) {
				$selected = '';
				if($datar[$columnreferenced] == $val) $selected = 'selected';
				echo "<option $selected value='$datar[$columnreferenced]'>".$datar[$nextcolumn]."</option>";
			}
			echo "</select>";
		}
		//urus enum
		else if ($fenum) {
			$sql = "show columns from $tablename where Field = '$columnname'"; // kolom Type
			//$sql = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$tablename' AND COLUMN_NAME = '$columnname'";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			$enumList = explode(",", str_replace("'", "", substr($row['Type'], 5, (strlen($row['Type'])-6))));
			echo "<select class='form-control' name='$columnname' $frequired>";
			echo "<option value=''>Please select...</option>";
			foreach($enumList as $valenum){
				$selected = '';
				if($val == $valenum) $selected='selected';
				else $selected = '';
				echo "<option $selected value='$valenum'>$valenum</option>";
			}
			echo "</select>";
		}
		//urus tipe data
		else if ($type=='date'){
			//if($action == 'input'){$val=$curdate;}
			echo "<input class='form-control' type='date' min='' max='' name='$columnname' value='$val' $frequired />";
			echo "<span class='cnote'><input type='checkbox' class='cnull'/>Today Date</span>";
			//echo "<div id='$columnname' class='col-sm-8 bfh-datepicker' data-format='y-m-d' data-min='01-15-2013' data-max='today' data-close='true'></div>";
		}
		else if ($type=='time'){
			//if($action == 'input'){$val=$curtime;}
			echo "<input class='form-control' type='time' min='' max='' name='$columnname' value='$val' $frequired />";
			echo "<span class='cnote'><input type='checkbox' class='cnull'/>Now Time</span>";
			//echo "<div id='$columnname' class='col-sm-8 bfh-datepicker' data-format='y-m-d' data-min='01-15-2013' data-max='today' data-close='true'></div>";
		}
		else if ($type=='datetime'){
		  //if($action == 'input'){$jam=$curtime; $tgl=$curdate;}
		  if($action == 'edit') {$waktu = explode(" ",$val);$tgl = $waktu[0]; $jam = $waktu[1];}	
			echo "<input class='setengah form-control' type='date' name='$columnname|t' value='$tgl' $frequired />
			<input class='setengah form-control' type='time' name='$columnname|j' value='$jam' $frequired />";
			echo "<span class='cnote'><input type='checkbox' class='cnull'/>Now</span>";
		}
		else if ($type=='int'){
			//echo "<input class='form-control' type='number' name='$columnname' value='$val' $frequired />";
			echo "<input class='form-control autonumber' type='text' name='$columnname' data-o-ride='8,0' value='$val' $frequired />";
		}
		else if ($type=='real'){
			//echo "<input class='form-control' type='number' name='$columnname' value='$val' $frequired />";
			echo "<input class='form-control autonumber' type='text' name='$columnname' data-o-ride='6,2' value='$val' $frequired />";
		}
		else if($type=='blob'){
			echo "<textarea $frequired class='form-control summernote' name='$columnname'>$val</textarea>";
		}
		//type file 
		else if($ffile) {
			echo "<input class='nama_gambar form-control' name='$columnname' value='$val' type='text' id='$columnname' placeholder='Masukkan file'/>";
			echo "<input id='f$columnname' type='file' accept='image/*' name='$columnname' style='display:none;' onchange='return ajaxFileUpload(this);'/>";
			echo "<img id='loading' src='image/loading.gif' style='display:none;'/>";
		}
		else {
			echo "<input type='text' class='form-control' name='$columnname' value='$val' $frequired $pattern/>";
		}
		
		$sqlc = "SELECT COLUMN_COMMENT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME = '$tablename' AND COLUMN_NAME = '$columnname'";
		$resultc = mysql_query($sqlc);
		$datac = mysql_fetch_assoc($resultc);
		if($datac['COLUMN_COMMENT'] != '') echo "<span class='cnote'>".$datac['COLUMN_COMMENT']."</span>";
		
		echo "</div>"; /* END INPUT Nonprimary */
		echo "</div>"; /* END FORM GROUP*/
	  }
	}
	echo "</div>";
	/*END MODAL BODY*/
	
	echo "<div class='modal-footer'>";
	if(!$sdisabled) 
		echo "<button type='submit' class='btn btn-info' name='$bname' id='$bid'>$bval </button>";
    echo "<button class='btn btn-default' data-dismiss='modal'>Close</button>";
    echo "</div>";
	echo "</form>";
	echo "</div> 
  </div>";
?>
<style>
.popover{z-index:1500;}
.cnotnull{color:red;}
.cnote{color:#b00;font-weight:normal;}
input.setengah{width:50%;float:left;}
</style>
<!-- include summernote -->
<link rel="stylesheet" href="summernote/summernote.css"/>
<script type="text/javascript" src="summernote/summernote.js"></script>
<script type="text/javascript">
//onclick input di null kan - tidak terpakai
$(".cnull").click(function(){
	thatdate = $(this).parent().parent().find('input[type="date"]');//prop("tagName"); //get name of element
	thattime = $(this).parent().parent().find('input[type="time"]');
	thatdate.val('<?php echo $curdate?>');
	thattime.val('<?php echo $curtime?>');
	
	//alert(that);
});

/* start summernote */
//https://pmcdeadline2.files.wordpress.com/2016/07/logo-tv-logo.png
var ajaxfile = "ajaxgambarupload.php";
$('.summernote').summernote(
{
	//width:500,
	height: 200,
	placeholder: 'write here...',
	dialogsInBody: true,
	dialogsFade: true,
	callbacks: {
		//image from local
		onImageUpload: function(files, editor, $editable) {
			for (var i = files.length - 1; i >= 0; i--) {
				console.log(files[i]);	//json file info
				sendFile(files[i], this);
			}
		}
	}
}
);

//image from url
$('button[data-original-title="Picture"]').click(function(){
	$('.modal-dialog .note-image-btn').one('click', function(e) {
		// Get Image URL text
		var imageUrl = $('.modal-dialog .note-image-url').val();
		$.ajax({
			url: ajaxfile,
			data: "url="+imageUrl,
			type: "POST",
			dataType: 'json'
		}).success(function(data) {
			if (typeof data[0] === 'string') {	//data berupa array
				console.log(data[0]);
				$('img[src="'+imageUrl+'"]').attr('src', data); //replace url with localurl
			} else {
				window.alert('Download failed');
			}
		}).error(function(error) {
			window.alert('File request failed! Check the file url');
		});
	});
});

function sendFile(file,el) {
	data = new FormData();
	data.append("file", file);
	$.ajax({
		url: ajaxfile,
		data: data,
		type: 'POST',
		cache: false, contentType: false, processData: false,
		//enctype: 'multipart/form-data',
		/* Progress bar *
		xhr: function() {
				var myXhr = $.ajaxSettings.xhr();
				if (myXhr.upload) myXhr.upload.addEventListener('progress', progressHandlingFunction, false);
				return myXhr;
		},
		/* */
		success: function(balikan){
			//$('.summernote').summernote('editor.insertImage', balikan);
			$(el).summernote('editor.insertImage', balikan);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			window.alert(textStatus+" "+errorThrown);
		}
	});
}
// update <progress></progress>
function progressHandlingFunction(e){
	if(e.lengthComputable){
		$('progress').attr({value:e.loaded, max:e.total});
		// reset progress on complete
		if (e.loaded == e.total) {
			$('progress').attr('value','0.0');
		}
	}
}
/* end summernote */

//autoNumber for money
$('input.autonumber').autoNumeric('init');
oldprimary = '';

$(".primary").dblclick(function(){
	//alert("Hati-hati mengubah kunci utama!!");
	var konfirmasi = confirm("Kunci utama akan diubah!! Apakah anda yakin?");
	if(konfirmasi) {
		oldprimary = $(this).val();
		$(this).prop('readonly',false);
		$(this).focus();
	}
});

$("#fdata").submit(function(){
	var data = $(this).serializeArray();
	$.post("sform.php",{table: '<?php echo $tablename ?>',primary: '<?php echo $primary ?>', oldprimary: oldprimary, data: data, action: '<?php echo $action ?>'},function(output){
		$("#infoForm").html(output);
	});
	$('#formModal').modal('hide');
	return false;
});

/* upload awalan file  */
$('.nama_gambar').click(function(){
	$(this).next().trigger('click');
	var name = $(this).attr("name");
	var namer = name.split("_");
	//tarik keluar tulisan file dari namer
	namer.splice(0,1);
	var filename = namer.join("_")+"_"+$(".primary").val()+".jpg";
	$(this).val(filename);
});

function ajaxFileUpload(elm){
	var kolom = $(elm).attr("name");
	var id = $(".primary").val();
	that = $(elm).next(); 	//merujuk ke img loading
	that
	.ajaxStart(function(){
		that.show();
	})
	.ajaxComplete(function(){
		that.hide();
	});
	$.ajaxFileUpload
	(
		{
			url:'ajaxfileupload.php?kolom='+kolom+'&id='+id,
			secureuri:false,
			fileElementId:'f'+kolom,//input tipe file harus pakai f+variabel (tanpa string f tdk terbaca di ajaxfileupload)
			dataType: 'json',
			data:{name:'logan', id:'id'},
			success: function (data, status){
				if(typeof(data.error) != 'undefined')
				{
					if(data.error != '')
					{
						alert(data.error);
					}else
					{
						alert(data.msg);
					}
				}
				//alert("File uploaded");
			},
			error: function (data, status, e){
				alert(e);
			}
		}
	)
	return false;
}
/* End upload dengan kolom file*/
</script>