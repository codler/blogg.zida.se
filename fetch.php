<?php
/*
 * Ajax handler
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	5 may 2010
 * @version			1.1
 * ----------------------------------------
*/
if (basename($_SERVER['PHP_SELF']) != 'index.php') die();

$type = $_POST['type'];
if ($type == 'exist-email') {
	$email = $_POST['email'];
	$db->safe($email);
	
	echo (!$user->occupied('email', $email)) ? "true" : "false";
	return;
}
if ($type == 'exist-name') {
	$name = $_POST['name'];
	$db->safe($name);
	
	echo (!$user->occupied('username', $name)) ? "true" : "false";
	return;
}
if ($type == 'not-exist-name') {
	$name = $_POST['name'];
	$db->safe($name);
	
	echo ($user->occupied('username', $name)) ? "true" : "false";
	return;
}
// blog url exist?
if ($type == 'exist-url') {
	$url = $_POST['url'];
	$db->safe($url);
	
	echo (!$blog->occupied('blog_url', $url)) ? "true" : "false";
	return;
}

// check login
if (!$user->logged_in()) { 
	return;
}

// delete image
if ($type == 'delete-image') {
	$url = $_POST['url'];
	$db->safe($url);
	$image = new image(DB_PREFIX, $db);
	echo ($image->delete($user->logged_in(), $url)) ? "true" : "false";
	return;
}

// change design name
if ($type == 'change-design-name') {
	$id = $_POST['id'];
	$name = $_POST['name'];
	
	if (!is_numeric($id))
		return;
	
	$db->safe($name);
	$design = new design(DB_PREFIX, $db);
	echo ($design->change_name($user->logged_in(), $id, $name)) ? "true" : "false";
	return;
}
?>