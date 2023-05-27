<?php 
/*
 * template: Add post page
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	4 May 2010
 * @version			1.0
 * ----------------------------------------
*/
if (!defined('BASE_DIR')) die('No direct script access allowed');
$title .= " - Post inlägg";

if (!$user->logged_in()) {
	return require_once(get_page('/login'));
}

add_external(ROOT . 'external/fancybox/jquery.fancybox-1.3.1.css', "css");

add_external(ROOT . 'external/ckeditor/ckeditor.js', "js");
add_external(ROOT . 'external/fancybox/jquery.fancybox-1.3.1.pack.js', "js");
add_external(ROOT . 'external/jquery.form.js', "js");
add_external(ROOT . 'external/json2.js', 'js');
	
// Get blog url
$blog_url = get_url("post/new");
$blog_id = $blog->get_id_by('blog_url', $blog_url);

// check permission
if (!$blog->check_permission($user->logged_in(), $blog_id, 'blog', 'add'))
	redirect(ROOT);
$info = $blog->info($blog_id);

?>
<h1>Nytt inlägg - <?php echo $info['blog_name']; ?></h1>
<?php 
form::open('write-blog',ROOT . 'submit', 'post', 'write-blog');
form::hidden('url',$blog_url);
form::text('Titel', 'headline', getFlash('blog/write/headline'));
form::message(false, 'content', getFlash('blog/write/content'), 'content');
form::submit('Posta inlägget');
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

// backup
(function () {
if (window.localStorage) {
	$("#write-blog").submit(function () {
		window.localStorage['backup-new-post-headline'] = $("input[name='headline']").val();
		window.localStorage['backup-new-post-data'] = CKEDITOR.instances.content.getData();
	});
	if (window.localStorage['backup-new-post-data']!= "") {
		$("<a>Återställ opostat inlägg</a>").insertAfter("input[name='headline']").click(function () {
			$("input[name='headline']").val(window.localStorage['backup-new-post-headline']);
			CKEDITOR.instances.content.setData(window.localStorage['backup-new-post-data']);
		});
	}
}
})();
	
</script>
<style>
/* Gallery */
#file-gallery, #flickr-gallery {
	background-color: rgba(255, 255, 255, 0.5);
	border: 1px solid #CCC;
	height: 100px;
	padding: 8px;
	position: relative;
	overflow: auto;
}

#file-gallery a, #flickr-gallery a {
	background: none;
	border: 2px solid #999;
	float: left;
	margin: 2px;
	padding: 0px;
}

#file-gallery a:hover, #flickr-gallery a:hover {
	border-color: #333;
}

#file-gallery a.add, #flickr-gallery a.add {
	border: none;
}

#file-gallery a.delete, #flickr-gallery a.delete {
	border: none;
	float: right;
}

#file-gallery img, #flickr-gallery img {
	height: 66px;
	width: 66px;
}

#file-gallery div, #flickr-gallery div {
	background-color: #FFF;
	min-height: 10px;
	position: absolute;
	display: none;
	width: 63px;
}
#file-gallery div img, #flickr-gallery div img {
	display: none;
}
</style>
<?php if ($info['creator_user_id'] == $user->logged_in()) : ?>
<h2>Galleri</h2>
<div id="file-gallery">
<?php $image_url = uri::scheme() . $blog_url . "." . uri::host() . "/images/"; 
$image = new image(DB_PREFIX, $db);
$images = $image->get_images($user->logged_in());
foreach ($images AS $v) :
?>
	<a rel="group1" href="<?php echo $image_url . $v['image_path'] . ((DEBUG) ? '?debug=0' :''); ?>"><img src="<?php echo $image_url . $v['image_path'] . ((DEBUG) ? '?debug=0' :''); ?>" /></a>
	<div class="file-gallery-button"><a class="add">+</a> <a class="delete">X</a>
		<img src="<?php echo $image_url . $v['image_path'] . ((DEBUG) ? '?debug=0' :''); ?>" />
	</div>
