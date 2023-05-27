<?php
/*
 * template: header
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	4 may 2010
 * @version			1.0
 * ----------------------------------------
*/
if (!defined('BASE_DIR')) die('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="sv" xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<meta charset="utf-8" />
<title><?php echo $title; ?></title>
<?php 
add_external(ROOT . 'external/jquery.lazyload.js', 'js');
load_external(); 
?>
<style>
/* === Tags === */
body {
	<?php echo css_gradient('#e0f8ff','#ffffff'); ?>
}

footer {
	margin-top: 100px;
}

/* == links == */
a {
	color: #003b9f;
	text-decoration: none;
}
a:visited {
	color: #003b5f;
}
a:hover {
	text-decoration: underline;
}

/* a[href^="http://"] {
	background: transparent url(<?php echo ROOT; ?>assets/images/ex.gif) repeat-y scroll right 0;
	padding-right: 13px;
}
a[href^="http://"]:hover {
	background-position:right 200px;
} */

/* == table == */
table {
	width: 100%;
}
td {
	border: 1px solid #aaaaaa;
	padding: 2px;
}

/* == form == */
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
input[type='submit'] {
	border: 1px solid #ffffe0;
	background-color: #e0f8ff;
	height: 30px;
	margin: 10px 0;
	<?php echo css_gradient('#e0f8ff','#ffffe0'); ?>
	<?php echo css_radius(5); ?>
	-o-box-shadow: 0px 0px 10px #B4B4B4;
	-moz-box-shadow: 0px 0px 10px #B4B4B4;
	-webkit-box-shadow: 0px 0px 10px #B4B4B4;
	box-shadow: 0px 0px 10px #B4B4B4; /* CSS3 */
}


/* === Ids === */
#wrapper-inner {
	background: url("<?php echo ROOT; ?>assets/images/footer_hill2.png") repeat-x 50% 150%;
	margin: 5px auto;
	width: 80%;
	min-height: 300px;
	max-width: 1000px;
}


/* === Classes === */
.time {
	font-size: 10px;
}

span.success , span.error , .notice {
	border: 1px solid #4D4D4D;
	display:block;
	font-size: 14px;
	margin: 0 auto;
	padding: 10px;
	text-align:center;
	max-width: 500px;
	<?php echo css_radius(10); ?>
	-o-box-shadow: 0px 0px 10px #B4B4B4;
	-moz-box-shadow: 0px 0px 10px #B4B4B4;
	-webkit-box-shadow: 0px 0px 10px #B4B4B4;
	box-shadow: 0px 0px 10px #B4B4B4; /* CSS3 */
}
.success {
	background-color:#CFF9A6;
	font-weight: bold;
}
.error {
	background-color:#FFD5D5;
	font-weight: bold;
}
.notice {
	background-color: lightyellow;
	font-style: italic;
}

.box {
	border: 1px solid #DDD; 
	display: inline-block;
	padding: 1%;
	vertical-align: top;
	
	/* CSS3 */
	background-image: -webkit-gradient(
		linear,
		left bottom,
		left top,
		color-stop(0.08, rgb(237,237,237)),
		color-stop(0.25, rgb(245,245,245)),
		color-stop(0.46, rgb(255,255,255))
	);
	background-image: -moz-linear-gradient(
		center bottom,
		rgb(237,237,237) 8%,
		rgb(245,245,245) 25%,
		rgb(255,255,255) 46%
	);
	
	filter:  progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr='#ffffff', endColorstr='#ededed'); /* IE6 & IE7 */
	-ms-filter: "progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr='#ffffff', endColorstr='#ededed')"; /* IE8 */
	
	<?php echo css_radius(5); ?>
}
.box-col-2 {
	width: 47%;
}

/* === define === */
header nav, footer {
	border: 3px solid #aaa;
	background-color: #4D4D4D;
	height: 30px;
	<?php echo css_gradient('#4D4D4D','#111111'); ?>
	<?php echo css_radius(5); ?>
}
header nav a, footer a {
	color: #f8f8f8;
	display: block;
	float:left;
	font-weight: bold;
	padding: 7px 5% 3px;
}
header nav a:visited, footer a:visited {
	color: #f8f8f8;
}
header nav a:hover, footer a:hover {
	<?php echo css_gradient('#4D4D4D','#666666'); ?>
}

