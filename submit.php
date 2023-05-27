<?php
/*
 * Form-post handler
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @create date		20 mars 2010
 * @last-modified	7 may 2010
 * @version			1.1
 * ---------------------------------------- 

*/
if (basename($_SERVER['PHP_SELF']) != 'index.php') die();

$type = form::get_submit_type();

/* if ($type == 'thread_add') {
	$fields = array('name','subject','message');
	if (!form::check_captcha()) {
		redirect(ROOT);
	}
}
if ($type == 'post_add') {
	$fields = array('id','name','message','reply_post');
	if (!form::check_captcha()) {
		redirect(ROOT);
	}
} */

// user
if ($type == 'login') {
	$fields = array('name','password');
}
if ($type == 'forgot') {
	$fields = array('name');
}
if ($type == 'recovery-password') {
	$fields =  array('name','key','new-password');
}
if ($type == 'register') {
	$fields = array('name','password','email','recruit-by');
}
// login required
if ($type == 'change-password') {
	$fields = array('old-password','new-password');
}
if ($type == 'change-name') {
	$fields = array('name');
}
if ($type == 'change-email') {
	$fields = array('email');
}

// blog
if ($type == 'blog-comment') {
	$fields = array('id','name','url','email','message');
	$int = array('id');
	if (!form::check_captcha()) {
		if (isset($_SERVER['HTTP_REFERER'])&&validate::url($_SERVER['HTTP_REFERER'])) {
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect(ROOT);
		}
	}
}

// login required
if ($type == 'add-blog') {
	$fields = array('name','url');
}

if ($type == 'delete-blog') {
	$fields = array('url');
}

if ($type == 'write-blog') {
	$fields = array('url','headline','content');
}

if ($type == 'edit-blog') {
	$fields = array('id','headline','content');
	$int = array('id');
}

// design
// login required
if ($type == 'add-design') {
	$fields = array('name','url');
}
if ($type == 'use-design') {
	$fields = array('id','url');
	$int = array('id');
}

// share
// login required
if ($type == 'share-blog') {
	$fields = array('url','users');
}
if ($type == 'share-blog-add') {
	$fields = array('url','name');
}
if (!$int)
	$int = array();
$post_data = form::get_posts($type, $fields, $int);

// No valid data found
if (!$post_data)
	redirect(ROOT);
	
// Prevent Sql injection.
#foreach ($post_data AS $k => $v) {
	$db->safe($v);
#	$post_data[$k] = $v; 
#}

// User
if ($type == 'login') {
	$success = $user->login($post_data['name'], $post_data['password']);
	if ($success) {
		setFlash('global', addClass('Inloggning lyckades! Välkommen ' . $post_data['name'] . '!', 'success'));
		if (getFlash('facebook/add')) {
			// facebook
			$oauth = new sso(DB_PREFIX, $db);
			if ($fb_auth = $oauth->get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET)) {
				$oauth->facebook_add($user->logged_in(),$fb_auth['uid']);
			}
		}
		$time = $user->last_login($user->logged_in(),1);
		$new_comments = $blog->count_all_new_comments($user->logged_in(),$time);
		// javascript notification api
		ob_start(); ?>
<script>
var notify;
$(document).ready(function () {
	if (window.webkitNotifications) {
		if (window.webkitNotifications.checkPermission() == 0) { // 0 is PERMISSION_ALLOWED
			//$("<a>Avaktivera skrivbordsnotifieringar</a>").appendTo("span.success").click(function () {
			//	window.webkitNotifications.requestPermission();
			//});  
			//$("<a>Senast inloggad</a>").appendTo("span.success").click(function () {
				notify = window.webkitNotifications.createNotification('', 'Senast inloggad', '<?php echo $time;?> och <?php echo $new_comments;?> nya kommentarer')
				notify.ondisplay = function () {
					window.setTimeout("notify.cancel()", 5000);
				}
				notify.show();
			//});
		} else {
			$("<a>Aktivera skrivbordsnotifieringar</a>").appendTo("span.success").click(function () {
				window.webkitNotifications.requestPermission();
			});  
		}
	}
});
</script>
		<?php
		$javascript = ob_get_contents();
		ob_end_clean();
		setFlash('global', $javascript);
		redirect(ROOT);
	} else {
		setFlash('user/login', addClass('Inloggning misslyckades!', 'error'));
		redirect(ROOT . "login");
	}
}

// User
if ($type == 'forgot') {
	$success = $user->forgot($post_data['name']);
	if ($success) {
		setFlash('user/forgot', addClass('Ett mejl har skickats till din e-post!', 'success'));
		
	} else {
		setFlash('user/forgot', addClass('Ett fel uppstod, var vänlig och kontakta mig!', 'error'));
	}
	redirect(ROOT);
}

