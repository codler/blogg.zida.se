<?php
require_once(BASE_DIR . "constraint.php");
/*
 * Blog Permission class
 *
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			24rd April 2010
 * @last-modified	24rd April 2010
 * @version			1.0
 * ----------------------------------------
 * Change log:
v1.0 - 24th April 2010
Created
 */

/* 
Depended files:
Blog (Parent class)

Methods:
 */
 
class blog_permission extends permission {
	function __construct() {
		$this->check = true;
	}
	
	// for array_map-function
	private function add_permission_mapping($x, $user_id, $blog_id, $type) {
		return "(".$user_id.", ".$blog_id.", " . $x . ",'".$type."')";
	}
	
	public function add_permission($user_id, $blog_id, $type, $permission='full') {
		if (!$this->check) return true;
		
		if (is_array($permission)) {
			$t = array();
			foreach ($permission AS $v) {
				$t = array_merge($t, $this->control($v));
			}
			$permission = $t;
		} else { 
			$permission = $this->control($permission);
		}
		
		$permission = array_map(array($this, "add_permission_mapping"), $permission, 
			array_fill(0,sizeof($permission),$user_id), 
			array_fill(0,sizeof($permission),$blog_id), 
			array_fill(0,sizeof($permission),$type));
		
		$sql = implode(',',$permission);
		return $this->db->insert("INSERT INTO ".$this->prefix."_blogs_permissions (user_id, blog_id, p_role, p_type)
			VALUES ".$sql);
	}

	public function add_permission_constraint($user_id, $blog_id, $type, $permission='full') {
		$this->permission_constraint($user_id, $blog_id, $type, $permission, 'add');
	}
	
	public function check_permission($user_id, $blog_id, $type, $role) {
		if (!$this->check) return true;

		$role = $this->control($role);
		$r = $this->db->select("SELECT blog_id FROM ".$this->prefix."_blogs_permissions 
			WHERE blog_id = ".$blog_id." AND user_id = ".$user_id." AND p_role = ".$role[0]." AND p_type = '".$type."' LIMIT 1;");
		
		if (!$r) return false;
		return ($r['rows']) ? true : false;
	}
	
	public function delete_permission($user_id, $blog_id, $type, $permission='full') {
		if (!$this->check) return true;
		
		if (is_array($permission)) {
			$t = array();
			foreach ($permission AS $v) {
				$t = array_merge($t, $this->control($v));
			}
			$permission = $t;
		} else { 
			$permission = $this->control($permission);
		}
		
		$role = implode(',', $permission);
		return $this->db->delete("DELETE FROM ".$this->prefix."_blogs_permissions 
			WHERE blog_id = ".$blog_id." AND user_id = ".$user_id." AND p_type = '".$type."' AND p_role IN (".$role.");");
	}
	
	public function delete_permission_constraint($user_id, $blog_id, $type, $permission='full') {
		$this->permission_constraint($user_id, $blog_id, $type, $permission, 'delete');
	}
	
	public function permission_constraint($user_id, $blog_id, $type, $permission='full', $constraint_type) {
		if (!is_array($permission)) $permission = array($permission);
	
		if ($type=='blog') {
			$b = new blog_permission_connector($blog_id);
			$p = new post_permission_connector();
			$u = new user_permission_connector($user_id);
			$per = new permission_constraint($b,$p,$u,$this);
			
			$u->set(array($constraint_type => $permission));
		}
		if ($type=='post') {
			$b = new blog_permission_connector($blog_id);
			$p = new post_permission_connector($user_id); // user_id = post_id
			$u = new user_permission_connector();
			$per = new permission_constraint($b,$p,$u,$this);
			
			$p->set(array($constraint_type => $permission));
		}
	}
	
	public function set_permission($user_id, $blog_id, $type, $permissions) {
		if (array_key_exists('delete', $permissions))
			$this->delete_permission($user_id, $blog_id, $type, $permissions['delete']);
		
		if (array_key_exists('add', $permissions))
			$this->add_permission($user_id, $blog_id, $type, $permissions['add']);
	}
}
?>