/* = design page = */
.editable:hover:after {
	content: " » ändra";
}
</style>
<!--[if lt IE 8]>
<style>
.box {
	display: inline;
}
</style>
<![endif]-->
<script>
$().ready(function () {
	$("#register").addClass("required");
	$("#add-blog").addClass("required");
	$("#write-blog").addClass("required");
	$("#upload-form").addClass("required");
	$("#recovery-password").addClass("required");
	$("#change-password").addClass("required");
	$("#change-name").addClass("required");
	$("#change-email").addClass("required");
	$("#add-design").addClass("required");
	// validate
	$("#register").validate({
		rules : {
			email : {
				//required: true,
				email: true,
				maxlength : 50,
				remote : {
					url: "<?php echo ROOT; ?>fetch<?php if (DEBUG) : echo '?debug=0'; endif; ?>",
					type: "post",
					data: {
						type: 'exist-email',
						email: function() {
							return $("#register input[name=email]").val();
						}
					}
				}
			},
			password : {
				minlength : 6
			},
			name : {
				minlength : 4,
				maxlength : 40,
				remote : {
					url: "<?php echo ROOT; ?>fetch<?php if (DEBUG) : echo '?debug=0'; endif; ?>",
					type: "post",
					data: {
						type: 'exist-name',
						name: function() {
							return $("#register input[name=name]").val();
						}
					}
				}
			}
		},
		messages: {
			email: {
				remote: jQuery.validator.format("{0} är upptagen.")
			},
			name: {
				remote: jQuery.validator.format("{0} är upptagen.")	
			}
		}

	});
	
	// main
	$("#add-blog").validate({
		rules : {
			name : {
				minlength : 4,
				maxlength : 100
			},
			url : {
				minlength : 6,
				maxlength : 100,
				remote : {
					url: "<?php echo ROOT; ?>fetch<?php if (DEBUG) : echo '?debug=0'; endif; ?>",
					type: "post",
					data: {
						type: 'exist-url',
						url: function() {
							return $("#add-blog input[name=url]").val();
						}
					}
				}
			}
		},
		messages: {
			url: {
				remote: jQuery.validator.format("{0} är upptagen.")
			}
		}
	});
	
	// write-blog
	$("#write-blog").validate({
		rules : {
			headline : {
				minlength : 3,
				maxlength : 255
			},
			content : {
				minlength : 6
			}
		}
	});
		
	// account
	$("#recovery-password").validate({
		rules : {
			'new-password' : {
				minlength : 6
			},
			'new2-password' : {
				equalTo : "#recovery-password input[name='new-password']"
			}			
		}
	});
	$("#change-password").validate({
		rules : {
			'old-password' : {
				minlength : 6
			},
			'new-password' : {
				minlength : 6
			},
			'new2-password' : {
				equalTo : "#change-password input[name='new-password']"
			}			
		}
	});
	$("#change-name").validate({
		rules : {
			name : {
				minlength : 4,
				maxlength : 40,
				remote : {
					url: "<?php echo ROOT; ?>fetch<?php if (DEBUG) : echo '?debug=0'; endif; ?>",
					type: "post",
					data: {
						type: 'exist-name',
						name: function() {
							return $("#change-name input[name=name]").val();
						}
					}
				}
			}		
		},
		messages: {
			name: {
				remote: jQuery.validator.format("{0} är upptagen.")	
			}
		}
	});
	$("#change-email").validate({
		rules : {
			email : {
				//required: true,
				email: true,
				maxlength : 50,
				remote : {
					url: "<?php echo ROOT; ?>fetch<?php if (DEBUG) : echo '?debug=0'; endif; ?>",
					type: "post",
					data: {
						type: 'exist-email',
						email: function() {
							return $("#change-email input[name=email]").val();
						}
					}
				}
			}	
		},
		messages: {
			email: {
				remote: jQuery.validator.format("{0} är upptagen.")	
			}
		}
	});
	// design
	$("#add-design").validate({
		rules : {
			name : {
				minlength : 4,
				maxlength : 100
			}	
		}
	});
	
	// settings
	$("#add-share-blog").validate({
		rules : {
			name : {
				minlength : 4,
				maxlength : 40,
				remote : {
					url: "<?php echo ROOT; ?>fetch<?php if (DEBUG) : echo '?debug=0'; endif; ?>",
					type: "post",
					data: {
						type: 'not-exist-name',
						name: function() {
							return $("#add-share-blog input[name=name]").val();
						}
					}
				}
			}
		},
		messages: {
			name: {
				remote: jQuery.validator.format("Användaren {0} existerar inte.")	
			}
		}
	});
	
	$("#delete-blog").submit(function () {
		return confirm("Är du säker på att ta bort bloggen? Det går ej att ångra!");
	});
	
	// Fancy Url
	$('#add-blog input[name=name]').keyup(fancyUrlcustom);
	$('#add-blog input[name=name]').change(fancyUrlcustom);
	function fancyUrlcustom() {
		var s = $(this).val();
		s = trimUrl(s);
		$('#add-blog input[name=url]').val(s);
	}
	
	$('#add-blog input[name=url]').keypress(fancyUrl);
	$('#add-blog input[name=url]').keyup(fancyUrl);
	$('#add-blog input[name=url]').change(fancyUrl);
	function fancyUrl() {
		var s = $(this).val();
		s = trimUrl(s);
		$(this).val(s);
	}
	
	function trimUrl(url) {
		url = url.replace(/å/gi,'a');
		url = url.replace(/ä/gi,'a');
		url = url.replace(/ö/gi,'o');
		url = url.replace(/[^a-zA-Z0-9]/g,'');
		return url;
	}
	
	// forgot toggle / facebook toggle
	$("#forgot-toggle, #facebook-invite-toggle, #add-blog-toggle").click(function () {
		$(this).hide("fast").next().show("fast");
		return false;
	}).next().hide();
	
	// register "toggle"
	$("#register-name").hide().prev().hide();
	$("#register-email").focus(function() {
		$("#register-name").show().prev().show();
	});
		
	// gallery
	$('#file-gallery a:not(.add , .delete), #flickr-gallery a:not(.add , .delete)').each(function () {
		var img = $(this).find('img');
		if (this.getElementsByTagName('img')[0].naturalWidth > 540) {
			img.attr('width', 540);
		}
	});
	$('#file-gallery a:not(.add , .delete), #file-gallery div, #flickr-gallery a:not(.add , .delete), #flickr-gallery div').live('mouseover',function (event) {
	if (!$(event.relatedTarget).is('div.file-gallery-button'))
		if ($(this).next().is('div')) {
			var position = $(this).offset();
			var positionGallery = $(this).parent().offset();
			var top = position.top - positionGallery.top + 3 + $(this).parent().scrollTop();
			var left = position.left - positionGallery.left + 3 + $(this).parent().scrollLeft();
			$(this).next().css({'top':top,'left':left}).show();
		}
	});
	$('#file-gallery a:not(.add , .delete), #flickr-gallery a:not(.add , .delete)').live('mouseout',function (event) {
		if (!$(event.relatedTarget).is('div.file-gallery-button')&&!$(event.relatedTarget).is('div.flickr-gallery-button'))
			if ($(this).next().is('div'))
				$(this).next().hide();
	});
	$('#file-gallery div, #flickr-gallery div').live('mouseover',function () {
	});
	
	$('#file-gallery div, #flickr-gallery div').live('mousedown',function (event) {
		event.stopImmediatePropagation();
		if ($(this).prev().is('a')&&$(event.target).hasClass('add'))
			editor.insertHtml($(this).prev().html());
			
		if ($(this).prev().is('a')&&$(event.target).hasClass('delete')&&$(this).parent().attr('id')=='file-gallery') {
			var choice = confirm("Vill du ta bort bilden?");
			if (choice) {
				var selected = $(this);
				$.post("<?php echo ROOT; ?>fetch<?php if (DEBUG) : echo '?debug=0'; endif; ?>", 
					{ type: "delete-image", url: $(this).prev()[0].pathname.substring('/images/'.length) },
					function(data){
						selected.prev().remove();
						selected.remove();
					}
				);
			}
		} else if ($(this).parent().attr('id')=='flickr-gallery') {
			$(this).prev().remove();
			$(this).remove();
		}
	});
	
	
	$("form").submit(function () {
		if ($(this).validate().form())
			$("input:submit", $(this)).attr("disabled", "disabled");
	});
	
	// lazy load
	$("img").lazyload({ threshold : 200 });

});
</script>
<!--[if IE]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body id="layout-one-column">
<a name="top"></a>
<nav id="zida_topbar">
	<ul>
		<li>© 2010 <a href="http://www.zencodez.net/">Han Lin Yap</a></li>
		<?php if ($user->logged_in()) : 
		$info = $user->info($user->logged_in());
		?>
		<li>Inloggad som <a href="<?php echo ROOT; ?>account"><?php echo $info['username'];?></a></li>
		<li><a href="<?php echo ROOT; ?>logout">Logga ut</a></li>		
		<?php endif; ?>
	</ul>
</nav>
<nav id="zida_quickbar"></nav>
<div id="wrapper">
	<div id="wrapper-inner">
<header>
<a href="<?php echo ROOT; ?>"><img src="<?php echo ROOT; ?>assets/images/logo.png" /></a>
<?php if ($user->logged_in()) : 
ob_start("date_sv");
?>
<span class="time">
<?php echo strftime("%A den %e %B, kl %H:%M, vecka %W"); ?>
</span>
<?php ob_end_flush();
endif; ?>
<nav>
<?php if ($user->logged_in()) : ?>
<a href="<?php echo ROOT; ?>">Bloggar</a>
<a href="<?php echo ROOT; ?>account">Konto</a>
<a href="<?php echo ROOT; ?>logout">Logga ut</a>
<?php else : ?>
<a href="<?php echo ROOT; ?>news">Nyheter</a>
<a href="http://metroroll.zencodez.net/v3/Kontakta.yap">Kontakta mig</a>
<?php endif; ?>
</nav>

<br />
<?php echo getFlash('global'); ?>
</header>