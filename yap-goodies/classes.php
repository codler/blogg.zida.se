<?php
/*
 * Class file
 *
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			23rd April 2010
 * @last-modified	11th May 2010
 * @version			1.4
 * ----------------------------------------
 * Change log:
v1.4 - 8th May 2010
added html5 email and url
v1.3 - 8th May 2010
added radio-method
v1.2 - 4th May 2010
added parameter id in open method
v1.1 - 27th April 2010
added checkbox-method

 v1.0 - 23th April 2010

 */
 
/*
 * --- Index ---
 * Form (class)
 *		captcha
 *		open
 *		text
 *		password
 *		checkbox
 *		radio
 *		submit
 *		input
 *		message
 *		close
 *
 */
 
class form {
	public static $link_name = 'yap_goodies';
	
	function __construct($type, $location='', $method='post') {
		echo "<form action=\"".$location."\" method=\"".$method."\">\r\n";
		$this->hidden(self::$link_name . '_submit_type',$type);
	}
	
	public function captcha($label_before=false, $label_after=false) {
		$first = rand(0,9);
		$second = rand(0,9);
		self::hidden(self::$link_name . '_captcha', md5($first+$second));
		self::text($label_before . $first . "+" . $second . $label_after, self::$link_name . '_captcha_check');
	}
	
