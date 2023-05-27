<?php 
/*
 * Abstract database class
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	19 mars 2010
 * @version			1.0
 * ----------------------------------------
*/

class database extends query {
	private $info;
	protected $con;
	public $error = false;
	public $debug = false;
	function __construct($config) {
		$this->info = $config;
		
		$this->con = mysql_connect(
			$this->info['host'], 
			$this->info['user'], 
			$this->info['password']
		);
		if (!$this->con) {
			$this->error = "Fail to connect to database";
			return false;
		}
		$success = mysql_select_db($this->info['database'],$this->con);
		if (!success) {
			$this->error = "Fail to select database";
			return false;
		}
	}
}

class query {
	public $error = false;
	
	public function debug($d) {
		if ($this->debug) 
			echo $d . "<br />\r\n";
	}
	
	public function select($s) {
		$this->debug($s);
		$result = mysql_query($s, $this->con);
		if (!$result) {
			$this->error = "Fail to select from database";
			return false;
		}
		$rows = mysql_num_rows($result);
		$data = array();
		while ($row = mysql_fetch_assoc($result)) {
			$data[] = $row;
		}
		
		return array('data' => $data,'rows' => $rows);
	}
	
	public function insert($s) {
		$this->debug($s);
		$result = mysql_query($s, $this->con);
		if (!$result) {
			$this->error = "Fail to insert to database";
			return false;
		}
		return mysql_insert_id($this->con);
	}
	
	public function update($s) {
		$this->debug($s);
		$result = mysql_query($s, $this->con);
		if (!$result) {
			$this->error = "Fail to update database";
			return false;
		}
		return mysql_affected_rows($this->con);
	}
	
	public function delete($s) {
		return $this->update($s);
	}
	
	public function safe(&$s) 
	{	
		if (is_array($s)) {
			foreach($s AS &$v) {
				if (!function_exists("get_magic_quotes_gpc") || !get_magic_quotes_gpc()) {
				$v = mysql_real_escape_string($v);
				} else {
					$v = mysql_real_escape_string(stripslashes($v));
				}
			}
		} else {
			$t = array(&$s);
			$this->safe($t);
		}
	}
	
	public function xss_safe(&$data, $fields) 
	{	
		if (!is_array($fields)) return false;
		foreach ($data AS $k => $v) {
			foreach ($fields AS $field) {
				$data[$k][$field] = htmlspecialchars($data[$k][$field]);
			}
		}
	}
}
?>