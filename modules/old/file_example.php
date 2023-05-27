<?php

$form = "";
$path = "";

require_once("file.php");

$fileUpload = new fileUpload($form);
if ($fileUpload) {
	$fileUpload->setPath(dirname(__file__)."/".$path);
	if ($fileUpload->check()) {
		$fileUpload->save();
		echo $fileUpload->getFilename().$fileUpload->getExtension();
	}
} else {
	echo "error";
}
?>