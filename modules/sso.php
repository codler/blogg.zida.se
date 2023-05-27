<?php
/**
 * Oauth - Facebook class
 * 
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			12th May 2010
 * @last-modified	12th May 2010
 * @version			1.0
 * @package 		modules
 */
/* ----------------------------------------
 * Change log:
v1.0 - 12th May 2010
Created
 */
 
class sso {
	function __construct($prefix, $db) {
		$this->prefix = $prefix;
		$this->db = $db;
	}
	
	public function facebook_add($user_id, $fb_uid) {
		$r = $this->db->select("SELECT * FROM ".$this->prefix."_users_auth_fb 
			WHERE fb_uid = ".$fb_uid." OR user_id = ".$user_id." LIMIT 1");
		if (!$r) return false;
		if ($r['rows']>0) return false;
		return $this->db->insert("INSERT INTO ".$this->prefix."_users_auth_fb (user_id,fb_uid) 
			VALUES (".$user_id.",".$fb_uid.");");
	}
	
	public function facebook_connected($user_id) {
		$r = $this->db->select("SELECT * FROM ".$this->prefix."_users_auth_fb 
			WHERE user_id = ".$user_id." LIMIT 1");
		if (!$r) return false;
		return ($r['rows']) ? true : false;
	}

	public function facebook_disconnect($user_id) {
		return $this->db->delete("DELETE FROM ".$this->prefix."_users_auth_fb 
			WHERE user_id = ".$user_id." LIMIT 1");
	}
	
	public function facebook_login($app_id, $application_secret) {
		$cookie = $this->get_facebook_cookie($app_id, $application_secret);

		if ($cookie) {
			$r = $this->db->select("SELECT * FROM ".$this->prefix."_users_auth_fb 
				WHERE fb_uid = ".$cookie['uid']." LIMIT 1");
			if (!$r) return false;
			return (isset($r['data'][0])) ? $r['data'][0]['user_id'] : false;
		}
	}

	function get_facebook_cookie($app_id, $application_secret) {
	  $args = array();
	  parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
	  ksort($args);
	  $payload = '';
	  foreach ($args as $key => $value) {
		if ($key != 'sig') {
		  $payload .= $key . '=' . $value;
		}
	  }
	  if (md5($payload . $application_secret) != $args['sig']) {
		return null;
	  }
	  return $args;
	}
}