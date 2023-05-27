<?php
/**
 * Blog class
 * 
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			24rd April 2010
 * @last-modified	24rd April 2010
 * @version			1.0
 * @package 		modules
 */
/* ----------------------------------------
 * Change log:
v1.0 - 24th April 2010
Created
 */

/* 
Depended files:
validate.php (module)
database (module)
url (module)

Methods:
__construct($prefix, $db)
add($user_id, $name, $url)
delete($user_id, $blog_id)
info($id, $field = "*")
get_id_by($field, $value)
get_list($blog_id, $limit = 10, $offset = 0)
 */
 
class blog extends blog_permission {
	/*public function __call($name, $arguments) {
        return blog_helper::{$name};
    }*/

	function __construct($prefix, $db) {
		parent::__construct();
		$this->prefix = $prefix;
		$this->db = $db;
	}
	
	public function add($user_id, $name, $url) {
		$reserverd_url = array(
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
		
		$valid = isset($name,$url);
		
		$obj_url = new url($url);
		$url = substr($obj_url->blog(), 0, 100);
		$name = substr(trim($name), 0, 100);
		
		$valid &= validate::min_length($name, 4) &&
			validate::min_length($url, 6) &&
			// reserverd usernames
			!in_array($url,$reserverd_url);
		
		// Valid to db
		$valid &= !$this->occupied('blog_name', $name) &&
			!$this->occupied('blog_url', $url);
		
		if ($valid) {
			// Projects # FUTURE
			$project_id = $this->db->insert("INSERT INTO ".$this->prefix."_projects (user_id, project_create_date, project_name, project_url)
				VALUES (".$user_id.", NOW( ), '".$name."', '".$url."')");
			if (!$project_id) return false;
			
			// Blog
			$blog_id = $this->db->insert("INSERT INTO ".$this->prefix."_blogs (creator_user_id,	create_date, blog_name,	blog_url)
				VALUES (".$user_id.", NOW( ), '".$name."', '".$url."')");
			if (!$blog_id) return false;
			
			// add design to new blog
			$design = new design($this->prefix, $this->db);
			$layout_id = $design->add($user_id, 'Standard mall', '{"global_settings":{"view_content":true,"component_zIndex":503,"inner_background":"http://","outer_background":"http://blogg.zida.se/assets/images/bg.png","worksheet_height":"600","worksheet_width":"883"},"0":{"layer_name":"Blogg inlägg","layer_id":7378366,"component_type":"blog_posts","css":{"background-color":"#f2f2f2","color":"#000000","width":"577px","top":"112px","left":"202px","min-height":"406px"}},"1":{"layer_name":"Rubrik","layer_id":2585600,"component_type":"text","css":{"background-color":"#d5d9d7","color":"#000000","height":"38px","width":"577px","top":"73px","left":"202px"},"text":"'. addslashes($name) .'"},"2":{"layer_name":"Lista","layer_id":7504540,"component_type":"list","css":{"background-color":"#d9caca","color":"#ffffff","height":"100px","width":"173px","top":"111px","left":"11px"},"list":{"0":{"url":"http://","name":"länk 1"},"1":{"url":"http://","name":"länk 2"}}}}');
			
			// Pages
			$id = $this->db->insert("INSERT INTO ".$this->prefix."_pages	(project_id, blog_id, page_create_date, page_name, page_owner, page_parent_id, page_layout, page_url)
				VALUES (".$project_id.",".$blog_id.", NOW( ),	'".$name."', ".$user_id.", 0, ".$layout_id.", '/')");
			if (!$id) return false;
			// Blog_Permissions
			$id = $this->add_permission($user_id, $blog_id, 'blog');
			
			if (!$id) return false;
			return $url;
		}
		return false;
	}
	
	public function add_comment($post_id, $name, $email, $message, $url = false) {
		$valid = isset($post_id,$name,$email,$message) &&
			validate::email($email) &&
			validate::min_length($message, 3);
			
		// check from db
		$valid &= $this->get_post(false, $post_id);
		
		if ($valid) {
			if ($url && validate::url($url)) {
				$sql_url = ', comment_url';
				$sql_url2 = ",'" . $url . "'";
			}
			return $this->db->insert("INSERT INTO ".$this->prefix."_comments (post_id, comment_name, comment_email, comment_message".$sql_url.", comment_ip, comment_date)
				VALUES (".$post_id.", '".$name."', '".$email."', '".$message."'".$sql_url2.", '".$_SERVER['REMOTE_ADDR']."', NOW( ))");
		} else {
			return false;
		}
	}
	
	// blog_or_post_id - blog_id when update is false , post_id when update is true.
	public function add_post($user_id, $blog_or_post_id, $headline, $content, $update = false) {
		$valid = isset($headline,$content) &&
			validate::min_length($headline, 3) &&
			validate::min_length($content, 6);
		
		// check permission
		if (!$update) {
			$valid &= $this->check_permission($user_id, $blog_or_post_id, 'blog', 'add');
		} else {
			$post = $this->get_post($user_id, $blog_or_post_id);
			$valid &= $this->check_permission($user_id, $post['blog_id'], 'blog', 'edit');
		}
		
		if (!$valid) {
			return false;
		}
		
		$headline = substr(trim($headline), 0, 254);
		$obj_url = new url($headline);
		$url = substr($obj_url->post(), 0, 100);
		
		// HTMLPurifier
		require_once (dirname(__file__).'/../external/HTMLPurifier/HTMLPurifier.auto.php');
		$config = HTMLPurifier_Config::createDefault();
		$config->set('Core.Encoding', 'UTF-8');

		$purifier = new HTMLPurifier($config);
		$content = $purifier->purify(stripslashes($content));
		if ($update) {
			return $this->db->update("UPDATE ".$this->prefix."_blogs_posts 
				SET post_headline = '".$headline."', post_content = '".$content."', post_url = '".$url."'
				WHERE post_id = ".$blog_or_post_id." ");
		} else {
			// add post
			$post_id = $this->db->insert("INSERT INTO ".$this->prefix."_blogs_posts (author, blog_id, post_headline, post_content, post_date, post_url)
				VALUES (".$user_id.", ".$blog_or_post_id.", '".$headline."', '".$content."', NOW( ), '".$url."')");
		
			// add post permission
			$this->add_permission_constraint($post_id, $blog_or_post_id, 'post');
			/* $id = $this->add_permission($user_id, $post_id, 'post');
			$info = $this->info($blog_or_post_id, 'creator_user_id');
			if ($user_id != $info['creator_user_id']) {
				$id = $this->add_permission($info['creator_user_id'], $post_id, 'post');
			} 
			if (!$id) return false;
			*/
			return $post_id;
		}
	}
	
	public function count_blog($user_id) {
		$r = $this->db->select("SELECT COUNT(blog_id) AS count_blog FROM ".$this->prefix."_blogs WHERE creator_user_id = ".$user_id.";");
		if (!r) return false;
		return $r['data'][0]['count_blog'];
	}
	
	public function count_blog_shared($user_id) {
		$role = $this->control('view');
		$r = $this->db->select("SELECT COUNT(".$this->prefix."_blogs.blog_id) AS count_blog FROM ".$this->prefix."_blogs
			JOIN ".$this->prefix."_blogs_permissions 
			ON ".$this->prefix."_blogs_permissions.blog_id = ".$this->prefix."_blogs.blog_id
			WHERE ".$this->prefix."_blogs_permissions.user_id = ".$user_id." AND p_type = 'blog' AND p_role IN (".$role[0].");");
		if (!r) return false;
		return $r['data'][0]['count_blog']-$this->count_blog($user_id);
	}
	
	public function count_comment($blog_id) {
		$r = $this->db->select("SELECT COUNT(comment_id) AS count_comment FROM ".$this->prefix."_comments 
			JOIN ".$this->prefix."_blogs_posts
			ON ".$this->prefix."_comments.post_id = ".$this->prefix."_blogs_posts.post_id 
			WHERE blog_id = ".$blog_id.";");
		if (!r) return false;
		return $r['data'][0]['count_comment'];
	}
	
	public function count_all_new_comments($user_id, $time) {
		$blogs = $this->get_list($user_id, getrandmax());
		$new_blogs = array();
		foreach ($blogs AS $blog) {
			$new_blogs[] = $blog['blog_id'];
		}
		
		$r = $this->db->select("SELECT COUNT(comment_id) AS count_all_new_comments FROM ".$this->prefix."_comments 
			JOIN ".$this->prefix."_blogs_posts
			ON ".$this->prefix."_comments.post_id = ".$this->prefix."_blogs_posts.post_id 
			WHERE blog_id IN (".implode(",",$new_blogs).") AND comment_date > '" . $time . "';");
		if (!r) return false;
		return $r['data'][0]['count_all_new_comments'];
	}
	
	public function count_new_comments($blog_id, $time) {
		$r = $this->db->select("SELECT COUNT(comment_id) AS count_new_comments FROM ".$this->prefix."_comments 
			JOIN ".$this->prefix."_blogs_posts
			ON ".$this->prefix."_comments.post_id = ".$this->prefix."_blogs_posts.post_id 
			WHERE blog_id = ".$blog_id." AND comment_date > '" . $time . "';");
		if (!r) return false;
		return $r['data'][0]['count_new_comments'];
	}
	
	public function count_post($blog_id) {
		$r = $this->db->select("SELECT COUNT(post_id) AS count_post FROM ".$this->prefix."_blogs_posts WHERE blog_id = ".$blog_id.";");
		if (!r) return false;
		return $r['data'][0]['count_post'];
	}
	
	public function delete($user_id, $blog_id) {
		// check permission
		if (!$this->check_permission($user_id, $blog_id, 'blog', 'delete')) return false;
		/* $posts = $this->get_post_list($blog_id, getrandmax());
		foreach ($posts AS $post) {
			$this->delete_post($user_id, $post['post_id']);
		} */
		
		$this->delete_permission_constraint(false, $blog_id, 'blog');
		
		$info = $this->info($blog_id, 'blog_url');
		
		$this->db->delete("DELETE FROM ".$this->prefix."_projects WHERE project_url = '".$info['blog_url']."' AND user_id = ".$user_id);
		
		$this->db->delete("DELETE FROM ".$this->prefix."_blogs_permissions WHERE blog_id = ".$blog_id." AND user_id = ".$user_id);
		$this->db->delete("DELETE FROM ".$this->prefix."_blogs WHERE blog_id = ".$blog_id." AND creator_user_id = ".$user_id);
		$this->db->delete("DELETE FROM ".$this->prefix."_pages WHERE page_layout = ".$blog_id." AND page_owner = ".$user_id);
		
		// Get all post id
		$r = $this->db->select("SELECT post_id FROM ".$this->prefix."_blogs_posts
			WHERE blog_id = " . $blog_id . ";");
		if (!$r) return false;
		$posts = array();
		foreach ($r['data'] AS $v) {
			$posts[] = $v['post_id'];
		}
		
		$this->db->delete("DELETE FROM ".$this->prefix."_blogs_posts WHERE blog_id = ".$blog_id);
		
		$this->db->delete("DELETE FROM ".$this->prefix."_comments WHERE post_id IN (".implode(",",$posts).") ");
		
		#$this->delete_permission($user_id, $blog_id, 'blog');
		return true;
	}
	
	public function delete_comment($user_id, $comment_id) {
		$post_id = $this->get_post_id_by_comment($comment_id);
		// check permission
		if (!$this->check_permission($user_id, $post_id, 'post', 'delete')) return false;
		
		return $this->db->delete("DELETE FROM ".$this->prefix."_comments WHERE comment_id = ".$comment_id." ");
	}
	
	public function delete_post($user_id, $post_id) {
		// check permission
		if (!$this->check_permission($user_id, $post_id, 'post', 'delete')) return false;
		#$post = $this->get_post($user_id, $post_id);
		#$shared_users = $this->get_user($post['blog_id'], getrandmax());
		#foreach ($shared_users AS $users) {
			$this->delete_permission_constraint($post_id, false, 'post');
		#}
		#$this->delete_permission($user_id, $post_id, 'post');
		
		$this->db->delete("DELETE FROM ".$this->prefix."_comments WHERE post_id = ".$post_id." ");
		
		return $this->db->delete("DELETE FROM ".$this->prefix."_blogs_posts WHERE post_id = ".$post_id." AND author = ".$user_id);
	}
	
	public function info($id, $field = "*") {
		$r = $this->db->select("SELECT " . $field . " FROM ".$this->prefix."_blogs 
				WHERE blog_id = ".$id." LIMIT 1;");
				
		if (!$r) return false;
		return (isset($r['data'][0])) ? $r['data'][0] : false;
	}
	
	// Get all comments from a blog
	public function get_all_comments($blog_id, $limit = 10, $offset = 0) {
		$r = $this->db->select("SELECT comment_id, comment_name, comment_url, comment_message, comment_date, comment_email, 
			".$this->prefix."_comments.post_id, post_headline, post_url, post_date, 
			YEAR(post_date) AS date_year, MONTH(post_date) AS date_month, DAY(post_date) AS date_day FROM ".$this->prefix."_comments 
			JOIN ".$this->prefix."_blogs_posts
			ON ".$this->prefix."_comments.post_id = ".$this->prefix."_blogs_posts.post_id 
			WHERE blog_id = ".$blog_id." ORDER BY comment_date DESC LIMIT ".$offset.", ".$limit.";");
		if (!$r) return false;
		$this->db->xss_safe($r['data'], array('comment_name','comment_url','comment_message'));
		$this->db->xss_safe($r['data'], array('post_headline'));
		return $r['data'];
	}
	
	// Get singel comment
	public function get_comment($user_id, $comment_id) {
		$r = $this->db->select("SELECT * FROM ".$this->prefix."_comments 
			JOIN ".$this->prefix."_blogs_posts
			ON ".$this->prefix."_comments.post_id = ".$this->prefix."_blogs_posts.post_id 
			WHERE comment_id = ".$comment_id." LIMIT 1;");
		if (!$r) return false;
		// check permission
		if ($user_id!=false&&!$this->check_permission($user_id, $r['data'][0]['post_id'], 'post', 'view')) return false;
		
		$this->db->xss_safe($r['data'], array('comment_name','comment_url','comment_message'));
		return (isset($r['data'][0])) ? $r['data'][0] : false;
	}
	
	// Get comments by id
	public function get_comments($post_id, $limit = 10, $offset = 0) {
		$r = $this->db->select("SELECT * FROM ".$this->prefix."_comments 
			WHERE post_id = ".$post_id." LIMIT ".$offset.", ".$limit.";");
		if (!$r) return false;
		$this->db->xss_safe($r['data'], array('comment_name','comment_url','comment_message'));
		return $r['data'];
	}
	
	/* START Helper - comment */
	public function get_blog_id_by_comment($comment_id) {
		$r = $this->db->select("SELECT blog_id FROM ".$this->prefix."_comments 
			JOIN ".$this->prefix."_blogs_posts
			ON ".$this->prefix."_comments.post_id = ".$this->prefix."_blogs_posts.post_id 
			WHERE comment_id = " . $comment_id . " LIMIT 1;");
		if (!$r) return false;
		return $r['data'][0]['blog_id'];
	}
	
	public function get_post_id_by_comment($comment_id) {
		$r = $this->db->select("SELECT post_id FROM ".$this->prefix."_comments 
			WHERE comment_id = " . $comment_id . " LIMIT 1;");
		if (!$r) return false;
		return $r['data'][0]['post_id'];
	}
	/* END Helper - comment */
	
	public function get_id_by($field, $value) {
		$r = $this->db->select("SELECT blog_id FROM ".$this->prefix."_blogs 
			WHERE ".$field." = '".$value."' LIMIT 1;");
		if (!$r) return false;
		return $r['data'][0]['blog_id'];
	}
	
	public function get_list($user_id, $limit = 10, $offset = 0) {		
		$r = $this->db->select("SELECT * FROM ".$this->prefix."_blogs 
			WHERE creator_user_id = ".$user_id." LIMIT ".$offset.", ".$limit.";");
		if (!$r) return false;
		return $r['data'];
	}
	
	public function get_post($user_id, $post_id) {
		// check permission
		if ($user_id!=false&&!$this->check_permission($user_id, $post_id, 'post', 'view')) return false;
		
		$r = $this->db->select("SELECT post_id, blog_id, post_headline, post_content, post_url, post_date, 
			YEAR(post_date) AS date_year, MONTH(post_date) AS date_month, DAY(post_date) AS date_day
			FROM ".$this->prefix."_blogs_posts 
								WHERE post_id = ".$post_id." LIMIT 1;");
		if (!$r) return false;
		$this->db->xss_safe($r['data'], array('post_headline'));
		return (isset($r['data'][0])) ? $r['data'][0] : false;
	}
	
	public function get_post_list($blog_id, $limit = 10, $offset = 0) {
		$r = $this->db->select("SELECT post_id, post_headline, post_content, post_url, post_date, 
			YEAR(post_date) AS date_year, MONTH(post_date) AS date_month, DAY(post_date) AS date_day
			FROM ".$this->prefix."_blogs_posts 
			WHERE blog_id = ".$blog_id. " ORDER BY post_date DESC LIMIT ".$offset.", ".$limit.";");
		if (!$r) return false;
		$this->db->xss_safe($r['data'], array('post_headline'));
		return $r['data'];
	}
	
	public function get_shared_list($user_id, $limit = 10, $offset = 0) {
		$role = $this->control('view');
		// exclude own blogs
		$blogs = $this->get_list($user_id, getrandmax());
		$t = array();
		foreach ($blogs AS $blog) {
			$t[] = $blog['blog_id'];
		}
		if (sizeof($t)>0) {
			$sql_blog_id = " ".$this->prefix."_blogs_permissions.blog_id NOT IN (" . implode(",", $t) . ") AND ";
		} else {
			$sql_blog_id = "";
		}
		$r = $this->db->select("SELECT * FROM ".$this->prefix."_blogs
			JOIN ".$this->prefix."_blogs_permissions 
			ON ".$this->prefix."_blogs_permissions.blog_id = ".$this->prefix."_blogs.blog_id
			WHERE ".$sql_blog_id." ".$this->prefix."_blogs_permissions.user_id = ".$user_id." AND p_type = 'blog' AND p_role IN (".$role[0].") LIMIT ".$offset.", ".$limit.";");
		if (!$r) return false;
		return $r['data'];
	}
	
	public function get_user($blog_id, $limit = 10, $offset = 0) {
		$role = $this->control('edit');
		$r = $this->db->select("SELECT user_id FROM ".$this->prefix."_blogs_permissions
			WHERE p_type = 'blog' AND p_role IN (".$role[0].") AND blog_id = " . $blog_id . " LIMIT ".$offset.", ".$limit.";");
		if (!$r) return false;
		$users = array();
		foreach ($r['data'] AS $v) {
			$users[] = $v['user_id'];
		}
		return $users;
	}
	
	public function occupied($type, $value) {
		$r = $this->db->select("SELECT blog_id FROM ".$this->prefix."_blogs WHERE ".$type." = '".$value."' LIMIT 1;");
		if (!$r) return false;
		return ($r['rows']) ? true : false;
	}
	
	public function update_post($user_id, $post_id, $headline, $content) {
		return $this->add_post($user_id, $post_id, $headline, $content, true);
	}
}
?>