// User
if ($type == 'recovery-password') {
	$user_id = $user->get_id_by("username", $post_data['name']);
	$success = $user->forgot_check($user_id,$post_data['key']);
	if ($success) 
		$success = $user->change_password(false, $post_data['new-password'], false, $user_id);
		
	if ($success) {
		$user->login($post_data['name'], $post_data['new-password']);
		setFlash('global', addClass('Lösenordet har ändrats!', 'success'));
		redirect(ROOT);
	} else {
		setFlash('global', addClass('Lösenordbyte misslyckades!', 'error'));
		redirect(ROOT . 'forgot/' . $post_data['name'] . '/' . $post_data['key']);
	}
}

// User
if ($type == 'register') {
	$success = $user->register($post_data['name'], $post_data['password'], $post_data['email'], $post_data['recruit-by']);
	if ($success) {
		setFlash('global', addClass('Registrering lyckades! Välkommen ' . $post_data['name'] . '!', 'success'));
		
		// facebook
		$oauth = new sso(DB_PREFIX, $db);
		if ($fb_auth = $oauth->get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET)) {
			$oauth->facebook_add($user->logged_in(),$fb_auth['uid']);
		}
		redirect(ROOT);
	} else {
		setFlash('user/register', addClass('Registrering misslyckades!', 'error'));
		redirect(ROOT . "login");
	}
}

// comment
if ($type == 'blog-comment') {
	$success = $blog->add_comment($post_data['id'], $post_data['name'], $post_data['email'], $post_data['message'], $post_data['url']);

	if ($success) {
		setFlash('blog/comment', 'Kommentaren har postats!', 'success');
	} else {
		setFlash('blog/comment', 'Ett fel inträffade!', 'error');
	}
	// redirect
	if (isset($_SERVER['HTTP_REFERER'])&&validate::url($_SERVER['HTTP_REFERER'])) {
			redirect($_SERVER['HTTP_REFERER']);
	} else {
		$post = $blog->get_post(false, $post_data['id']);
		$info = $blog->info($post['blog_id']);
		redirect(uri::scheme() . $info['blog_url'] . "." . uri::host() . '/post/' . $post_data['id'] . '/' . $post['date_year'] . '/' . $post['date_month'] . '/' . $post['date_day'] . '/' . $post['post_url']);
		redirect(ROOT);
	}
}

// check login
if (!$user->logged_in()) { 
	redirect(ROOT . 'login'); 
}

// User
if ($type == 'change-password') {
	$success = $user->change_password($post_data['old-password'], $post_data['new-password']);
	if ($success) {
		setFlash('account', 'Lösenordet har ändrats!', 'success');
	} else {
		setFlash('global', addClass('Lösenord byte misslyckades!', 'error'));
	}
	redirect(ROOT . 'account');
}
// User
if ($type == 'change-name') {
	$success = $user->change_username($post_data['name']);
	if ($success) {
		setFlash('account', 'Ditt smeknamn har ändrats!', 'success');
	} else {
		setFlash('account', 'Smeknamn byte misslyckades!', 'error');	
	}
	redirect(ROOT . 'account');
}
// User
if ($type == 'change-email') {
	$success = $user->change_email($post_data['email']);
	if ($success) {
		setFlash('account', 'Ditt email har ändrats!', 'success');
	} else {
		setFlash('account', 'E-post byte misslyckades!', 'error');	
	}
	redirect(ROOT . 'account');
}

// Blog
if ($type == 'add-blog') {
	$success = $blog->add($user->logged_in(), $post_data['name'], $post_data['url']);
	if ($success) {
		setFlash('global', 'Ny blogg har skapats! ' . $post_data['name'], 'success');
		redirect(ROOT . 'post/new/' . $success);
	} else {
		setFlash('global', 'Bloggskapandet misslyckades! ', 'error');
		redirect(ROOT);
	}
}

// Blog
if ($type == 'delete-blog') {
	$blog_id = $blog->get_id_by('blog_url', $post_data['url']);
	$success = $blog->delete($user->logged_in(), $blog_id);
	if ($success) {
		setFlash('global', 'Bloggen har blivit borttagen!', 'success');
	} else {
		setFlash('global', 'Borttagningen misslyckades!', 'error');
	}
	redirect(ROOT);
}

// post
if ($type == 'write-blog') {
	$blog_id = $blog->get_id_by('blog_url', $post_data['url']);

	$success = $blog->add_post($user->logged_in(), $blog_id, $post_data['headline'], $post_data['content']);
	if ($success) {
		setFlash('global', 'Inlägget har publicerats!', 'success');
		
		// javascript backup reset
		ob_start(); ?>
<script>
$(document).ready(function () {
	if (window.localStorage) {
		window.localStorage['backup-new-post-headline'] = "";
		window.localStorage['backup-new-post-data'] = "";
	}
});
</script>
		<?php
		$javascript = ob_get_contents();
		ob_end_clean();
		setFlash('global', $javascript);
		
		redirect(ROOT . 'posts/' . $post_data['url']);
	} else {
		setFlash('global', 'Något fel inträffades!', 'error');
		setFlash('blog/write/headline', $post_data['headline']);
		setFlash('blog/write/content', $post_data['content']);
		redirect(ROOT . 'post/new/' . $post_data['url']);
	}
}