	public function check_captcha() {
		if (isset($_POST[self::$link_name . '_captcha'])&&isset($_POST[self::$link_name . '_captcha_check'])) {
			if ($_POST[self::$link_name . '_captcha'] == md5($_POST[self::$link_name . '_captcha_check'])) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	public function config($c) {
		// available: text, hidden, radio, checkbox, password, submit 
		// html5: number, url, email, date, month, week, time, color
		$c['auto_focus'] = (isset($c['auto_focus'])) ? $c['auto_focus'] : false; // html5
		$c['auto_complete'] = (isset($c['auto_complete'])) ? $c['auto_complete'] : true; // html5
		$c['custom'] = (isset($c['custom'])) ? $c['custom'] : array();
		$c['class'] = (isset($c['class'])) ? $c['class'] : false;
		$c['id'] = (isset($c['id'])) ? $c['id'] : false;
		$c['label'] = (isset($c['label'])) ? $c['label'] : false;
		$c['multiple'] = (isset($c['multiple'])) ? $c['multiple'] : false; // html5
		$c['name'] = (isset($c['name'])) ? $c['name'] : 'text';
		$c['placeholder'] = (isset($c['placeholder'])) ? $c['placeholder'] : false; // html5
		$c['remember'] = (isset($c['remember'])) ? $c['remember'] : false;
		$c['required'] = (isset($c['required'])) ? $c['required'] : false; // html5
		$c['type'] = (isset($c['type'])) ? $c['type'] : 'text';
		$c['value'] = (isset($c['value'])) ? $c['value'] : false;
		
		$c['name'] = ($c['type']=='checkbox') ? $c['name'].'[]' : $c['name'];
		$c['checked'] = (($c['type']=='checkbox'||$c['type']=='radio') &&
			isset($c['checked'])) ? $c['checked'] : false;
		
		// if label and not id get a random id.
		if ($c['label']&&!$c['id'])
			$c['id'] = "l".rand();
			
		// to custom variable
		if ($c['class'])
			$c['custom']['class'] = $c['class'];
		
		if ($c['id'])
			$c['custom']['id'] = $c['id'];
		
		$c['custom']['name'] = $c['name'];
		
		// build html
		$input = '';
		if ($c['label'])
			$input .= "<label for=\"".$c['id']."\">".$c['label']."</label>\r\n";
		
		if ($c['auto_focus'])
			$input .= ' autofocus ';
		
		if ($c['multiple'])
			$input .= ' multiple ';
		
		if (!$c['auto_complete'])
			$input .= ' autocomplete="off" ';
		
		foreach ($c['custom'] AS $k => $v) {
			$input .= ' '.$k.'="'.$v.'" ';
		}
		
		
		return "<input />";
	}
	
	public function open($type, $location='', $method='post', $id=false) {
		$id = ($id) ? "id=\"".$id."\"" : "";
		echo "<form action=\"".$location."\" method=\"".$method."\" ".$id.">\r\n";
		self::hidden(self::$link_name . '_submit_type',$type);
	}
	
	public function get_submit_type() {
		if (isset($_POST[self::$link_name . '_submit_type'])) {
			return $_POST[self::$link_name . '_submit_type'];
		} else {
			return false;
		}
	}
	
	public function get_posts($type, $fields, $int=array()) {
		if (!is_array($fields)) return false;
		if ($_POST[self::$link_name . '_submit_type'] != $type) return false;
	
		$data = array();
		foreach ($fields AS $v) {
			if (in_array($v, $int)) {
				if (is_numeric($_POST[$v])) {
					$data[$v] = $_POST[$v];
				}
			} else {		
				if (is_array($_POST[$v])) {
					$data[$v] = $_POST[$v];
				} else {
					$data[$v] = trim($_POST[$v]);
				}
			}
			
			// Cookie
			if (isset($_POST[self::$link_name . '_remember_' . $v])) {
				// Max length
				if (strlen(trim($_POST[$v])) < 500) {
					setcookie(self::$link_name . "_remember_field[" . $v . "]", trim($_POST[$v]), time()+8*24*60*60);
				}
			}
		}
		return $data;
	}
	
	public function hidden($field, $value, $id=false) {
		self::input('hidden', false, $field, $value, $id);
	}
	
	public function text($label, $field, $value=false, $id=false, $remember=false, $auto_empty=false) {
		self::input('text', $label, $field, $value, $id, $remember, $auto_empty);
	}
	
	public function password($label, $field, $id=false) {
		self::input('password', $label, $field, false, $id);
	}
	
	public function checkbox($label, $field, $value=false, $id=false, $checked=false) {
		self::input('checkbox', $label, $field."[]", $value, $id, false, false, $checked);
	}
	
	public function radio($label, $field, $value=false, $id=false, $checked=false) {
		self::input('radio', $label, $field, $value, $id, false, false, $checked);
	}
	
	public function submit($label, $field='') {
		self::input('submit', false, $field, $label);
	}
	
	// html5
	public function email($label, $field, $value=false, $id=false, $remember=false, $auto_empty=false) {
		self::input('email', $label, $field, $value, $id, $remember, $auto_empty);
	}
	public function url($label, $field, $value=false, $id=false, $remember=false, $auto_empty=false) {
		self::input('url', $label, $field, $value, $id, $remember, $auto_empty);
	}
	
	public function input($type, $label, $field='', $value=false, $id=false, $remember=false, $auto_empty=false, $checked=false) {
		if (!$id) $id = "l".rand();
		if ($label) echo "<label for=\"".$id."\">".$label."</label>\r\n";
		
		// remember / cookie
		if ($remember) {
			if (isset($_COOKIE[self::$link_name . "_remember_field"])) {
				$cookie = $_COOKIE[self::$link_name . "_remember_field"][$remember];
				if (isset($cookie)) {
					$value = htmlspecialchars(stripslashes(stripslashes($cookie)));
				}
			}
			self::hidden(self::$link_name . '_remember_' . $field,'true');
		}	
		
		// checked
		$check = ($checked) ? "checked" : "";
		
		if ($value||$value===0) {
			if ($auto_empty) {
				echo "<input ".$check." type=\"".$type."\" id=\"".$id."\" name=\"".$field."\" value=\"".$value."\" onfocus=\"if(this.value=='".addslashes($value)."')this.value=''\" onblur=\"if(this.value=='')this.value='".addslashes($value)."'\"/>\r\n";
			} else {
				echo "<input ".$check." type=\"".$type."\" id=\"".$id."\" name=\"".$field."\" value=\"".$value."\"/>\r\n";
			}
		} else {
			echo "<input ".$check." type=\"".$type."\" id=\"".$id."\" name=\"".$field."\" />\r\n";
		}
	}
	
	public function message($label, $field, $value=false, $id=false) {
		if (!$id) $id = "l".rand();
		echo "<label for=\"".$id."\">".$label."</label>\r\n";
		if ($value) {
			echo "<textarea id=\"".$id."\" name=\"".$field."\">".$value."</textarea>\r\n";
		} else {
			echo "<textarea id=\"".$id."\" name=\"".$field."\"></textarea>\r\n";
		}
	}
	
	public function close() {
		echo "</form>\r\n";
	}
}
?>