<?php endforeach; ?>
</div>
<script>
(function () {
	// fancybox
	$("#file-gallery a:not(.add , .delete)").fancybox({ type: 'image'});
})();
</script>
<div>
<?php $usage = $image->usage($user->logged_in()); ?>
Utrymme: <?php echo ceil($usage/(1024*1024)); ?> MB av <?php echo ceil(100*1024*1024/(1024*1024)); ?> MB
</div>
<?php if ($usage < 100*1024*1024) : ?>
<form enctype="multipart/form-data" id="upload-form" action="<?php echo ROOT; ?>upload<?php if (DEBUG) : echo '?debug=0'; endif; ?>" method="post" >

	<input type="file" name="files[]" /><!--multiple="multiple"-->
	
<?php 
form::submit('Ladda upp bild');
form::close();
?>
<span>Laddar upp ...</span>
<script>
(function () {
	
	$('#upload-form').ajaxForm({
		beforeSubmit: function(a,f,o) {
			$('#upload-form').hide().next().text('Laddar upp ...').show();
        },
        success: function(data) {
			data = $.parseJSON(data);
			$.each(data.path, function (i, value) {
				$("<a rel=\"group1\" href=\"<?php echo $image_url; ?>" + value + "<?php if (DEBUG) : echo '?debug=0'; endif; ?>\"><img src=\"<?php echo $image_url; ?>" + value + "<?php if (DEBUG) : echo '?debug=0'; endif; ?>\" /></a><div class=\"file-gallery-button\"><a class=\"add\">+</a><img src=\"<?php echo $image_url; ?>" + value + "<?php if (DEBUG) : echo '?debug=0'; endif; ?>\" /></div>").prependTo("#file-gallery").fancybox({ type: 'image'});
			});
			if (!data.error) {
				$('#upload-form').show().next().html("<span class=\"success\">Filen har laddats upp</span>").delay(5000).hide("slow");
			} else {
				$('#upload-form').show().next().html("<span class=\"error\">" + data.error + "</span>");
			}
			$('#upload-form input:submit').removeAttr('disabled');
        }
    }).next().hide();
	
})();
</script>
<?php endif; // upload ?>
<?php endif; // gallery ?>


<h2>Bilder från internet</h2>
<?php form::text('Sök på Flickr', 'flickr-search', false, 'flickr-search'); ?>
<?php form::text('Sök på Picasa', 'picasa-search', false, 'picasa-search'); ?>
<div id="flickr-gallery">
</div>
<script>
(function () {
	function flickr_gallery(type, tag) {
		$("#flickr-gallery").html("");
		var tag = tag || false;
		var url = "";
		
		if (type=='flickr') {
			url += "http://api.flickr.com/services/feeds/photos_public.gne?tagmode=any&format=json&jsoncallback=?";
			if (tag)
				url += "&tags="+encodeURIComponent(tag.replace(" ", ","));
		} else if (type=='picasa') {
			url += "http://picasaweb.google.com/data/feed/base/" + ((tag)?"all":"featured") + "?access=public&max-results=20&alt=json&prettyprint=true&callback=?";
			if (tag)
				url += "&q="+encodeURIComponent(tag.replace(" ", ","));
		}
		
		
		$.getJSON(url,
			function(data){
				var items = false;
				if (type=='flickr') {
					items = data.items;
				} else if (type=='picasa') {
					items = data.feed.entry;
				}
			
				$.each(items, function(i,item){
					var image_url = false;
					if (type=='flickr') {
						image_url = item.media.m;
					} else if (type=='picasa') {
						image_url = item.content.src;
					}
					$("<a rel=\"group2\" href=\"" + image_url + "\"><img src=\"" + image_url + "\" /></a><div class=\"flickr-gallery-button\"><a class=\"add\">+</a><img src=\"" + image_url + "\" /></div>").prependTo("#flickr-gallery").fancybox({ type: 'image'});
			});
		});
	}
	
	$("#flickr-search").change(function () {
		flickr_gallery('flickr', $(this).val());
	});
	$("#picasa-search").change(function () {
		flickr_gallery('picasa', $(this).val());
	});
	
	flickr_gallery('flickr');
})();
</script>