<?php
/**
 * Request image
 * 
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			22nd May 2010
 * @last-modified	22nd May 2010
 * @version			1.0
 * @package 		modules
 */
/* ----------------------------------------
 * Change log:
v1.0 - 22nd May 2010
Created
 */

 

$image_url = get_url("images");

$image = new image(DB_PREFIX, $db);
$image_real_path = BASE_DIR . USER_IMAGE_DIR . $image->get_image_real_path($user->logged_in(), $image_url);
if (!$image_real_path) {
	header("HTTP/1.1 404 Not Found");
	die();
}

$extension = file_upload::extension($image_real_path);

if ($extension == '.jpg') {
	header("Content-type: image/jpeg");
} elseif($extension == '.png') {
	header("Content-type: image/png");
} elseif($extension == '.gif') {
	header("Content-type: image/gif");
}
//header("Access-Control-Allow-Origin: ".substr(ORIG_ROOT, 0, strlen(ORIG_ROOT)-1));
readfile($image_real_path);
?>