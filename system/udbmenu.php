<?php
session_start();
if(!isset($_SESSION['dbname'])) header("location: udb.php");

?>
<style>
.example input{width:50%; display:none;}
a.bli{margin: 10px; color:red;}
#listtable{overflow-y:scroll;}
.cedit{background-color:grey;color:white;}
</style>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
	include_once("ui.php");
  ?>
  <link rel="stylesheet" href="../assets/css/style_drag.css"/>
  <script src="../assets/js/jquery-sortable.js"></script>
</head>
<body>
<div class="alert alert-danger" id="infomenu"></div>
<div class="container">
<div class="row">
	<?php
	include_once("uimenu.php");
	?>
	<div class="page-header">
		<a class="btn btn-danger pull-right" href="javascript:clearmenu()">Clear Menu</a>
		<div class="clearfix"></div>
	</div>
</div>
<div class="panel-group col-sm-5">
    <div class="panel panel-default">
      <div class="panel-heading">
          <h3 class="panel-title pull-left">Available Tables in DB <?php echo $_SESSION['dbname'] ?></h3>
		  <button class="btn btn-default pull-right" id="brefresh">Refresh</button>
		  <div class="clearfix"></div>
      </div>
      <div id="list1" class="panel-collapse">
        <ul id="listtable" class='list-group example'></ul>
		<div class="panel-footer result">Click Edit Alias to Edit</div>
      </div>
    </div>
</div>
<div class="panel-group col-sm-7" >
    <div class="panel panel-default">
      <div class="panel-heading">
          <h3 class="panel-title pull-left">Menu List <sup class="cedit"> *) Click to edit</sup></h3>
		  <button class="btn btn-default pull-right" id="bmenuadd">New Menu</button>
		  <div class="clearfix"></div>
      </div>
      <div id="list2" class="panel-collapse">
        <ul id="listmenu" class='list-group example'></ul>
        <div class="panel-footer result">
			<button id="badd" class="btn btn-info bsave">Save Menu</button>
        </div>
	  </div>
	</div>
</div>
</div>
</body>
<script>

function menuexist(){
	$("#infomenu").html("Please edit Menu List or <a href='../application'>Continue with existing menu</a>.");
	$(".bsave").prop("id","bedit");
}
function menunotexist(){
	$("#infomenu").html("Please setup menu");
	$(".bsave").prop("id","badd");
}
function deletemenu(){
	$.post("udbscript.php",{act:"del"},function(output){
		alert(output);
	});
}
function addmenu(){
$('ul#listmenu').each(function(){
		//var list = []; //number considered index
		var list = {};
		var urut=0;//urutparent=0 untuk parent utama
		$(this).find('li').each(function(){
			var urut = $(this).index()+1;
			var level = $(this).parents('ul').length-1;
			var text = $(this).contents().not($(this).children('ul')).text();
			list[text] = level+'.'+urut;
		});
		var amount = Object.keys(list).length;
		if( amount > 0) {
			$.post("udbscript.php",{listmenu: list},function(output){
				alert(output);
				menuexist();
			});
		}
		else alert("No menu available!");
		load_ul("#listmenu");
	});
}
function freeze(){
	$(".example").sortable("destroy");
	$("button").prop("disabled",true);
	$("#listtable,a.bli").css('pointer-events','none');
}
function load_li(el){
	$(el+' li:last span').click(function(){
		$(this).hide();
		$(this).parent().find("input").eq(0).show().select();
	});
	$(el+' li:last input').focusout(function(){
		$(this).parent().find("span").eq(0).text($(this).val()).show();	//ambil span terdekat
		$(this).hide();
	});
	$(el+' li:last a.bli').click(function(){
		$(this).parent().remove();
		return false;
	});
}
function load_ul(el){
	$(el).html(divloading);
	$.ajax({
		url: "udbscript.php",
		data: {act:el},
		type: 'POST',
	}).success(function(balikan) {
		$(el).html(balikan);
		$(el+' li span').click(function(){
			$(this).hide();
			$(this).parent().find("input").eq(0).show().select();
		});
		$(el+' li input').focusout(function(){
			$(this).parent().find("span").eq(0).text($(this).val()).show();
			$(this).hide();
		});
	}).error(function(error) {
		console.log(error);
	});
}

var divloading = "<div id='loading' align='center'><img src='../assets/images/loadingbig.gif'/></div>";
function clearmenu(){
	var stdelete = confirm("Are you sure to delete?");
	if(stdelete){
		deletemenu();
		load_ul("#listmenu");
		menunotexist();
	}
}
$(document).ready(function() {
/* always load table from chosen database */
load_ul("#listtable");
/* onload check menu exist or not*/
$.post("udbscript.php",{act:"check"},function(output){
	if(output == 'check1'){
		menuexist();
		load_ul("#listmenu");
	}
	else 
		menunotexist();
});
/**/
$("#brefresh").click(function(){
	load_ul("#listtable"); 
});

$(".bsave").click(function(e){
	var bsaveid = $(this).attr('id');
	if(bsaveid == 'bedit'){
		var stdelete = confirm("Menu will be changed?");
		if(stdelete){
			deletemenu();
			addmenu();
		}
	}
	else{ 
		addmenu();
	}
	
});

$("#bmenuadd").click(function(){
	//listtable tidak punya ul
	$("#listmenu").append("<li class='list-group-item'><input value='...' type='text' class='form-control'/><span class='cedit'>Edit Parent Menu </span><a class='bli' href=''><i class='fa fa-trash-o'></i></a><ul></ul></li>");
	load_li("#listmenu");
});
/* if onclick conflick with drag */
$("#listmenu").on("click", ".draggable", function(e){
     $(".example").append(e.target).sortable('refresh');
});
/**/
var oldContainer;
$("ul.example").sortable({
  group: 'nested',
  afterMove: function (placeholder, container) {
    if(oldContainer != container){
      if(oldContainer)
        oldContainer.el.removeClass("active");
      container.el.addClass("active");
      oldContainer = container;
    }
  },
  onDrop: function ($item, container, _super) {
    container.el.removeClass("active");
    _super($item, container);
  }
});
});
</script>
</html>