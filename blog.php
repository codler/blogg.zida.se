<?php if (!defined('BASE_DIR')) die('No direct script access allowed');

define('ORIG_ROOT', uri::scheme() . substr(strstr(uri::host(),'.'),1) . '/');

$blog_url = explode(".", uri::host());
$blog_url = $blog_url[0];

$blog_id = $blog->get_id_by('blog_url', $blog_url);
$blog_info = $blog->info($blog_id);

$design = new design(DB_PREFIX, $db);
$use_design = (isset($_GET['d'])&&is_numeric($_GET['d'])) ? $_GET['d'] : false;
if (!$use_design)
	$use_design = $design->get_current_layout($blog_id);
	
// get layout
$data = json_decode($design->get_layout($use_design), true);

// look what page
$path = parse_url(uri::fqdn(), PHP_URL_PATH);

if ($path == "/feed") :
	require_once("rss.php");
	die();
endif;
if (strpos($path, "/images/") === 0) : 
	require_once("image.php");
	die();
endif;
?>
<!--
Theme: Sandbox
Version: 1.0
Date: 2009-07-19
-->
<!DOCTYPE html>
<html lang="sv" xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<meta charset="utf-8" />
<meta name="generator" content="<?php echo ORIG_ROOT; ?>" />
<!-- facebook -->
<meta property="og:site_name" content="Blogg*zida"/>
<title><?php echo $blog_info['blog_name']; ?> @ blogg*zida</title>
<link rel="alternate" href="feed<?php if (DEBUG) : echo '?debug=0'; endif; ?>" type="application/rss+xml" title="RSS" />
<?php 
add_external(ROOT . 'external/jquery.lazyload.js', 'js');

load_external(); 
?>
<?php if ($data) : ?>
<style>
<!--
label {
	color: #aaaaaa;
	display:block;
	font-weight: bold;
	margin: 6px 0 2px 0;
}
label:hover {
	color: #666666;
}

input {
	display: block;
	font-size: 14px;
	height: 25px;
	width: 200px;
}
input:focus {
	background-color: #f0ffff;
}

textarea {
	display: block;
	font-size: 14px;
	height: 200px;
	width: 98%;
}

#wrapper {
	background: url(<?php echo $data['global_settings']['outer_background'];?>) top center;
}
#wrapper-inner {
	background: url(<?php echo $data['global_settings']['inner_background'];?>) top center;
	height: <?php echo $data['global_settings']['worksheet_height'];?>px;
	width: <?php echo $data['global_settings']['worksheet_width'];?>px;
}

div[layer_id] {
	position: absolute;
}
<?php foreach ($data AS $k => $v) :
if (!is_numeric($k)) continue;
			
$css = array();
foreach($v['css'] AS $attr => $value) {
	if ($attr == 'backgroundColor') {
		$attr = 'background-color';
	}
	$css[] = $attr . ":" . $value;
}

$layer_id = (isset($v['layer_id'])) ? "layer_id=\"".$v['layer_id']."\"" : "";
?>
div[layer_id='<?php echo $v['layer_id']; ?>'] {
	<?php echo implode(';',$css); ?>
}
<?php endforeach; ?>
-->
</style>

<!-- Assets - Javascript -->
<script type="text/javascript">
<!--
function getSelected(){
	var t = '';
	if(window.getSelection){
		t = window.getSelection();
	}else if(document.getSelection){
		t = document.getSelection();
	}else if(document.selection){
		t = document.selection.createRange().text;
	}
	return t;
}

// clipboard
function copy() {
	// Only IE
	if(window.clipboardData) {
		var text = getSelected();
		if (text.length > 50) {
			window.clipboardData.setData('text',text + "\r\n\r\nLäs mer: " + window.location.href);
			return false;
		}
	} 
	return true;
}


