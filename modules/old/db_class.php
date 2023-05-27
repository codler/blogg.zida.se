<?php 
/*
Date: 2009-06-19
Author: Han Lin Yap
Website: www.zencodez.net
File Description: Mysql database connection class

*/

/*$c['db'] = array("host" => "localhost",
					  "username" => "",
					  "password" => "",
					  "database" => "");*/
/* Example */
//$db = new zcDatabase($c['db']);
//$d = $db->query("SELECT * FROM zc_userservices");
//echo $db->scaffold();
//echo $db->debugLog();
/* Class */
class zcDatabase
{	
	private $host;
	private $username;
	private $password;
	private $database;
	private $connection;
	
	private $queryData = array();
	private $querySql = array();
	private $queryTimes = 0;
	private $queryRows = array();
	
	private $debugLog = array();
	private $debugOn = false;
	
	// Initiation
	public function __construct($data) {
		extract($data, EXTR_OVERWRITE);
		
		$this->host = $host;
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;
		
		// Connect
		$this->connection = mysql_connect($this->host,$this->username,$this->password) or die('Could not connect: ' . mysql_error());
		// Selecting database
		mysql_select_db($this->database, $this->connection) or die('Could not select database: ' . mysql_error());
		
	}
	
	// Change database
	public function setDB($database) {
		$this->database = $database;
		// Selecting database
		mysql_select_db($this->database, $this->connection) or die('Could not select database: ' . mysql_error());
	}
	
	// Get data
	public function query($sql,$cache=false) {
		if ($cache) {
			$index = array_search($sql,$this->querySql);
			if ($index) {
				if (array_key_exists($index,$this->queryData))
					return $this->queryData[$index];
			}
		}
		
		$result = @mysql_query($sql,$this->connection);
		if ($result) {
			$this->queryRows[] = mysql_num_rows($result);
			$data = array();
			while ($row = mysql_fetch_assoc($result)) {
				$data[] = $row;
			}
			$this->queryTimes++;
			$this->queryData[] = $data;
			$this->querySql[] = $sql;
			return $data;
		} else {
			$this->debugLog("Query Failed: " . $sql);
			return false;
		}
	}
	// Execute sql-query
	public function executeQuery($sql) {
		$result = @mysql_query($sql,$this->connection);
		if ($result) {
			return true;
		} else {
			$this->debugLog("Execute Query Failed: " . $sql);
			return false;
		}
	}
	
	//  Last insest id (Only use after an INSERT-sql)
	public function insertId() {
		return mysql_insert_id($this->connection);
	}
	
	// Debug Logging
	public function debugLog($log=false) {
		if ($log===false) return implode("<br />",$this->debugLog);
		
		$this->debugLog[] = $log;
	}
	
	/*  ------------------------------------------------------------------------
		Function: scaffold
		Parameters:	
		@data
		@settings	array("insert",
						  "update",
						  "delete",
						  "excludeFields" => array("fieldname")
						  )
		------------------------------------------------------------------------ */
	public function scaffold($data=false) {
		if ($data = end($this->queryData)) {
		$layout = "<table>\r\n";
		$layout .= "<tr>\r\n";
		// print fieldname
		foreach($data[0] AS $field => $v) {
			$layout .= "<td>\r\n";
			$layout .= $field;
			$layout .= "</td>\r\n";
		}
		$layout .= "</tr>\r\n";
		
		// print data-value
		foreach($data AS $field) {
			$layout .= "<tr>\r\n";
			foreach($field AS $k => $v) {
				$layout .= "<td>\r\n";
				$layout .= $v;
				$layout .= "</td>\r\n";
			}
			$layout .= "</tr>\r\n";
		}
		$layout .= "</table>\r\n";
		
		return $layout;
		} else {
			$this->debugLog("Scaffold: No data or query was submitted");
			return false;	
		}
	}
	
	// Get number of rows
	public function getRows() {
		return end($this->queryRows);
	}
	
	// Print sql
	public function getSql() {
		return end($this->querySql);
	}
	
	// Get last data
	public function getData() {
		return end($this->queryData);
	}
	
	// Close
	public function __destruct() {
		//mysql_close($this->connection);
	}
	
	public function safe(&$s) 
	{	
		if (!function_exists("get_magic_quotes_gpc") || !get_magic_quotes_gpc()) {
			$s = mysql_real_escape_string($s);
		} else {
			$s = mysql_real_escape_string(stripslashes($s));
		}
	}
}
?>