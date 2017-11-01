<?php
$foldergambar = "../file_upload";
$error = "";
$msg = "";
$kolom = $_GET['kolom']; //file_portofolio_1
$id = $_GET['id'];
$filex = explode("_",$kolom);
array_shift($filex);
$filename = implode("_",$filex)."_".$id.".jpg";
$fileelement = $kolom;
//$fileke = substr($kolom,-1); 		//terbatas 1 digit 1-9 saja - belum terpakai
//$fileelement = 'file';

if(!empty($_FILES[$fileelement]['error'])){
	switch($_FILES[$fileelement]['error']){
		case '1':
			$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
			break;
		case '2':
			$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
			break;
		case '3':
			$error = 'The uploaded file was only partially uploaded';
			break;
		case '4':
			$error = 'No file was uploaded.';
			break;
		case '6':
			$error = 'Missing a temporary folder';
			break;
		case '7':
			$error = 'Failed to write file to disk';
			break;
		case '8':
			$error = 'File upload stopped by extension';
			break;
		case '999':
		default:
			$error = 'No error code avaiable';
	}
}
else if(empty($_FILES[$fileelement]['tmp_name']) || $_FILES[$fileelement]['tmp_name'] == 'none'){
	$error = 'Element File '. $fileelement .' Not recognized ';
}
else{
	//ori properties
	$targetori = $foldergambar."/".$filename;
	//thumb properties
	$thumbwidth = 100;
	$targetthumb = $foldergambar."/thumbs/".$filename;
	
	require_once("../system/SimpleImage.php");
	$filethumb = store_resized_image($fileelement,$targetthumb,$thumbwidth);
	$fileori = store_image($fileelement,$targetori);
	$msg = "File $fileori and $filethumb berhasil disimpan";
	//$msg .= " File Name: " . $_FILES[$fileelement]['name'] . ", ";
	//$msg .= " File Size: " . @filesize($_FILES[$fileelement]['tmp_name']);
	//for security reason, we force to remove all uploaded file
	@unlink($_FILES[$fileelement]);	
}

echo "{";
echo				"error: '" . $error . "',\n";
echo				"msg: '" . $msg . "'\n";
echo "}";

function store_image($html_element_name, $target){
	$target_file = $target;
	$image = new SimpleImage();
	$image->load($_FILES[$html_element_name]['tmp_name']);
	$image->save($target_file);
	return $target_file;
}

function store_resized_image($html_element_name, $target, $new_img_width) {
	$target_file = $target;
	$image = new SimpleImage();
	$image->load($_FILES[$html_element_name]['tmp_name']);
	$image->resizeToWidth($new_img_width);
	$image->save($target_file);
	return $target_file;
}

?>