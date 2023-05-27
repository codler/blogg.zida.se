<?php
/*
 * Rest - Querystring - handler
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	20 mars 2010
 * @version			1.0
 * ----------------------------------------
*/
if (basename($_SERVER['PHP_SELF']) != 'index.php') die();


// check login
if (!$user->logged_in()) { 
	redirect(ROOT . 'login'); 
}

$path = parse_url(uri::fqdn(), PHP_URL_PATH);

// post delete
if (strpos($path, "/post/delete/") === 0) {
	$post_id = get_url("post/delete");
	if (!is_numeric($post_id))
		redirect(ROOT);
		
	$post = $blog->get_post($user->logged_in(), $post_id);
	$info = $blog->info($post['blog_id']);
	
	$success = $blog->delete_post($user->logged_in(), $post_id);
	if ($success) {
		setFlash('global', 'Inlägget har tagits bort!', 'success');
	} else {
		setFlash('global', 'Något fel inträffades!', 'error');
	}
	redirect(ROOT . 'posts/' . $info['blog_url']);
}

// comment delete
if (strpos($path, "/comment/delete/") === 0) {
	$comment_id = get_url("comment/delete");
	if (!is_numeric($comment_id))
		redirect(ROOT);
		
	$comment = $blog->get_comment($user->logged_in(), $comment_id);
	$info = $blog->info($comment['blog_id']);
	
	$success = $blog->delete_comment($user->logged_in(), $comment_id);
	if ($success) {
		setFlash('global', 'Kommentaren har tagits bort!', 'success');
	} else {
		setFlash('global', 'Något fel inträffades!', 'error');
	}
	redirect(ROOT . 'comments/' . $info['blog_url']);
}

// design delete
if (strpos($path, "/design/delete/") === 0) {
	$layout_id = get_url("design/delete");
	if (!is_numeric($layout_id))
		redirect(ROOT);
	
	$design = new design(DB_PREFIX, $db);
	$success = $design->delete($user->logged_in(), $layout_id);
	
	if ($success) {
		setFlash('global', 'Designen har tagits bort!', 'success');
	} else {
		setFlash('global', 'Designen har ej tagits bort!', 'error');
	}
	redirect(ROOT);
}

// facebook disconnect
if (strpos($path, "/disconnect/") === 0) {
	// type = facebook
	$type = get_url("disconnect");
	
	$oauth = new sso(DB_PREFIX, $db);	
	if ($type == 'facebook')
		$success = $oauth->facebook_disconnect($user->logged_in());
		
	if ($success) {
		setFlash('global', $type . ' har kopplats bort från ditt konto!', 'success');
	} else {
		setFlash('global', 'Kunde ej koppla bort!', 'error');
	}
	redirect(ROOT);
}

redirect(ROOT);
?>