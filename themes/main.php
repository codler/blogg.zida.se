<?php 
/*
 * template: Main page - maps to login- or logged in page
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	4 May 2010
 * @version			1.0
 * ----------------------------------------
*/
if (!defined('BASE_DIR')) die('No direct script access allowed');

if ($user->logged_in()) {
	require_once(get_page('/main-loggedin'));
} else {
	$oauth = new sso(DB_PREFIX, $db);
	
		require_once(get_page('/login'));

}

?>