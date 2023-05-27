<?php
/*
 * template: Edit post page
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	4 May 2010
 * @version			1.0
 * ----------------------------------------
*/
if (!defined('BASE_DIR')) die('No direct script access allowed');

if (!$user->logged_in()) {
	return require_once(get_page('/login'));
}

add_external(ROOT . 'external/ckeditor/ckeditor.js', "js");

// Get post_id
$post_id = get_url("post/edit");
$post = $blog->get_post($user->logged_in(), $post_id);

// check permission
if (!$blog->check_permission($user->logged_in(), $post['blog_id'], 'blog', 'edit'))
	redirect(ROOT);

$title .= " - Ändra post - " . $post['post_headline'];
?>
<h1>Ändra inlägg</h1>

<?php 
form::open('edit-blog',ROOT . 'submit');
form::hidden('id',$post_id);
$headline = getFlash('blog/write/headline');
if ($headline != false) {
	form::text('Titel', 'headline', $headline);
	form::message(false, 'content', getFlash('blog/write/content'), 'content');
} else {
	form::text('Titel', 'headline', $post['post_headline']);
	form::message(false, 'content', $post['post_content'], 'content');
}
form::submit('Uppdatera inlägget');
form::close();
?>
<script type="text/javascript">
	editor = CKEDITOR.replace('content', {
<?php $oauth = new sso(DB_PREFIX, $db);
if ($oauth->get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET)) : ?>
		filebrowserImageBrowseUrl : '/image/browse',
		<?php endif; ?>
		height: 300, 
		width: 580
	});
</script>