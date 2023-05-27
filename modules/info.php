<?php
/**
 * Info class
 * 
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			13th May 2010
 * @last-modified	13th May 2010
 * @version			1.0
 * @package 		modules
 */
/* ----------------------------------------
 * Change log:
v1.0 - 13th May 2010
Created
 */
class info {
	/**  Location for overloaded data.  */
    private $data = array();
	
	function __construct($type, $value) {
		
	}
	
	public function __set($name, $value) {
		if ($name == 'blog' || $name == 'user' || $name == 'post') {
			$this->data[$name] = new info($name, $value);
		} else {
			$this->data[$name] = $value;
		}
    }
	
	public function __get($name) {
		if (array_key_exists($name, $this->data)) {
			return $this->data[$name]
		} else {
			if ($name == 'blog' || $name == 'user' || $name == 'post') {
				return new info($name);
			} else {
				return null;
			}
		}
    }
}
?>