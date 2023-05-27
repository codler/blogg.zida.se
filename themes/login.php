<?php 
/*
 * template: Login page
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	4 May 2010
 * @version			1.0
 * ----------------------------------------
*/
if (!defined('BASE_DIR')) die('No direct script access allowed');

add_external(ROOT . 'external/easyTooltip.js', 'js');

$title .= " - Logga in"; 

$oauth = new sso(DB_PREFIX, $db);
if ($user_id = $oauth->facebook_login(FACEBOOK_APP_ID, FACEBOOK_SECRET)) {
	$user->force_login($user_id);
	setFlash('global', addClass('Inloggning via Facebook lyckades!', 'success'));
	return require_once(get_page('/main-loggedin'));
}

if ($oauth->get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET)) : 
setFlash('facebook/add','true');
endif;
?>
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
		
		$("#fb-logout").click(function () {
			FB.logout();
		});

		FB.Event.subscribe('auth.sessionChange', function(response) {
			if (response.session) {
				// A user has logged in, and a new cookie has been saved
				window.location.reload();
			} else {
				// The user has logged out, and the cookie has been cleared
				window.location.reload();
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
<?php if (!$oauth->get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET)) : ?>
<div class="notice"><b>Om Blogg*zida</b> - skapa en egen design och få den till din blogg utan att kunna något om kodning. Detta kan du genom designverktyget som jag har utvecklat!</div>
<?php 
else :
echo addClass("Du har loggat in via Facebook första gången. Var god och koppla till en befintlig konto genom att fylla i formulären nedan.",'success'); ?>
<a id="fb-logout">Logga ut från Facebook.</a>
<?php endif; ?>
<br/>
<div class="box box-col-2">
	<h1>Logga in</h1>
	<?php echo getFlash('user/login'); ?>
	<?php 
	form::open('login',ROOT . 'submit');
	form::text('E-post','name', false, false, 'name');
	form::password('Lösenord','password');
	form::submit('Logga in');
	form::close();
	?>
	<?php if (!$oauth->get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET)) : ?>
	<fb:login-button show-faces="true" perms="photo_upload,user_photos,email,publish_stream"></fb:login-button>
	<?php endif; ?>
	<a id="forgot-toggle">Glömt lösenord?</a>
	<span>
	<h1>Glömt lösenord</h1>
	<?php echo getFlash('user/forgot'); ?>
	<?php
	form::open('forgot',ROOT . 'submit');
	form::text('E-post','name', false, false, 'name');
	form::submit('Skicka nytt lösenord');
	form::close();
	?>
	</span>
</div>
<div class="box box-col-2">
	<h1>Registrera</h1>
	<?php echo getFlash('user/register'); ?>
	<?php 
	form::open('register',ROOT . 'submit', 'post','register');
	if (isset($_GET['r'])&&validate::username($_GET['r'])) 		
		form::hidden('recruit-by', $_GET['r']);

	form::email('E-post','email', false, 'register-email');
	form::password('Lösenord','password');
	form::text('Smeknamn','name', false, 'register-name');
	form::submit('Registrera');
	form::close();
	?>
</div>
<script>
(function () {
	// tooltips
	$('#register-name').easyTooltip({ content: 'Minst 4 tecken och endast a-z0-9 är tillåten.', clickRemove: true });

})();
</script>
<!--[if lt IE 8]>
<div id="browser_msie">
<p>Du använder Internet Explorer<br />
Rekommenderar att du använder någon av följande<br />
<a href="http://www.mozilla.com?from=sfx&amp;uid=188254&amp;t=561"><img border="0" width="128" height="128" src="/yap-goodies/images/Firefox.png" title="Firefox" /></a>
<a href="http://www.google.com/chrome"><img border="0" width="128" height="128" src="/yap-goodies/images/Chrome.png" title="Chrome" /></a>
</p>
</div>
<![endif]-->
<!--<a href="http://www.opera.com/"><img border="0" width="128" height="128" src="/yap-goodies/images/Opera.png" title="Opera" /></a>
<a href="http://www.apple.com/safari/"><img border="0" width="128" height="128" src="/yap-goodies/images/Safari.png" title="Safari" /></a>-->

<div class="box box-col-2">
	<h1>Unikt med Blogg*zida</h1>
	<ul>
		<li>Blogga tillsammans</li>
		<li>Kraftfull designverktyg</li>
		<li>Användaren är prio!</li>
	</ul>
</div>
<div class="box box-col-2">
	<h2><a href="<?php echo ROOT; ?>design/preview<?php if (DEBUG) : echo '?debug=0'; endif; ?>">Testa designverktyget</a></h2>
</div>