<?php 
/*
 * template: List post page
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
$blog_url = get_url("posts");
$blog_id = $blog->get_id_by('blog_url', $blog_url);

// check permission
if (!$blog->check_permission($user->logged_in(), $blog_id, 'blog', 'view'))
	redirect(ROOT);
$info = $blog->info($blog_id);
$title .= " - Alla inlägg - " . $info['blog_name'];

// ----------------------------------------------
?>
<h1>Mina inlägg - <?php echo $info['blog_name'];?></h1>
<table>
<?php 
$pagination_limit = 10;
$pagination = (is_numeric(get_url("posts",1))) ? abs(get_url("posts",1))*$pagination_limit : 0;
$list = $blog->get_post_list($blog_id, $pagination_limit, $pagination);
if (count($list)>0) :
foreach ($list AS $v) : ?>
	<tr>
		<td><a href="<?php echo uri::scheme() . $blog_url . "." . uri::host(); ?>/post/<?php echo $v['post_id']; ?>/<?php echo $v['date_year']; ?>/<?php echo $v['date_month']; ?>/<?php echo $v['date_day']; ?>/<?php echo $v['post_url']; ?>" alt="<?php echo $v['post_headline']; ?>"><?php echo $v['post_headline']; ?></a></td>
		<td><a href="<?php echo ROOT; ?>post/edit/<?php echo $v['post_id']; ?>/<?php echo $v['post_url']; ?>" alt="<?php echo $v['post_headline']; ?>">Ändra</a></td>
		<td><?php echo happy_date($v['post_date']); ?></td>
		<td><a href="<?php echo ROOT; ?>post/delete/<?php echo $v['post_id']; ?>" onclick="return confirm('Är du säker på att ta bort? Det går ej att ångra!');" alt="<?php echo $v['post_headline']; ?>">Ta bort</a></td>
	</tr>
<?php endforeach; ?>
</table>
<?php 
	$total_post = $blog->count_post($blog_id);
	if ($total_post > $pagination_limit) : 
		$total_page = ceil($total_post/$pagination_limit);
		for ($i = 0; $i < $total_page; $i++) : 
			if ($pagination/$pagination_limit == $i) : ?>
			<?php echo ($i+1); ?>
			<?php else : ?>
		<a href="<?php echo ROOT; ?>posts/<?php echo $blog_url; ?>/<?php echo $i; ?>"><?php echo ($i+1); ?></a>
	<?php	endif; 
		endfor; 
	endif;
endif;
?>