<?php 
/*
 * template: Settings page
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

// Get blog url
$blog_url = get_url("settings");
$blog_id = $blog->get_id_by('blog_url', $blog_url);

// check permission
if (!$blog->check_permission($user->logged_in(), $blog_id, 'blog', 'edit'))
	redirect(ROOT);
#if (!$blog->check_permission($user->logged_in(), $blog_id, 'blog', 'delete'))
#	redirect(ROOT);
$info = $blog->info($blog_id);

$title .= " - Inställningar ";

?>
<h1>Blogg inställningar - <?php echo $info['blog_name']; ?></h1>
<h2>Dela bloggning</h2>
<p>ger andra tillåtelse att blogga på din blogg</p>
<?php 
$list = $user->get_friends($user->logged_in());
$shared_users = $blog->get_user($blog_id, getrandmax());
$new_list = array();
foreach ($list AS $k => $v) {
	if (in_array($v['user_id'], $shared_users)) {
		$list[$k]['checked'] = true;
		$new_list[] = $list[$k];
	}
} 
if (sizeof($new_list)>0) :
	form::open('share-blog',ROOT . 'submit');
	form::hidden('url',$blog_url); ?>
	<table>
	<?php foreach ($new_list AS $key) : ?>
		<tr>
			<td><?php form::checkbox($key['username'],'users',$key['username'], 'l'.$key['username'], $key['checked']); ?></td>
		</tr>
	<?php 
	endforeach; ?>
	</table>
	<?php
	form::submit('Dela blogg \'' . $info['blog_name'] . '\' ');
	form::close();
endif;

// add user to share blog with
form::open('share-blog-add',ROOT . 'submit', 'post', 'add-share-blog');
form::hidden('url',$blog_url); 
form::text('Användare','name');
form::submit('Lägg till användare');
form::close();
?>



<?php 
// Remove blog
if ($blog->check_permission($user->logged_in(), $blog_id, 'blog', 'delete')) : ?>
<h2>Ta bort</h2>
<h3>OBS! Går ej att ångra!</h3>
<?php 
form::open('delete-blog',ROOT . 'submit', 'post', 'delete-blog');
form::hidden('url',$blog_url);
form::submit('Ta bort \'' . $info['blog_name'] . '\' ');
form::close();
?>
<?php endif; // permission ?>