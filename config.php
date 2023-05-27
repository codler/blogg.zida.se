<?php
/*
 * Config file
 *
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			23rd April 2010
 * @last-modified	23rd April 2010
 * @version			1.0
 * ----------------------------------------
 * Change log:
v1.0 - 23th April 2010

 */
 
// Debug mode
if (!isset($_GET['debug'])) 
	define('DEBUG', true);
// Production mode - set to false if it is live version.
define("PRODUCTION", true);	
// default title
$title = "Blogg*zida v0.7";
// design tool version
define('DESIGN_TOOL_VERSION', '0.6');

// --- DONT EDIT BELOW - ON LIVE -------------------

// facebook
define('FACEBOOK_APP_ID', '2dce67debb1d376f0674cdda6e54d16a');
define('FACEBOOK_SECRET', '4a8349a40a48c6f52c77cadba0232d1d');

// Root url
define('ROOT', '/');

// Database information
$db_info = array(
	'host'		=> 'localhost',
	'user'		=> 'admin_zc',
	'password'	=> '31568',
	'database'	=> 'admin_zida'
);

if (PRODUCTION)
	$db_info['database'] = 'admin_devzida';
	
// table prefix
define('DB_PREFIX', 'zida');
// themes
define('THEME_DIR', 'themes/');

// user images
define('USER_IMAGE_DIR', 'user/');

// Map url to file
$pages = array(
	// default
	'default' => THEME_DIR . "main.php",
	
	// login required
	'account' => THEME_DIR . "account.php",
	'comments' => "*" . THEME_DIR . "comments.php",
	'design' => "*" . THEME_DIR . "design.php",
	'main-loggedin' => THEME_DIR . "main-loggedin.php",
	'post/new' => "*" . THEME_DIR . "post-new.php",
	'post/edit' => "*" . THEME_DIR . "post-edit.php",
	'posts' => "*" . THEME_DIR . "posts.php",
	'settings' => "*" . THEME_DIR . "settings.php",
	
	// no login required
	'forgot' => "*" . THEME_DIR . "forgot.php",
	'login' => THEME_DIR . "login.php",
	'logout' => THEME_DIR . "logout.php",
	'news' => THEME_DIR . "news.php",
	
	// Image browser
	'image/browse' => THEME_DIR . "image_browse.php",
	'upload' => "upload.php",
	
	// other
	'constraint' => "constraint.php",
	
	// design
	'design/edit' => "*" . "design.php",
	'design/preview' => "design.php",
	'storage' => "*" . "storage.php",
	
	// submition, ajax, get, post
	'comment/delete' => "*" . "rest.php",
	'design/delete' => "*" . "rest.php",
	'disconnect' => "*" . "rest.php",
	'fetch' => "fetch.php",
	'post/delete' => "*" . "rest.php",
	'submit' => "submit.php"
);



// Set validation values
if (class_exists('validate')) {
	validate::$email_length = 50;
	validate::$password_min_length = 6;
	validate::$username_length = 40;
	validate::$username_regex = "/^[a-zA-Z0-9]{4,}$/";
}

// Load assets
add_external(ROOT . 'yap-goodies' . '/css/global.css', "css");
add_external(ROOT . 'assets/css/common.css', "css");
add_external("http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js", "js");
#add_external("http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js", "js");
add_external(ROOT . "external/jquery-validate/jquery.validate.min.js", "js");
add_external(ROOT . "external/jquery-validate/localization/messages_se.js", "js");

// --- DONT EDIT BELOW - INIT -------------------

// debug off
if (isset($_GET['debug'])&&$_GET['debug']==0)
	define('DEBUG', false);

// security
new security(true);

// DB
$db = new database($db_info);
$db->debug = DEBUG;
if ($db->debug) {
	echo $db->error;
}

// User
$user = new user(DB_PREFIX, $db);
// Blog
$blog = new blog(DB_PREFIX, $db);

?>