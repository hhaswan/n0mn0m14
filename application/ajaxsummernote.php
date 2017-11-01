<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>summernote - bs3fa4</title>
<?php
require_once("ui.php");
?>

</head>
<body>
<!-- Trigger the modal with a button -->
<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
	<form action="" method="post">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Header</h4>
      </div>
      <div class="modal-body">
			<textarea name="teks" required="required" class="summernote"></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-info" type="submit" name="bkirim">Kirim</button>
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
	</form>        
	</div>
  </div>
</div>

<?php
	if(isset($_POST['bkirim'])){
		echo $_POST['teks'];
	}
?>
</body>

</html>
<style>
.popover{z-index:1500;}
</style>
<!-- include summernote -->
<link rel="stylesheet" href="summernote/summernote.css"/>
<script type="text/javascript" src="summernote/summernote.js"></script>
<script type="text/javascript">
/* start summernote */
var ajaxfile = "ajaxgambarupload.php";
$('.summernote').summernote({
	//width:500,
	height: 200,
	placeholder: 'write here...',
	dialogsInBody: true,
	callbacks: {
		//image from local
		onImageUpload: function(files, editor, welEditable) {
			console.log(files[0]);	//json file info
			sendFile(files[0],editor,welEditable);
		}
	}
});

//.note-dialog .modal-dialog{ z-index:1050; }
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
			//window.alert('File request failed! Check the file url');
			console.log(error);
		});
	});
});

function sendFile(file,editor,welEditable) {
	data = new FormData();
	data.append("file", file);
	$.ajax({
		url: ajaxfile,
		data: data,
		type: 'POST',
		cache: false, contentType: false, processData: false,
		xhr: function() {
				var myXhr = $.ajaxSettings.xhr();
				if (myXhr.upload) myXhr.upload.addEventListener('progress', progressHandlingFunction, false);
				return myXhr;
		},
		success: function(balikan){  
			console.log(balikan);
			$('.summernote').summernote('editor.insertImage', balikan);
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

</script>
