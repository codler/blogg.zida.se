<?php 
/*
 * User class
 *
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			23rd April 2010
 * @last-modified	5th May 2010
 * @version			1.1
 * ----------------------------------------
 * Change log:
v1.1 - 5 May 2010
New methods
change_name($name)
change_email($email)

v1.0 - 23th April 2010
Created
 */

/* 
Depended files:
validate.php (module)
database (module)

Methods:
__construct($prefix, $db)
change_password($old_password, $new_password, $new_password2 = false)
change_name($name)
change_email($email)
encrypt($s)
forgot() 	# TODO
info($id, $field = "*")
login($username, $password)
logout()
occupied($type, $value)
register($username, $password, $email)
 */
 
class user {
	public static $salt = 'zida';
	
	/* Params:
	 *	db 		- db class
	 */
	function __construct($prefix, $db) {
		$this->prefix = $prefix;
		$this->db = $db;
		$this->permission_level = 0;
	}
	
	// change password set old- and new password
	// recovery password set new password and user_id
	public function change_password($old_password=false, $new_password, $new_password2 = false, $user_id=false) {		
		$valid = isset($new_password) &&
			validate::password($new_password);
		
		// if oldpassword is set
		if ($old_password)
			$valid &= isset($old_password);
		
		// If newpassword 2 is set
		if ($new_password2)
			$valid &= isset($new_password2) &&
				validate::password($new_password2) &&
				$new_password == $new_password2;
		
		if ($valid) {
			// if oldpassword is set
			if ($old_password) {
				$old_encrypted_password = $this->encrypt($old_password);
				$new_encrypted_password = $this->encrypt($new_password);
				
				$info = $this->info($this->logged_in(), 'password');
				$real_old_password = $info['password'];
				if ($real_old_password == $old_encrypted_password) {
					$this->db->update("UPDATE ".$this->prefix."_users SET password = '".$new_encrypted_password."' 
						WHERE user_id = ".$this->logged_in());
					return true;
				}
			} else {
				$new_encrypted_password = $this->encrypt($new_password);
				$this->db->update("UPDATE ".$this->prefix."_users SET password = '".$new_encrypted_password."' 
					WHERE user_id = ".$user_id);
				return true;
			}
		}
		return false;
	}
	
	public function change_username($username) {
		$valid = isset($username) &&
			validate::username($username);

		if ($valid) {
			$this->db->update("UPDATE ".$this->prefix."_users SET username = '".$username."' 
				WHERE user_id = ".$this->logged_in());
			return true;
		}
		return false;
	}
	
	public function change_email($email) {
		$valid = isset($email) &&
			validate::email($email);

		if ($valid) {
			$this->db->update("UPDATE ".$this->prefix."_users SET email = '".$email."' 
				WHERE user_id = ".$this->logged_in());
			return true;
		}
		return false;
	}
	
	public function encrypt($s) {
		return md5(self::$salt.$s);
	}
	
	public function forgot($username) {
		$valid = isset($username);
		
		$field = (validate::email($username)) ? "email" : "username";
		if ($field=="username") {
			$valid &= validate::username($username);
			
			// Valid to db
			$valid &= $this->occupied('username', $username);
		} else {
			$valid &= $this->occupied('email', $username);
		}
		
		if ($valid) {
			$key = md5("forgot".microtime());
			
			if ($field=="email") {
				$user_id = $this->get_id_by("email", $username);
			} else {
				$user_id = $this->get_id_by("username", $username);
			}
			$info = $this->info($user_id);
			$r = $this->db->select("SELECT * FROM ".$this->prefix."_users_forgot 
				WHERE user_id = ".$user_id." AND forgot_date >= DATE_SUB(NOW( ), INTERVAL 2 DAY) LIMIT 1");
			if (!$r) return false;
			if ($r['rows']>0) return false;
			$this->db->insert("INSERT INTO ".$this->prefix."_users_forgot (user_id,forgot_key,forgot_ip,forgot_date) 
				VALUES (".$user_id.",'".$key."','".$_SERVER['REMOTE_ADDR']."',NOW( ))");
				
			// PHPmailer
			require_once (dirname(__file__).'/../external/class.phpmailer.php');
			$mail = new PHPMailer(); // defaults to using php "mail()"
			$mail->IsSendmail(); // telling the class to use SendMail transport
			$mail->CharSet = 'utf-8';
			
			$mail->AddReplyTo("codler+bloggzida@gmail.com","Han Lin Yap");
			$mail->SetFrom('noreply+forgot@blogg.zida.se', 'Glömt Bloggzida');
			
			$mail->AddAddress($info['email'], $info['username']); // ändra sen
			$mail->Subject = "Glömt lösenordet - Blogg*zida";
			// Body
			$body  = "Glömt lösenordet";
			$body .= "\r\n<br>" . $info['username'];
			$body .= "\r\n<br>";
			$body .= "\r\n<br>Klicka på länken för att ändra lösenordet " . $info['username'];
			$body .= "\r\n<br>http://blogg.zida.se/forgot/".$info['username']."/".$key;
			$body .= "\r\n<br>Länken gäller i 2 dagar";
			$body .= "\r\n<br>IP: " . $_SERVER['REMOTE_ADDR'];
			$body .= "\r\n<br>";		
			$body .= "\r\n<br>MVH";
			$body .= "\r\n<br>Han Lin Yap / Blogg*zida";
			$body .= "\r\n<br>http://blogg.zida.se";
			
			$body = preg_replace("[\]",'',$body);
			$mail->MsgHTML($body);
			if(!$mail->Send()) {
				return false;
			} else {
				return true;
			}
		}
	}
	
	public function forgot_check($user_id, $key) {
		$r = $this->db->select("SELECT * FROM ".$this->prefix."_users_forgot 
				WHERE user_id = ".$user_id." AND forgot_date >= DATE_SUB(NOW( ), INTERVAL 2 DAY) AND forgot_key = '" . $key . "' LIMIT 1;");
				
		if (!$r) return false;
		return ($r['rows']) ? true : false;
	}
	
	public function info($id, $field = "*") {
		$r = $this->db->select("SELECT " . $field . " FROM ".$this->prefix."_users 
				WHERE user_id = ".$id." LIMIT 1;");
				
		if (!$r) return false;
		return (isset($r['data'][0])) ? $r['data'][0] : false;
	}

	public function force_login($user_id) {
		$_SESSION['logged_in'] = true;
		$_SESSION['user_id'] = $user_id;
		return $user_id;
	}
	
	public function login($username, $password) {
		$valid = isset($username, $password);
		
		$field = (validate::email($username)) ? "email" : "username";
		if ($field=="username")
			$valid &= validate::username($username);
		
		if ($valid) {
			$encrypted_password = $this->encrypt($password);
			$r = $this->db->select("SELECT user_id FROM ".$this->prefix."_users 
				WHERE ".$field." = '".$username."' AND password = '".$encrypted_password."' LIMIT 1;");
				
			if (!$r) return false;
			$user_id = $r['data'][0]['user_id'];
			if (isset($user_id)) {
				$_SESSION['logged_in'] = true;
				$_SESSION['user_id'] = $user_id;
				
				// Log login
				$this->log_login($_SESSION['user_id']);
				
				return $user_id;
			}
		}
		return false;
	}
	public function last_login($user_id, $offset=0) {
		$r = $this->db->select("SELECT log_date FROM `".$this->prefix."_users_log` 
			WHERE user_id = ".$user_id." ORDER BY log_date DESC LIMIT ".$offset.",1");
		if (!$r) return false;
		return $r['data'][0]['log_date'];
	}
	public function log_login($user_id) {
		$this->db->insert("INSERT INTO ".$this->prefix."_users_log (user_id,log_ip,log_date) 
			VALUES (".$user_id.",'".$_SERVER['REMOTE_ADDR']."', NOW( ));");
	}
	
	public function logged_in() {
		return $_SESSION['user_id'];
	}
	
	public function logout() {
		unset($_SESSION['user_id']);
		unset($_SESSION['logged_in']);
	}
	
	public function get_id_by($field, $value) {
		$r = $this->db->select("SELECT user_id FROM ".$this->prefix."_users 
			WHERE ".$field." = '".$value."' LIMIT 1;");
		if (!$r) return false;
		return $r['data'][0]['user_id'];
	}
	
	public function get_friends($user_id) {
		# TODO - hämtar alla användare istället för vänner för tillfälligt
		$r = $this->db->select("SELECT * FROM ".$this->prefix."_users WHERE user_id != ".$user_id.";");
		if (!$r) return false;
		return $r['data'];
	}
	
	public function occupied($type, $value) {
		$r = $this->db->select("SELECT user_id FROM ".$this->prefix."_users WHERE ".$type." = '".$value."' LIMIT 1;");
		if (!$r) return false;
		return ($r['rows']) ? true : false;
	}
	
	public function register($username, $password, $email, $recruit=false) {
		$reserverd_user = array(
			"abcde",
			"abcdef",
			"abcdefg",
			"abcdefgh",
			"abcdefghi",
			"anvandare",
			"admin",
			"administrator",
			"blog",
			"blogg",
			"blogga",
			"bloggar",
			"developer",
			"google",
			"metroroll",
			"moderator",
			"sida",
			"statistik",
			"user",
			"zencodez",
			"zida"
		);
								
		$valid = isset($username, $password, $email) &&
			validate::email($email) &&
			validate::username($username) &&
			validate::password($password) &&
			// reserverd usernames
			!in_array($username,$reserverd_user); //&&
			// check for unique character so eg aaaaa username won't be accepted
			// atleast 4
			//strlen(count_chars($username, 3))>=4;
		
		// Valid to db
		$valid &= !$this->occupied('email', $email) &&
			!$this->occupied('username', $username);
			
		if ($recruit) {
			$valid &= validate::username($recruit);
		}
		
		if ($valid) {
			$encrypted_password = $this->encrypt($password);
			$_SESSION['logged_in'] = true;
			if ($recruit) {
				$_SESSION['user_id'] = $this->db->insert("INSERT INTO ".$this->prefix."_users (username,password,email,create_date, recruit) 
				VALUES ('".$username."','".$encrypted_password."','".$email."', NOW( ), '".$recruit."');");
			} else {
				$_SESSION['user_id'] = $this->db->insert("INSERT INTO ".$this->prefix."_users (username,password,email,create_date) 
				VALUES ('".$username."','".$encrypted_password."','".$email."', NOW( ));");
			}
			// Log login
			$this->log_login($_SESSION['user_id']);
			return $_SESSION['user_id'];
		}
		return false;
	}

}

?>