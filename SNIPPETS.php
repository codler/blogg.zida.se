<?php if (!defined('BASE_DIR')) die('No direct script access allowed');
// Login require pages
if (!$user->logged_in()) {
	return require_once(get_page('/login'));
}

// Assets
add_external(ROOT . 'external/ckeditor/ckeditor.js', 'js');

// Get blog url
$blog_url = get_url("design/delete");
$blog_id = $blog->get_id_by('blog_url', $blog_url);

// check permission
if (!$blog->check_permission($user->logged_in(), $blog_id, 'blog', 'add'))
	redirect(ROOT);
$info = $blog->info($blog_id);

?>

<?php
/**
 * Info class
 * 
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			13th May 2010
 * @last-modified	13th May 2010
 * @version			1.0
 * @package 		modules
 */
/* ----------------------------------------
 * Change log:
v1.0 - 13th May 2010
Created
 */
if (!defined('BASE_DIR')) die('No direct script access allowed');
$title .= " - Logga in"; ?>