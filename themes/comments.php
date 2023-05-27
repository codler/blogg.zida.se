<?php 
/*
 * template: comment page
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	18 May 2010
 * @version			1.0
 * ----------------------------------------
*/
if (!defined('BASE_DIR')) die('No direct script access allowed');

if (!$user->logged_in()) {
	return require_once(get_page('/login'));
}

// Get blog url
$blog_url = get_url("comments");
$blog_id = $blog->get_id_by('blog_url', $blog_url);

// check permission
if (!$blog->check_permission($user->logged_in(), $blog_id, 'blog', 'delete'))
	redirect(ROOT);
	
$info = $blog->info($blog_id);
$title .= " - Alla Kommentarer - " . $info['blog_name'];

// ----------------------------------------------
?>
<h1>Kommentarer - <?php echo $info['blog_name'];?></h1>
<table>
<?php 
$pagination_limit = 10;
$pagination = (is_numeric(get_url("comments",1))) ? abs(get_url("comments",1))*$pagination_limit : 0;
$list = $blog->get_all_comments($blog_id, $pagination_limit, $pagination);
if (count($list)>0) :
foreach ($list AS $v) : ?>
	<tr>
		<td><p><a href="<?php echo uri::scheme() . $blog_url . "." . uri::host(); ?>/post/<?php echo $v['post_id']; ?>/<?php echo $v['date_year']; ?>/<?php echo $v['date_month']; ?>/<?php echo $v['date_day']; ?>/<?php echo $v['post_url']; ?>" alt="<?php echo $v['post_headline']; ?>"><?php echo $v['post_headline']; ?></a></p>
		<p>Från <i><?php echo $v['comment_name']; ?></i></p>
		<p><?php echo $v['comment_message']; ?></p>
		</td>
		<td><?php echo happy_date($v['comment_date']); ?></td>
		<td><a href="<?php echo ROOT; ?>comment/delete/<?php echo $v['comment_id']; ?>" onclick="return confirm('Är du säker på att ta bort? Det går ej att ångra!');">Ta bort</a></td>
	</tr>
<?php endforeach; ?>
</table>
<?php 
	$total_post = $blog->count_comment($blog_id);
	if ($total_post > $pagination_limit) : 
		$total_page = ceil($total_post/$pagination_limit);
		for ($i = 0; $i < $total_page; $i++) : 
			if ($pagination/$pagination_limit == $i) : ?>
			<?php echo ($i+1); ?>
			<?php else : ?>
		<a href="<?php echo ROOT; ?>comments/<?php echo $blog_url; ?>/<?php echo $i; ?>"><?php echo ($i+1); ?></a>
	<?php	endif; 
		endfor; 
	endif;
endif;
?>