$(document).ready(function () {
	<?php foreach ($data AS $k => $v) :
		// Events
		if (isset($v['user_events'])) { 
			foreach ($v['user_events'] AS $event) : ?>
$("div[layer_id='<?php echo $v['layer_id']; ?>']").live('<?php echo $event['event']; ?>', function () {
	$("div[layer_id='<?php echo $event['layer']; ?>']").<?php echo $event['effect']; ?>(<?php echo $event['duration']; ?>);
});
		<?php endforeach;
		}
		// Grid
		if (isset($v['grid'])) { 
			foreach ($v['grid'] AS $grid) : ?>
var toGrid<?php echo $grid; ?> = $("div[layer_id='<?php echo $grid; ?>']");
toGrid<?php echo $grid; ?>.css({ position : 'static' });
$("div[layer_id='<?php echo $v['layer_id']; ?>']").append(toGrid<?php echo $grid; ?>);
		<?php endforeach;
		}
	endforeach; ?>
	
	// validate
	$("#blog-comment input[text!=submit][text!=submit][name!=url], #blog-comment textarea").addClass("required");
	$("#blog-comment").validate({
		rules : {
			email : {
				//required: true,
				email: true,
				maxlength : 50
			},
			url : {
				maxlength : 4000
			},
			name : {
				maxlength : 40
			},
			message : {
				minlength : 3
			}
		}
	});
	
	// lazy load
	$("img").lazyload({ threshold : 200 });
});
-->
</script>
<!--[if IE]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<?php endif; ?>
</head>
<body id="layout-one-column">
<!-- Facebook start -->
<div id="fb-root"></div>
<script>
	window.fbAsyncInit = function() {
		FB.init({
			appId  : '<?php echo FACEBOOK_APP_ID; ?>',
			status : true, // check login status
			cookie : true, // enable cookies to allow the server to access the session
			xfbml  : true  // parse XFBML
		});
		
		FB.getLoginStatus(function(response) {
			if (response.session) {
				// logged in and connected user, someone you know
				if ($("#blog-comment input[name=name]").val()=='') {
					FB.api('/me', function(response) {
						$("#blog-comment input[name=name]").val(response.name);
					});
				}
			} else {
				// no user session available, someone you dont know
			}
		});

	};

	(function() {
		var e = document.createElement('script');
		e.src = document.location.protocol + '//connect.facebook.net/sv_SE/all.js';
		e.async = true;
		document.getElementById('fb-root').appendChild(e);
	}());
</script>
<!-- Facebook end -->
<a name="top"></a>
<nav id="zida_topbar">
	<ul>
		<li><a href="http://blogg.zida.se/">Blogg*zida</a></li>
		<?php if ($user->logged_in()) : 
		$info = $user->info($user->logged_in());
		?>
		<li>Inloggad som <a href="<?php echo ORIG_ROOT; ?>account"><?php echo $info['username'];?></a></li>
		<li><a href="<?php echo ORIG_ROOT; ?>logout">Logga ut</a></li>		
		<?php endif; ?>
	</ul>
</nav>
<div id="wrapper">
	<div id="wrapper-inner">
	<?php if ($data) : ?>
		<?php foreach ($data AS $k => $v) :
			if (!is_numeric($k)) continue; 
			
			$css = array();
			foreach($v['css'] AS $attr => $value) {
				if ($attr == 'backgroundColor') {
					$attr = 'background-color';
				}
				$css[] = $attr . ":" . $value;
			}
			
			$layer_id = (isset($v['layer_id'])) ? "layer_id=\"".$v['layer_id']."\"" : "";
			?>
			<!--[if IE]><div oncopy="return copy();" style="position: absolute; <?php echo implode(';',$css); ?>" <?php echo $layer_id; ?>><![endif]-->
