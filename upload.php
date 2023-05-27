<?php if (!defined('BASE_DIR')) die('No direct script access allowed');
/**
 * Upload
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

if (!$user->logged_in()) {
	return;
}

// settings
$relative_upload_path = $user->logged_in();
$upload_path = BASE_DIR.USER_IMAGE_DIR.$relative_upload_path;
$max_height = 1200;
$max_width = 1600;
$max_upload_size = 2*1024*1024;
$max_usage_size = 100*1024*1024;
$error = array();
$success = array();

$files = count($_FILES["files"]['name']);
$image = new image(DB_PREFIX, $db);
// count upload file sent from client and check so not exceeding space usage
if($files>0 && $image->usage($user->logged_in()) < $max_usage_size) : 
	// create map 
	if(!file_exists($upload_path)) {
		mkdir($upload_path, 0777);
		chmod($upload_path, 0777);
	}

	for($i=0; $i < count($files); $i++) :
		// info
		$temp_name = $_FILES["files"]['tmp_name'][$i];
		$filename = basename($_FILES["files"]['name'][$i]);
		$size = $_FILES["files"]['size'][$i];
		$type = $_FILES["files"]['type'][$i];
		$extension = file_upload::extension($filename);
		
		// allowed extension
		if (!in_array($extension,array('.jpg', '.png', '.gif'))) {
			$error[] = "Felaktig filtyp, endast JPG, PNG eller GIF är tillåten.";
			continue;
		}
		
		// max upload size
		if ($size > $max_upload_size) {
			$error[] = "Förstor fil, max storlek är ".($max_upload_size/1024)." KB.";
			continue; 
		}
		// checksum
		if ($image->get_checksum($user->logged_in(), md5(file_get_contents($temp_name)))) {
			$error[] = "En liknande fil finns redan uppladdad.";
			continue;
		}
		
		// remove extention
		$filename = substr($filename,0,-strlen($extension));
		$obj_url = new url($filename);
		$filename = $obj_url->file();
		
		// Unique filename
		if (file_exists($upload_path."/".$filename.$extension)) {
			$number = 1;
			while (file_exists($upload_path."/".$filename.$number.$extension)){
				$number++;
			}
			$filename .= $number;
		}
		
		// resize
		list($width, $height) = getimagesize($temp_name);
		
		if ($width > $max_width || $height > $max_height) {
			$newWidth = $width;
			$newHeight = $height;
			// Justify aspectratio
			if ($newWidth > $max_width) {
				$newHeight = $newHeight / ($newWidth / $max_width);
				$newWidth = $max_width;
			}
			
			if ($newHeight > $max_height) {
				$newWidth = $newWidth / ($newHeight / $max_height);
				$newHeight = $max_height;
			}
	
			// JPG
			if($type == "image/pjpeg" || $type == "image/jpeg" || $extension == '.jpg') {
				$create_image = imagecreatefromjpeg($temp_name);
			// PNG
			} elseif($type == "image/x-png" || $type == "image/png" || $extension == '.png'){
				$create_image = imagecreatefrompng($temp_name);
			// GIF
			} elseif($type == "image/gif" || $extension == '.gif'){
				$create_image = imagecreatefromgif($temp_name);
			} else {
				$create_image = imagecreatefromjpeg($temp_name);
			}
	
			if (!$create_image) {
				$error[] = "Ogiltig bild.";
				continue;
			}
			$newImage = imagecreatetruecolor($newWidth,$newHeight);
	
			imagecopyresized($newImage, $create_image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
	
			// Save it
			// JPG
			if($type == "image/pjpeg" || $type == "image/jpeg" || $extension == '.jpg') {
				imagejpeg($newImage,$upload_path."/".$filename.$extension);
			// PNG
			} elseif($type == "image/x-png" || $type == "image/png" || $extension == '.png'){
				imagepng($newImage,$upload_path."/".$filename.$extension);
			// GIF
			} elseif($type == "image/gif" || $extension == '.gif'){
				imagegif($newImage,$upload_path."/".$filename.$extension);
			} else {
				imagejpeg($newImage,$upload_path."/".$filename.$extension);
			}
	
			imagedestroy ($newImage);
			imagedestroy ($create_image);
	
			// check filesize - Max 2MB
			if (filesize($upload_path."/".$filename.$extension) > $max_upload_size) {
				@unlink($upload_path."/".$filename.$extension);
				$error[] = "Förstor fil, max storlek är ".($max_upload_size/1024)." KB.";
				continue;
			}

		
		} else {
			// no resize made 

			// Save it
			move_uploaded_file($temp_name,$upload_path."/".$filename.$extension);
			
			// optimize , require Optipng installed in server
			if ($extension==".png")
				file_upload::optipng($upload_path."/".$filename.$extension);
		}

		// check checksum again for the resized image
		if ($image->get_checksum($user->logged_in(), md5(file_get_contents($upload_path."/".$filename.$extension)))) {
			@unlink($upload_path."/".$filename.$extension);
			$error[] = "En liknande fil finns redan uppladdad.";
			continue;
		} else {
			// save to db
			$image->save($user->logged_in(), $filename.$extension, $relative_upload_path."/".$filename.$extension, $size, md5(file_get_contents($upload_path."/".$filename.$extension)));
		}
		
		$success[] = '"'.$filename.$extension.'"';
	endfor; ?>
{
	"path": [<?php echo implode(',', $success); ?>]
	<?php if (count($error)>0) : ?>
	,"error": "Fel: En eller fler filer misslyckades att ladda upp. <?php echo implode(" ", $error); ?>"
	<?php endif; ?>
}
<?php else : 
setFlash("global", "Bilduppladdning misslyckad");
?>
{"error": "Fel: Ingen fil skickades till servern eller så har du fullt med utrymme."}
<?php endif; ?>