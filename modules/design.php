<?php
/*
 * Design class
 *
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			4rd May 2010
 * @last-modified	4rd May 2010
 * @version			1.0
 * ----------------------------------------
 * Change log:
v1.0 - 4th May 2010
Created
 */

/* 
Depended files:
database (module)

Methods:
__construct($prefix, $db)
 */
 
class design {
	function __construct($prefix, $db) {
		$this->prefix = $prefix;
		$this->db = $db;
	}
	
	public function add($user_id, $name, $data=false) {
		if (!$data)
			$data = '{}';
			
		$valid = isset($name);
		$name = substr(trim($name), 0, 100);
		
		$valid &= validate::min_length($name, 4);
		
		if ($valid) {
			return $this->db->insert("INSERT INTO ".$this->prefix."_layouts (layout_data, layout_name, layout_date, creator_user_id)
				VALUES ('".$data."', '".$name."',NOW( ), ".$user_id.")");
		} else {
			return false;
		}
	}
	
	public function change_name($user_id, $layout_id, $name) {
		// check permission
		$info = $this->info($layout_id, 'creator_user_id');
		if ($info['creator_user_id']!=$user_id)
			return false;
			
		$valid = validate::min_length($name, 6);
		
		if ($valid) {
		return $this->db->update("UPDATE ".$this->prefix."_layouts SET layout_name = '".$name."' WHERE layout_id = ".$layout_id);
		} else {
			return false;
		}
	}
	
	public function delete($user_id, $layout_id) {
		// check permission
		$info = $this->info($layout_id, 'creator_user_id');
		if ($info['creator_user_id']!=$user_id)
			return false;
		return $this->db->delete("DELETE FROM ".$this->prefix."_layouts WHERE layout_id = ".$layout_id);
	}
	
	public function info($id, $field = "*") {
		$r = $this->db->select("SELECT " . $field . " FROM ".$this->prefix."_layouts 
				WHERE layout_id = ".$id." LIMIT 1;");
				
		if (!$r) return false;
		return (isset($r['data'][0])) ? $r['data'][0] : false;
	}
	
	public function get_current_layout($blog_id) {
		$r = $this->db->select("SELECT * FROM ".$this->prefix."_pages
			WHERE blog_id = ".$blog_id." LIMIT 1;");
		if (!$r) return false;
		
		if ($r['rows'] == 0)
			return false;
		return $r['data'][0]['page_layout'];
	}
	
	public function get_layout($layout_id) {
		$r = $this->db->select("SELECT * FROM ".$this->prefix."_layouts
			WHERE layout_id = ".$layout_id." LIMIT 1;");
		if (!$r) return false;
		
		if ($r['rows'] == 0)
			return false;
		return $r['data'][0]['layout_data'];
	}

	public function get_layout_list($user_id, $limit = 10, $offset = 0) {
		$r = $this->db->select("SELECT * FROM ".$this->prefix."_layouts
			WHERE creator_user_id = ".$user_id." LIMIT ".$offset.", ".$limit.";");
		if (!$r) return false;
		$this->db->xss_safe($r['data'], array('layout_name'));
		return $r['data'];
	}
	
	public function save($layout_id, $design) {
		$this->db->safe($design);
		
		if ($this->get_layout($layout_id)) {
			return $this->db->update("UPDATE ".$this->prefix."_layouts SET layout_data = '" . $design . "' WHERE layout_id = ".$layout_id." LIMIT 1;");
		} 
		return false;
	}
	
	public function use_design($user_id, $blog_id, $layout_id) {
		return $this->db->select("UPDATE ".$this->prefix."_pages 
			SET page_layout = ".$layout_id." WHERE blog_id = ".$blog_id." AND page_owner = " . $user_id . " ");
	}
}