<?php

class permission_constraint extends constraint {
	// a=blog, b=post, c=user
	function __construct($a, $b, $c, $blog) {
		$this->blog = $blog;
		$this->a = $a;
		$this->b = $b;
		$this->c = $c;
		
		$this->a->add_constraint($this);
		$this->b->add_constraint($this);
		$this->c->add_constraint($this);
	}
	
	function forget_value() {
		$this->a->value = false;
		$this->b->value = false;
		$this->c->value = false;
	}
	
	function new_value($s) {
		// $a = blog, $b = post
		if ($s === $this->a) { // = $blog har ändrats
			// $b <= $a (dvs blog HAR ändrats) post SKA ändra
			
			foreach ($this->a->blogs AS $blog_id) {		
				// check if users is set, get users if it isnt set
				if ($this->c->users == false) {
					$users = $this->blog->get_user($blog_id, getrandmax());
				} else {
					$users = $this->c->users;
				}
				// set blog permission
				foreach ($users AS $user_id) {
					$this->blog->set_permission($user_id, $blog_id, 'blog', $this->a->value);
				}
			}
			$this->b->set($this->a->value, $this);
		} elseif ($s === $this->b) {
			// $a <= $b (dvs post HAR ändrats) blog SKA ändra
			
			// check if blogs is set, get blog related to post if it isnt set
			if ($this->a->blogs == false) {
				$this->blog->check = false;
				$post_data = $this->blog->get_post($this->c->users[0], $this->b->posts[0]);
				$this->blog->check = true;
				$this->a->blogs = array($post_data['blog_id']);
			}
			
			foreach ($this->a->blogs AS $blog_id) {
				// check if posts is set, get posts if it isnt set
				if ($this->b->posts == false) {
					$this->blog->check = false;
					$posts_data = $this->blog->get_post_list($blog_id, getrandmax());
					$this->blog->check = true;
					$posts = array();
					foreach ($posts_data AS $post) {
						$posts[] = $post['post_id'];
					}
				} else {
					$posts = $this->b->posts;
				}
				// check if users is set, get users if it isnt set
				if ($this->c->users == false) {
					$users = $this->blog->get_user($blog_id, getrandmax());
				} else {
					$users = $this->c->users;
				}
				foreach ($users AS $user_id) {
					// set post permission
					foreach ($posts AS $post) {
						$this->blog->set_permission($user_id, $post, 'post', $this->b->value);
					} 
				}
			}
			#$this->a->set($this->b->value, $this);
		} elseif ($s === $this->c) { // user
			if ($this->a->blogs==false&&$this->b->posts==false) {
				
			}
			$this->b->set($this->c->value, $this);
			$this->a->set($this->c->value, $this);
			
		}
	}
}

class blog_permission_connector extends connector {
	function __construct($blogs=false) {
		$this->blogs = (is_array($blogs)||!$blogs) ? $blogs : array($blogs);
	}
}
class post_permission_connector extends connector {
	function __construct($posts=false) {
		$this->posts = (is_array($posts)||!$posts) ? $posts : array($posts);
	}
}
class user_permission_connector extends connector {
	function __construct($users=false) {
		$this->users = (is_array($users)||!$users) ? $users : array($users);
	}
}
/* $b = new blog_permission_connector();
$p = new post_permission_connector(array(7));
$u = new user_permission_connector(array(1));
$blog = new blog(DB_PREFIX, $db);

$per = new permission_constraint($b,$p,$u,$blog);

// set new blog permission
#$b->set(array('add'=> array('full'))); // all post will have 'full' permission to specific users

#$p->set(array('add'=> array('add'))); // specific posts will have 'full' permission to specific users
#$u->set(array('add'=> array('full')));
 */
// ===========================================
class constraint {
	function __construct($a, $b) {
		$this->a = $a;
		$this->b = $b;
		
		$this->a->add_constraint($this);
		$this->b->add_constraint($this);
	}
	
	function forget_value() {
		$this->a->value = false;
		$this->b->value = false;
	}
	
	function new_value($s) {
		if ($s !== $this->a) {
			$this->a->value = $this->b->value - 1;
		} else {
			$this->b->value = $this->a->value + 1;
		}
	}
}

class connector {
	public $constraints = array();
	public $value = false;
	function add_constraint($c) {
		$this->constraints[] = $c;
	}
	
	function set($s,$setter=false) {
		if (!$this->value) {
			$this->value = $s;
			foreach ($this->constraints AS $constraint) {
				#if ($constraint != $setter) {
					$constraint->new_value($this);
				#}
			}
		}
	}
}

/* $con1 = new connector();
$con2 = new connector();
$net = new constraint($con1, $con2);
$con1->set(2);
echo $con1->value;
echo $con2->value;
$con2->set(5);
echo $con1->value;
echo $con2->value; */

?>