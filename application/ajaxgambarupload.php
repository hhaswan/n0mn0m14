<?php
$foldergambar = "../file_upload";
//manage local input
if ($_FILES['file']['name']) {
	if (!$_FILES['file']['error']) {
		$name = md5(rand(100, 200));
		$namafile = $_FILES['file']['name'];
		$ext = explode('.', $namafile);
		$filename = $name . '.' . $ext[1];
		$destination = "$foldergambar/$filename";
		$location = $_FILES["file"]["tmp_name"];
		move_uploaded_file($location, $destination);
		echo "$foldergambar/". $filename;
	}
	else
	{
	  echo  $fileresponse = 'Ooops!  Your upload triggered the following error:  '.$_FILES['file']['error'];
	}
}
//manage link input
else if ($_POST['url']) {
    $image = file_get_contents($_POST['url']);
	$namafile = parse_url($_POST['url'], PHP_URL_PATH);

	$name = md5(rand(100, 200));
	$ext = explode('.', $namafile);
	$filename = $name.'.'.$ext[1];
	
    if ($image) {
        // Put downloaded image on your server
        $file = fopen("$foldergambar/$filename", "w");
        fwrite($file, $image);
        fclose($file);
    }

    /* To avoid bool to string conversion during output response needs to be sent as JSON */
    $urlresponse = ($image) ? array("$foldergambar/$filename") : array(false);
    echo json_encode($urlresponse);
}
?>