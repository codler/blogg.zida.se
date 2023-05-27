<?php if (!defined('BASE_DIR')) die('No direct script access allowed');

if (get_url("design")!="preview") {

	if (!$user->logged_in()) {
		redirect(ROOT);
	}

	$blog_url = get_url("design/edit");
	$layout_id = get_url("design/edit",1);

	$blog_id = $blog->get_id_by('blog_url', $blog_url);

	// check permission
	if (!$blog->check_permission($user->logged_in(), $blog_id, 'blog', 'edit'))
		redirect(ROOT);
	$design = new design(DB_PREFIX, $db);
	$design_info = $design->info($layout_id, 'creator_user_id');
	if ($design_info['creator_user_id']!=$user->logged_in())
		redirect(ROOT);
		
	$info = $blog->info($blog_id);
	$data = $design->get_layout($layout_id);
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
<meta charset="utf-8" />
<title>Blogg design verktyg <?php echo DESIGN_TOOL_VERSION; ?> - skapad av Han Lin Yap</title>
<?php 

// Assets
add_external('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/themes/ui-lightness/jquery-ui.css', 'css');
add_external(ROOT . 'external/colorpicker/css/colorpicker.css', 'css');
add_external(ROOT . 'assets/css/design.css', 'css');

add_external('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.min.js', 'js');
add_external(ROOT . 'external/json2.js', 'js');
add_external(ROOT . 'external/colorpicker/js/colorpicker.js', 'js');
add_external(ROOT . 'external/easyTooltip.js', 'js');
add_external(ROOT . 'yap-goodies/js-0.1.js', 'js');
if (PRODUCTION) {
	add_external(ROOT . 'assets/js/design.init.js', 'js');
	add_external(ROOT . 'assets/js/design.functions.js', 'js');
} else {
	add_external(ROOT . 'assets/js/design.init-'.DESIGN_TOOL_VERSION.'.min.js', 'js');
	add_external(ROOT . 'assets/js/design.functions-'.DESIGN_TOOL_VERSION.'.min.js', 'js');
}

load_external(); 
?>
<!--[if IE]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type="text/javascript"> 
<!--
/* 
V.0.4 changes
Font-size setting

V.0.3 changes
Tooltip
Integrate with blogg.

V.0.2 changes
Prevent selection while dragging
Fix toolbar button was hidden when on small resolution screen.
Added key interaction. able to move components px-by-px
Fix colorpicker bug - restoring to original color did not change.
*/

<?php if ($data) : ?>
$().ready(function () {
	var data = "<?php echo addslashes($data); ?>";
	load_design_by_json($.parseJSON(data));
	
	global_settings.version = <?php echo DESIGN_TOOL_VERSION; ?>;
});
<?php endif; ?>
-->
</script>
</head>
<body>

<nav>
	<ul>
		<li>
	<!--<input type="checkbox" id="view_content" name="view_content" checked /><label for="view_content">Visa innehåll</label>--></li>
		<?php if (get_url("design")!="preview") { ?>
		<li><a href="<?php echo ROOT; ?>design/<?php echo $blog_url; ?>" onclick="return confirm('Osparade förändringar kommer att gå förlorad, vill du fortsätta?');">Tillbaka till Bloggen.</a></li>
		<?php } else { ?>
		<li><a href="<?php echo ROOT; ?>">Tillbaka till Bloggen.</a></li>
		<?php } ?>
		<li>Yta:</li> 
		<li>
			<label for="worksheet_width">Bredd:</label>
			<input class="input-number" type="text" id="worksheet_width" name="worksheet_width" value="800" />
		</li>
		<li>
			<select class="unit">
				<option value="px" selected>px</option>
				<option value="%">%</option>
			</select>
		</li>
		<li>
			<label for="worksheet_height">Höjd:</label>
			<input class="input-number" type="text" id="worksheet_height" name="worksheet_height" value="600" />
		</li>
		<li>
			<select class="unit">
				<option value="px" selected>px</option>
				<option value="%">%</option>
			</select>
		</li>
		<li><a id="settings-background">Bakgrund</a></li>
		<li><input type="button" id="design-reset" name="reset" value="Nytt" /></li>
		<?php if (get_url("design")!="preview") { ?>
		<li><input type="button" id="design-save" name="save" value="Spara" /></li>
		<?php } ?>
		<li><input type="button" id="design-load" name="load" value="Ladda" <?php if (get_url("design")=="preview") { ?>style="visibility: hidden;"<?php } ?>/></li>
		<?php if (get_url("design")!="preview") { ?>
		<li><input type="button" id="design-preview" name="preview" value="Spara och visa" /></li>
		<?php } ?>
		<li><input type="button" id="design-load-example" name="load-example" value="Ladda exempel" /></li>
		<?php if (get_url("design")=="preview") { ?>
		<li>Preview mode</li>
		<?php } ?>
	</ul>
</nav>
<div id="notice"><span></span></div>
<div id="tool">
	<div id="tool-dock"><a>DOCK</a></div>
	<div id="tool-section-component" class="tool-section">
		<div class="draghandler"><a>Verktyg</a></div>
		<div class="tool-section-content"></div>
	</div>
	<div id="tool-section-select" class=".tool-section">
		<div class="draghandler"><a>Valda lager inställning</a></div>
		<div class="tool-section-content"></div>
	</div>
	<div id="tool-section-layer" class=".tool-section">
		<div class="draghandler"><a>Lager</a></div>
		<div class="tool-section-content"></div>
	</div>
	<!--
	<div id="tool-section-history" class=".tool-section">
		<div class="draghandler"><a>History</a></div>
		<div class="tool-section-content"></div>
	</div>-->
</div>
<div id="worksheet">
	<div id="workspace"></div>
	<div id="worksheet-size">&nbsp;</div>
</div>

<div id="dialog-component-text" class="dialog" title="Komponent Text">
	<textarea></textarea>
</div>
<div id="dialog-component-image" class="dialog" title="Komponent Bild">
	<label for="url">Bild URL</label><input type="text" id="url" name="url" value="http://" />
</div>
<div id="dialog-component-list" class="dialog" title="Komponent Lista">
	<table>
	<thead>
		<tr>
			<th>URL</th>
			<th>Namn</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td></td>
			<td><a>Mer</a></td>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td><input type="text" id="url" name="url" value="http://" /></td>
			<td><input type="text" id="namn" name="namn" value="" /></td>
		</tr>
	</tbody>
	</table>	
</div>

<div id="dialog-settings-background" class="dialog" title="Inställning - Bakgrund">
	<p>Inre bakgrund</p>
	<label for="inner-background">Bild URL</label><input type="text" id="inner-background" name="url" value="http://" />
	<p>Yttre bakgrund</p>
	<label for="outer-background">Bild URL</label><input type="text" id="outer-background" name="url" value="http://" />
</div>

<div id="dialog-settings-events" class="dialog" title="Inställning - Händelser">
Tips, läs som en mening.
	<table>
	<thead>
		<tr>
			<th>Händelse</th>
			<th>Effekt</th>
			<th>Varaktighet</th>
			<th>Layer</th>
			<th>Ta bort</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td>Vid 
				<select id="events-event">
				</select>
			</td>
			<td>
				<select id="events-effect">
				</select>
			</td>
			<td>
				<select id="events-duration">
				</select>
			</td>
			<td>
				<select id="events-layer">
				</select>
			</td>
			<td><a class="create">Skapa händelsen</a></td>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td>Vid 
				<select class="events-event">
				</select>
			</td>
			<td>
				<select class="events-effect">
				</select>
			</td>
			<td>
				<select class="events-duration">
				</select>
			</td>
			<td>
				<select class="events-layer">
				</select>
			</td>
			<td><a class="delete">Ta bort</a></td>
		</tr>
	</tbody>
	</table>
</div>
</body>
</html>