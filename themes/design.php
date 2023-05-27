<?php 
/*
 * template: Design page
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	8 May 2010
 * @version			1.0
 * ----------------------------------------
*/
if (!defined('BASE_DIR')) die('No direct script access allowed');
$title .= " - Design";

if (!$user->logged_in()) {
	return require_once(get_page('/login'));
}

// Get blog url
$blog_url = get_url("design");
$blog_id = $blog->get_id_by('blog_url', $blog_url);

echo getFlash('design');
?>
<h1>Design</h1>
<?php
$design = new design(DB_PREFIX, $db);
$list = $design->get_layout_list($user->logged_in());
if (sizeof($list)>0) :
?>
<h2>Mina</h2>
<?php 
form::open('use-design',ROOT . 'submit');
form::hidden('url',$blog_url); 
$use_design = $design->get_current_layout($blog_id);
?>

<table>
<?php foreach ($list AS $key) : ?>
	<tr>
		<td><?php form::radio(false,'id',$key['layout_id'], false,($key['layout_id']==$use_design)? true : false); ?></td>
		<td class="editable" design_id="<?php echo $key['layout_id']; ?>"><?php echo $key['layout_name']; ?></td>
		<td><?php echo happy_date($key['layout_date']); ?></td>
		<td><a href="<?php echo ROOT; ?>design/edit/<?php echo $blog_url; ?>/<?php echo $key['layout_id']; ?><?php if (DEBUG) : echo '?debug=0'; endif; ?>" alt="<?php echo $key['layout_name']; ?>">Redigera</a></td>
		<td><a href="<?php echo ROOT; ?>design/delete/<?php echo $key['layout_id']; ?><?php if (DEBUG) : echo '?debug=0'; endif; ?>" onclick="return confirm('Är du säker på att ta bort? Det går ej att ångra!');" alt="<?php echo $key['layout_name']; ?>">Ta bort</a></td>
	</tr>
<?php endforeach; ?>
</table>
<script>
(function () {
	$(".editable").click(function () {
		var selected = $(this);
		var text = selected.text();
		selected.html("");
		$("<input />").val(text).appendTo(selected).select().blur(function () {
			selected.text($(this).val());
			if ($(this).val()!= text) {
				$.post("<?php echo ROOT; ?>fetch<?php if (DEBUG) : echo '?debug=0'; endif; ?>", 
					{ 	type: "change-design-name", 
						id : selected.attr('design_id'),
						name: $(this).val()
					},
					function (data) {
						if (data == "false") {
							selected.text(text);
						}
					}
				);
			}
		});
	});
})();
</script>
<?php
form::submit('Använd designen');
form::close();
?>
<br />
<?php 
endif; 

if (sizeof($list)<10) :
?>
<div class="box">
	<h2>Skapa ny design!</h2>
	<p>Minst 6 tecken.</p>
	<?php
	form::open('add-design',ROOT . 'submit', 'post', 'add-design');
	form::hidden('url', $blog_url);
	form::text('Design namn','name');
	form::submit('Skapa ny design');
	form::close();
	?>
</div>
<?php endif; ?>