<![if !IE]><div <?php echo $layer_id; ?>><![endif]>


		
		<?php if ($v['component_type'] == 'blog_posts') { ?>
		<section>
		<?php 
		if (strpos($path,'/post/')===0) {
			// single post
			$post_id = get_url("post");
			$list = array($blog->get_post(false, $post_id));
		} else {
			// all posts
			if (strpos($path, "/page/") === 0) : 
			$pagination = get_url("page");
			endif;
			$pagination_limit = 10;
			$pagination = (is_numeric($pagination)) ? abs($pagination)*$pagination_limit : 0;
			$list = $blog->get_post_list($blog_id, $pagination_limit, $pagination);
		}
		zcTplFor(); ?>
			<article>
				<header><h1><?php if (!$post_id) : ?><a href="<?php echo ROOT; ?>post/[key=post_id]/[key=date_year]/[key=date_month]/[key=date_day]/[key=post_url]" alt="[key=post_headline]"><?php endif; ?>[key=post_headline]<?php if (!$post_id) : ?></a><?php endif; ?></h1></header>
				<section>[key=post_date]<br />[key=post_content]</section>
				<fb:like href="<?php echo uri::scheme() . uri::host(); ?>/post/[key=post_id]/[key=date_year]/[key=date_month]/[key=date_day]/[key=post_url]"></fb:like>
			</article>
		<?php zcTplForEnd($list); 
		echo getFlash('blog/comment');
		if ($post_id) {
			
			$comments = $blog->get_comments($post_id);
			
			foreach ($comments AS $comment) : ?>
			<img width="40" height="40" src="http://www.gravatar.com/avatar/<?php echo md5(strtolower($comment['comment_email'])); ?>?size=40&d=<?php echo ORIG_ROOT; ?>assets/images/logo.png" />
			<?php 
			if ($comment['comment_url']!='') :
				if (strpos($comment['comment_url'], 'http://')===0 ||
					strpos($comment['comment_url'], 'https://')===0) :
					$url = $comment['comment_url'];
				else :
					$url = 'http://' . $comment['comment_url'];
				endif;
			endif; ?>
			<b><?php echo ($comment['comment_url']!='') ? '<a href="'.$url.'">'.$comment['comment_name'].'</a>': $comment['comment_name']; ?></b>
			<p><?php $message = $comment['comment_message'];
			// Fix newline
			$message = str_replace(array("\r\n","\n","\r"), ' <br />', $message);
			$message = wordwrap($message, 120, ' <br />');
			// auto add link
			$message = preg_replace('/http[s]?:\/\/[^\s]*/','<a href="\0" rel="nofollow">\0</a>',$message);
			// bold text
			$message = preg_replace('/\[b\](.*?)\[\/b\]/','<b>${1}</b>',$message);
			// cursive text
			$message = preg_replace('/\[i\](.*?)\[\/i\]/','<i>${1}</i>',$message);
			echo $message;
			?></p>
			<?php endforeach;
			form::open('blog-comment',ORIG_ROOT . 'submit', 'post', 'blog-comment');
			form::hidden('id',$post_id);
			form::message('Kommentar*', 'message');
			if ($user->logged_in()) :
				$info = $user->info($user->logged_in());
				form::email('E-post*', 'email', $info['email'], false, true, true);
				$oauth = new sso(DB_PREFIX, $db);
				if ($oauth->get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET)) :
					form::text('Namn*', 'name', false, false, true);
				else :
					form::text('Namn*', 'name', $info['username'], false, true, true);
				endif;
			else :
				form::email('E-post*', 'email', false, false, true);
				form::text('Namn*', 'name', false, false, true);
			endif;
			form::url('Url', 'url', false, false, true);
			form::captcha("Vad blir ","?* (för validering)");
			form::submit('Kommentera');
			form::close();
		} else {
			// pagination
			$total_post = $blog->count_post($blog_id);
			if ($total_post > $pagination_limit) : 
				$total_page = ceil($total_post/$pagination_limit);
				for ($i = 0; $i < $total_page; $i++) : 
					if ($pagination/$pagination_limit == $i) : ?>
					<?php echo ($i+1); ?>
					<?php else : ?>
				<a href="<?php echo ROOT; ?>page/<?php echo $i; ?>"><?php echo ($i+1); ?></a>
			<?php	endif; 
				endfor; 
			endif;
		}
		?>
		</section>
		<?php 
		} elseif ($v['component_type'] == 'text') {
			echo nl2br($v['text']);
		} elseif($v['component_type'] == 'image') {
			echo "<img src=" . $v['image'] . " width=\"100%\" height=\"100%\" />";
		} elseif($v['component_type'] == 'list') {
			echo "<ul>";
			foreach ($v['list'] AS $item) :
				echo "<li><a href='".$item['url']."'>".$item['name']."</a></li>";
			endforeach;
			echo "</ul>";
		} elseif($v['component_type'] == 'grid') {
		} else {
			echo $v['layer_name'];
		}
		?>
		</div>	
		<?php endforeach; ?>
		<?php //echo print_r($data); ?>
	<?php endif; ?>
	</div><!-- end wrapper-inner -->
</div><!-- end wrapper -->

</body>
</html>
