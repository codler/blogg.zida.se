<?php
/*
 * Validate class
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
 */
 
class validate {
	public static $email_length = 50;
	public static $password_min_length = 6;
	public static $username_length = 40;
	public static $username_regex = "/^[a-zA-Z0-9]{4,}$/";
	
	public function email($s) {
		return self::length($s, self::$email_length) &&
			filter_var($s, FILTER_VALIDATE_EMAIL);
	}
	
	public function length($s, $i) {
		$f = (is_array($s)) ? "sizeof" : "strlen";
		return $f($s) <= $i;
	}
	
	public function min_length($s, $i) {
		$f = (is_array($s)) ? "sizeof" : "strlen";
		return $f($s) >= $i;
	}
	
	public function password($s) {
		return self::min_length($s, self::$password_min_length);
	}
	
	public function url($s) {
		return filter_var($s, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);
	}
	
	public function username($s) {
		return self::length($s, self::$username_length) &&
			preg_match(self::$username_regex, $s);
	}
}

?>