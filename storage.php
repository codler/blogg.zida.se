<?php if (!defined('BASE_DIR')) die('No direct script access allowed');

if ($_GET['example']) { ?>
{"global_settings":{"view_content":true,"component_zIndex":521,"inner_background":"http://","outer_background":"http://blogg.zida.se/assets/images/bg.png","worksheet_height":"600","worksheet_width":"883","version":0.5},"0":{"layer_name":"Huvud bild","layer_id":9037173,"component_type":"image","css":{"background-color":"","color":"#FFFFFF","height":"329px","width":"883px","top":"-1px","left":"0.5px","font-size":"16px"},"image":"http://blogg.zida.se/assets/images/blogg_zida.png"},"1":{"layer_name":"Länk lista","layer_id":3840722,"component_type":"list","css":{"background-color":"","color":"#FFFFFF","height":"56px","width":"178px","top":"228px","left":"150px","font-size":"16px"},"list":{"0":{"url":"http://www.zencodez.net","name":"Min portfolio"},"1":{"url":"http://codler.blogspot.com/","name":"Min blogg"}}},"2":{"layer_name":"Text","layer_id":9735847,"component_type":"text","css":{"background-color":"#b3e6cf","color":"#000000","height":"30px","width":"401px","top":"293px","left":"164px","font-size":"16px"},"text":"Lite text här och där"},"3":{"layer_name":"Rutnät","layer_id":578898,"component_type":"grid","css":{"background-color":"#cccccc","color":"#000000","height":"208px","width":"154px","top":"292px","left":"566px","font-size":"16px","padding":"10px"},"grid":{"0":1774205,"1":9579079}},"4":{"layer_name":"Text i rutnät 1","layer_id":1774205,"component_type":"text","css":{"background-color":"#f7f0b2","color":"#991399","height":"111px","width":"154px","top":"10px","left":"10px","font-size":"16px"},"text":"Rutnät är till för att samla flera verktyg i samma låda"},"5":{"layer_name":"Text i rutnät 2","layer_id":9579079,"component_type":"text","css":{"background-color":"#ff9cf5","color":"#2a3000","height":"99px","width":"154px","top":"77px","left":"10px","font-size":"16px"},"text":"Då slipper du ändra storleken på varje verktyg i rutnätet"},"6":{"layer_name":"Lista - vänster","layer_id":204810,"component_type":"list","css":{"background-color":"#b0ff85","color":"#FFFFFF","height":"89px","width":"203px","top":"324px","left":"165px","font-size":"16px"},"list":{"0":{"url":"http://","name":"Dra från"}}},"7":{"layer_name":"Lista - höger","layer_id":8881463,"component_type":"list","css":{"background-color":"#b5ff8a","color":"#FFFFFF","height":"88px","width":"193px","top":"324px","left":"371px","font-size":"16px"},"list":{"0":{"url":"http://","name":"en lista"},"1":{"url":"http://","name":"till en annan"}}},"8":{"layer_name":"Text-9","layer_id":8906509,"component_type":"text","css":{"background-color":"","color":"#000000","height":"87px","width":"195px","top":"434px","left":"212px","font-size":"16px"},"text":"Lycka till\u000aMvh \u000aHan Lin Yap (aka Codler)"}}
<?php

return;
}

if (!$user->logged_in()) {
	return;
}

if ($_POST['design-save']) {
	$blog_url = get_url("storage");
	$layout_id = get_url("storage", 1);

	$blog_id = $blog->get_id_by('blog_url', $blog_url);

	// check permission
	if (!$blog->check_permission($user->logged_in(), $blog_id, 'blog', 'edit'))
		return;
	$design = new design(DB_PREFIX, $db);
	$design_info = $design->info($layout_id, 'creator_user_id');
	if ($design_info['creator_user_id']!=$user->logged_in())
		return;
	$_SESSION['design-storage'] = $_POST['design-save'];
	$design->save($layout_id, $_SESSION['design-storage']);
} else {
	echo stripslashes($_SESSION['design-storage']);
}
?>