// post
if ($type == 'edit-blog') {
	$success = $blog->update_post($user->logged_in(), $post_data['id'], $post_data['headline'], $post_data['content']);

	if ($success) {
		$post = $blog->get_post($user->logged_in(), $post_data['id']);
		$info = $blog->info($post['blog_id']);
		setFlash('global', 'Inlägget har ändrats!', 'success');
		redirect(ROOT . 'posts/' . $info['blog_url']);
	} else {
		setFlash('global', 'Något fel inträffades, inlägget har inte ändrats än!', 'error');
		setFlash('blog/edit/headline', $post_data['headline']);
		setFlash('blog/edit/content', $post_data['content']);
		redirect(ROOT . 'post/edit/' . $post_data['id']);	
	}
}

// design
if ($type == 'add-design') {
	$design = new design(DB_PREFIX, $db);
	$success = $design->add($user->logged_in(), $post_data['name']);
	
	if ($success) {
		redirect(ROOT . 'design/edit/' . $post_data['url'] . '/' . $success);
	} else {
		setFlash('design', 'Misslyckades att skapa', 'error');
		redirect(ROOT . 'design/' . $post_data['url']);
	}
}

// design
if ($type == 'use-design') {
	$design = new design(DB_PREFIX, $db);
	$blog_id = $blog->get_id_by('blog_url', $post_data['url']);
	
	// check permission
	if ($blog->check_permission($user->logged_in(), $blog_id, 'blog', 'edit'))
		$success = $design->use_design($user->logged_in(), $blog_id, $post_data['id']);

	if ($success) {
		setFlash('design', 'Ny design används nu!', 'success');
		redirect(ROOT . 'design/' . $post_data['url']);
	} else {
		setFlash('design', 'Misslyckades att ändra design', 'error');
		redirect(ROOT . 'design/' . $post_data['url']);
	}
}

// share
if ($type == 'share-blog') {
	$blog_id = $blog->get_id_by('blog_url', $post_data['url']);
	
	#$list = $user->get_friends($user->logged_in());
	$list = $blog->get_user($blog_id, getrandmax());
	
	$delete_users = array();
	foreach ($list AS $v) {
		$delete_users[] = $v['user_id'];
	}
	if (!empty($post_data['users'])) {
		$add_users = array();
		foreach ($post_data['users'] AS $username) {
			$add_users[] = $user->get_id_by('username', $username);
		}
		$t = $delete_users;
		$delete_users = array_diff($delete_users, $add_users);
		$add_users = array_diff($add_users, $t);
	}
	// exclude blog owner
	$info = $blog->info($blog_id, 'creator_user_id');
	$delete_users = array_diff($delete_users, array($info['creator_user_id']));
	$delete_users = array_diff($delete_users, array($user->logged_in()));
	
	foreach ($delete_users AS $v) {
		$blog->delete_permission_constraint($v, $blog_id, 'blog', array('full'));
	}
	if (!empty($post_data['users'])) {
		foreach ($add_users AS $v) {
			$blog->add_permission_constraint($v, $blog_id, 'blog', array('view','add','edit'));
		}
	}
	
	$success = !$db->error;
	
	if ($success) {
		setFlash('global', 'Du delar nu med ' . sizeof($add_users) . ' nya personer och ' . sizeof($delete_users) . ' färre personer!', 'success');
	} else {
		setFlash('global', 'Något fel inträffades!', 'error');
	}
	redirect(ROOT . 'settings/' . $post_data['url']);
}

// share
if ($type == 'share-blog-add') {
	$blog_id = $blog->get_id_by('blog_url', $post_data['url']);
	
	$user_id = $user->get_id_by('username', $post_data['name']);
	
	$list = $blog->get_user($blog_id, getrandmax());
	
	if (is_numeric($user_id)&&!in_array($user_id,$list)) {
		$blog->add_permission_constraint($user_id, $blog_id, 'blog', array('view','add','edit'));
		$success = !$db->error;
	} else {
		$success = false;
	}
	
	if ($success) {
		setFlash('global', 'Du delar nu med ' . $post_data['name'] . '!', 'success');
	} else {
		setFlash('global', 'Du delar redan med ' . $post_data['name'] . '!', 'error');
	}
	redirect(ROOT . 'settings/' . $post_data['url']);
}

redirect(ROOT);
?>