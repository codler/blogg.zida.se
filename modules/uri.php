<?php
/*
 * URI class
 *
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			23rd April 2010
 * @last-modified	23rd April 2010
 * @version			1.0
 * ----------------------------------------
 * Change log:
v1.0 - 23th April 2010
Created
 */

/* 
Methods:
fqdn()
scheme($port=false)
host()
path()
 */
class uri {	
	function fqdn() {
		return self::scheme() . self::host() . self::path();
	}
	
	function scheme($port=false) {
		$port = ($port) ? $port : $_SERVER['SERVER_PORT'];
		return ($port == 443 ? 'https://' : 'http://');
	}
	
	function host() {
		return $_SERVER['HTTP_HOST'];
	}
	
	function path() {
		return $_SERVER['REQUEST_URI'];
	}
}
?>