<?php
/**
 * Image class
 * 
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			22nd May 2010
 * @last-modified	22nd May 2010
 * @version			1.0
 * @package 		modules
 */
/* ----------------------------------------
 * Change log:
v1.0 - 22nd May
Created
 */

class image {
	
	function __construct($prefix, $db) {
		$this->prefix = $prefix;
		$this->db = $db;
	}
	
	function delete($user_id, $path) {
		$real_path = $this->get_image_real_path($user_id, $path);
		// delete file
		@unlink(BASE_DIR . USER_IMAGE_DIR . $real_path);
		// delete from db
		return $this->db->delete("DELETE FROM ".$this->prefix."_images WHERE user_id = ".$user_id." AND image_path = '" . $path . "'");
	}
	
	function save($user_id, $path, $real_path, $size, $checksum) {
		return $this->db->insert("INSERT INTO ".$this->prefix."_images (user_id, image_path, image_real_path, image_size, image_checksum) VALUES (".$user_id.", '" . $path . "', '" . $real_path . "', " . $size . ", '" . $checksum . "')");
	}
	
	function usage($user_id) {
		$r = $this->db->select("SELECT sum(image_size) AS used FROM ".$this->prefix."_images WHERE user_id = ".$user_id);
		if (!$r) return false;
		return ($r['data'][0]) ? $r['data'][0]['used'] : false;
	}
	
	function get_checksum($user_id, $checksum) {
		$r = $this->db->select("SELECT image_checksum FROM ".$this->prefix."_images 
			WHERE user_id = ".$user_id." AND image_checksum = '" . $checksum . "' LIMIT 1");
		
		if (!$r) return false;
		return ($r['data'][0]) ? $r['data'][0]['image_checksum'] : false;
	}
	
	function get_image_real_path($user_id, $path) {
		$r = $this->db->select("SELECT image_real_path FROM ".$this->prefix."_images 
			WHERE user_id = ".$user_id." AND image_path = '" . $path . "' LIMIT 1");
		if (!$r) return false;
		return ($r['data'][0]) ? $r['data'][0]['image_real_path'] : false;
	}
	
	function get_images($user_id) {
		$r = $this->db->select("SELECT * FROM ".$this->prefix."_images WHERE user_id = ".$user_id);
		if (!$r) return false;
		return $r['data'];
	}
} 
?>