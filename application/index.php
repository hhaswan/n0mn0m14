<?php
/* START LOGIN PART  */
if(!file_exists("config/Host.php")){
	echo "<script>
	alert('Continue to set your connection!');
	window.location.href='../system/usrv.php';
	</script>";
}
require_once("config/View.php");
require_once("config/Connection.php");
require_once("sslogin.php");

/*Old Version Menu - Name menu file list_dbname *
$list_menu_file = "list_menu/list_$_DBNAME.php";
require_once $list_menu_file;
/**/
?>
<!doctype>
<html>
<head>
	<meta charset="UTF-8"/>
	<title>NomNom DB v1.4</title>
	<link rel="shortcut icon" href="images/favicon.png"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />	
	<?php 
		include("ui.php");
	?>
	
<script>
var divloading = "<div id='loading' align='center'><img src='../assets/images/loadingbig.gif'/></div>";
$(document).ready(function(){
	//Perhatian! setiap button apapun diklik ini diset ulang lagi
	var tablename = undefined;
	setInterval(function(){
		window.location.href = 'sslogout.php';
	},30*60*1000);	//30 menit
	$('input.autonumber').autoNumeric("init"); //autoNumber for money
	$('.top-tooltip').tooltip(); //button hint
	/* START cookies */
	//menu untuk anchor langsung, menuli untuk menu list
	$('.menu a').click(function (e) {
		e.preventDefault();
		var tablename = $(this).attr('id');
		$.cookie('tablename',tablename);
		gettable($.cookie('tablename'));
	});
	gettable($.cookie('tablename'));	//jika table diakses tanpa melalui klik
	/* END cookies */
});

function gettable(tablename){
	$("li.dropdown").removeClass('active'); 	//menu
	$(".menu a").removeClass('active');			//sub menu
	$(".container-search").hide();
	$(".container-isi").html(divloading);
	//tablename dibuat undefined supaya home.php yang diload 
	if(tablename == 'home') tablename = undefined;
	
	//tampilkan filter dari tabel terpilih 
	if(tablename != null){
		$("#home").parent().removeClass('active');
		$("#"+tablename).addClass('active');
		$("#"+tablename).parent().parent().parent().addClass('active');
		$.post("ssearch.php",{tablename: tablename},function(output){
			$(".container-search").html(output).show();
		});
	}
	else $("#home").parent().addClass('active');
	
	//tampilkan hasil pencarian
	$.get("ptable.php",{tablename:tablename},function(output){
		$(".container-isi").html(output);
	});
	return false;
}
</script>
</head>
<body>
<nav class="navbar-inverse navbar-static-top no-margin">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="#"><img src="../assets/images/logo_thumb.png" style="height:100%"/></a>
			<button class="navbar-toggle" data-toggle="collapse" data-target="#tablemenu"><span class="icon-bar"></span><span class="icon-bar"></span></button>
		</div>
		<div class="collapse navbar-right navbar-collapse" id="tablemenu">
		<ul class="menu-top nav navbar-nav">
			<li class="menu"><a id="home" href="index.php"><i class='fa fa-fw fa-home'></i> Home</a></li>
<?php
/* New version - query menu from database */
$sql0 = "select m.*,d.db_name from _menu m, _dblisted d where d.id = m.id_dblisted and d.db_name = '$_DBNAME' and level=0 and id_parent = 0 order by order_number";				
$rs0 = mysql_query($sql0);
			
			//level parent utama sejajar home dan logout
			while($data0 = mysql_fetch_assoc($rs0)){
			echo "	
			<li class='dropdown'>
				<a href='$data0[link_table]' class='dropdown-toggle' data-toggle='dropdown'><i class='fa fa-fw fa-edit'></i>$data0[caption]<b class='caret'></b></a>
			";
			echo "<ul class='dropdown-menu multi-level'>";
$sql1 = "select * from _menu where id_parent = $data0[id] and level=1 order by order_number";
$rs1 = mysql_query($sql1);
				//level child yang menjadi bagian parent utama
				while($data1 = mysql_fetch_assoc($rs1)){
					echo "<li class='menu'><a id='$data1[link_table]' href='".md5($data1['link_table'])."'>".$data1['caption']."</a></li>";
				}
				echo "<li class='divider'></li>";
			echo "</ul>";
		echo "</li>";
				}
/* */
?>
<?php
				/* Old version - list_menu *
				foreach($list_menu as $name => $caption){
				?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class='fa fa-fw fa-edit'></i><?php echo $caption; ?><b class="caret"></b></a>
					<ul class="dropdown-menu multi-level">
						<?php
						foreach($$name as $i => $tb_name){
							if(substr($tb_name,-1,1)== '-') echo "<li style='padding:10px 0;text-align:center;background:#eeeefe;'>$tb_name</li>";
							else echo "<li class='menu'><a id='$tb_name' href='".md5($tb_name)."'>".konversiGarisBawah($tb_name)."</a> </li>";
						}
						?>
						<li class='divider'></li>
					</ul>
				</li>
				<?php
				}
				/* */
				?>
			<li class=""><a id="home" href="sslogout.php"><i class='fa fa-fw fa-sign-out'></i> Logout</a></li>  
		</ul>
		
		</div>
	</div>
</nav>
<div class="col-sm-12">
<!-- START Table Database -->
<section>
    <div class='ptable' align="center">
		<div class='container-search clearfix'></div>
		<div class="container-isi"></div>
	</div> 
    <div class="alert alert-danger" id="infoForm">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        Show hint of data transaction
    </div>
</section>
<!-- END Table Database -->
</div>
<!-- START Modal Form pform.php -->
<div class="modal fade" id="formModal"></div>
</body>
</html>