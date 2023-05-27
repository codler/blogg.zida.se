<?php
/*
 * Index file
 *
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			23rd April 2010
 * @last-modified	23rd April 2010
 * @version			0.4
 * ----------------------------------------
 * Change log:
v0.4 - 23th April 2010
Blogg core start from scratch.
 */

// Constants
define('MEMORY_INIT', memory_get_usage()); // For memory debug
define('TIME_INIT', microtime(true)); // For load time debug
define('BASE_DIR', dirname(__FILE__) . "/");


// Auto load class
function __autoload($class_name) {
    require_once BASE_DIR . "modules/" . $class_name . '.php';
}

if (!isset($_SESSION)) session_start();
@header(base64_decode('WC1Qb3dlcmVkLUJ5OiB6ZW5jb2Rlei5uZXQ='));

// Include
require_once(BASE_DIR . "external/rpc/client.php"); // Common functions
require_once(BASE_DIR . "yap-goodies/index.php"); // Common functions
require_once(BASE_DIR . "functions.php"); // Site functions
require_once(BASE_DIR . "config.php"); // Site config

$host = 'blogg.zida.se';
if (uri::host() != $host) {
	require_once(BASE_DIR . "blog.php");
} else {

	// Current page
	$page = get_page(parse_url(uri::fqdn(), PHP_URL_PATH));
	if (file_exists($page)) {
		
		ob_start();
		// Main
		require_once($page);
		$content = ob_get_contents();
		ob_end_clean();
		
		// Header
		if ($page != substr($pages['design/edit'], 1) && 
			$page != substr($pages['storage'], 1) && 
			$page != $pages['upload'] && 
			$page != $pages['fetch'])
			if (file_exists(BASE_DIR . THEME_DIR . "header.php"))
				require_once(BASE_DIR . THEME_DIR . "header.php");
				
		echo $content;
			
		// Footer
		if ($page != substr($pages['design/edit'], 1) && 
			$page != substr($pages['storage'], 1) && 
			$page != $pages['upload'] && 
			$page != $pages['fetch'])
			if (file_exists(BASE_DIR . THEME_DIR . "footer.php"))
				require_once(BASE_DIR . THEME_DIR . "footer.php");
				
	} else {
		header("HTTP/1.1 404 Not Found");
		die();
	}
}

if (DEBUG) {
	function convert($size) {
		$unit=array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
	if ($db->error)
		echo "DB SQL error: ".$db->error;
	echo "Memory usage: (start)".convert(MEMORY_INIT);
	echo " (end)".convert(memory_get_usage());
	echo " (peak)".convert(memory_get_peak_usage());
	echo "<br />";
	$data = getrusage();  
	echo "User time: ".  
	($data['ru_utime.tv_sec'] +  
	$data['ru_utime.tv_usec'] / 1000000);  
	echo "<br />";
	echo "System time: ".  
	($data['ru_stime.tv_sec'] +  
	$data['ru_stime.tv_usec'] / 1000000);  
	echo "<br />";
	echo "Load time: ".(microtime(true) - TIME_INIT);
}
die();
?>
<!--
HTTP_HOST: <?php echo $_SERVER['HTTP_HOST']; ?>
<br>
QUERY_STRING: <?php echo $_SERVER['QUERY_STRING']; ?>
<br>
PHP_SELF: <?php echo $_SERVER['PHP_SELF']; ?>

<br>
Request: <?php echo print_r($_REQUEST); ?>
<br>
GET: <?php echo print_r($_GET); ?>
<br>
SESSION: <?php echo print_r($_SESSION); ?>
<br>
REQUEST_URI: <?php echo print_r($_SERVER); ?>
-->