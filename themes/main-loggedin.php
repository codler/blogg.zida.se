<?php 
/*
 * template: Main logged in page
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	4 May 2010
 * @version			1.0
 * ----------------------------------------
*/
if (!defined('BASE_DIR')) die('No direct script access allowed');
$title .= " - Inloggad";
?>
<h1>Bloggar</h1>
<?php
$pagination_limit = 10;
$pagination = (is_numeric($_GET['p1'])) ? abs($_GET['p1'])*$pagination_limit : 0;
$list = $blog->get_list($user->logged_in(), $pagination_limit, $pagination);
if (sizeof($list)>0) :
?>
<h2>Mina</h2>
<?php zcTplFor(); ?>
<div class="box box-col-2">
	<b><a href="<?php echo uri::scheme(); ?>[key=blog_url].<?php echo uri::host(); ?>" alt="[key=blog_url]">[key=blog_name]</a></b> -
	<a href="<?php echo ROOT; ?>post/new/[key=blog_url]" alt="[key=blog_name]">Skriv inlägg</a> -
	<a href="<?php echo ROOT; ?>posts/[key=blog_url]" alt="[key=blog_name]">Visa inlägg</a> -
	<a href="<?php echo ROOT; ?>comments/[key=blog_url]">Kommentarer</a> -
	<a href="<?php echo ROOT; ?>design/[key=blog_url]">Designa</a> -
	<a href="<?php echo ROOT; ?>settings/[key=blog_url]">Inställningar</a>
</div>
<?php zcTplForEnd($list); ?>
<br />
<?php 
	$total_post = $blog->count_blog($user->logged_in());
	if ($total_post > $pagination_limit) : 
		$total_page = ceil($total_post/$pagination_limit);
		for ($i = 0; $i < $total_page; $i++) : 
			if ($pagination/$pagination_limit == $i) : ?>
			<?php echo ($i+1); ?>
			<?php else : ?>
		<a href="<?php echo ROOT; ?>?p1=<?php echo $i; ?>"><?php echo ($i+1); ?></a>
	<?php	endif; 
		endfor; 
	endif;
endif; 

$pagination = (is_numeric($_GET['p2'])) ? abs($_GET['p2'])*$pagination_limit : 0;
$list_shared = $blog->get_shared_list($user->logged_in(), $pagination_limit, $pagination); 
if (sizeof($list_shared)>0) : ?>
<h2>Delade</h2>
<h3>där andra delar sin blogg med dig.</h3>
<table>
<?php zcTplFor(); ?>
	<tr>
		<td><a href="<?php echo uri::scheme(); ?>[key=blog_url].<?php echo uri::host(); ?>" alt="[key=blog_url]">[key=blog_name]</a></td>
		<td><a href="<?php echo ROOT; ?>post/new/[key=blog_url]" alt="[key=blog_name]">Skriv</a></td>
		<td><a href="<?php echo ROOT; ?>posts/[key=blog_url]" alt="[key=blog_name]">Inlägg</a></td>
		<td><a href="<?php echo ROOT; ?>design/edit/[key=blog_id]<?php if (DEBUG) : echo '?debug=0'; endif; ?>">Designa</a></td>
		<td><a href="<?php echo ROOT; ?>settings/[key=blog_url]">Inställningar</a></td>
	</tr>
<?php zcTplForEnd($list_shared); ?>
</table>
<br />
<?php 
	$total_post = $blog->count_blog_shared($user->logged_in());
	if ($total_post > $pagination_limit) : 
		$total_page = ceil($total_post/$pagination_limit);
		for ($i = 0; $i < $total_page; $i++) : 
			if ($pagination/$pagination_limit == $i) : ?>
			<?php echo ($i+1); ?>
			<?php else : ?>
		<a href="<?php echo ROOT; ?>?p2=<?php echo $i; ?>"><?php echo ($i+1); ?></a>
	<?php	endif; 
		endfor; 
	endif;
endif; 
if (sizeof($list)<10) :
?>
<br />
<a id="add-blog-toggle">Skapa blogg</a>
<span>
<div class="box">
	<h2>Skapa blogg!</h2>
	<p>Minst 6 tecken.</p>
	<?php
	form::open('add-blog',ROOT . 'submit', 'post', 'add-blog');
	form::text('Bloggnamn','name');
	form::text('Bloggurl','url');
	form::submit('Skapa blogg');
	form::close();
	?>
</div>
</span>
<?php endif; ?>
<br /><br />
<?php $info = $user->info($user->logged_in()); ?>
<h2>Rekryteringslänk</h2>
<input style="width: 400px; font-size: 12px;" onclick="this.select();" onkeyup="this.value='<?php echo uri::fqdn(); ?>?r=<?php echo $info['username']; ?>';" type="text" name="recruitlink" id="recruitlink" value="<?php echo uri::fqdn(); ?>?r=<?php echo $info['username']; ?>" />