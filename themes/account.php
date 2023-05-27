<?php 
/*
 * template: Account page
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	5 May 2010
 * @version			1.0
 * ----------------------------------------
*/
if (!defined('BASE_DIR')) die('No direct script access allowed');
$title .= " - Konto inställningar";

if (!$user->logged_in()) {
	return require_once(get_page('/login'));
}
$info = $user->info($user->logged_in()); 

$oauth = new sso(DB_PREFIX, $db);
if ($oauth->get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET)) : 
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
				
		FB.getLoginStatus(function(response) {
			if (response.session) {
				// logged in and connected user, someone you know
				FB.api('/me', function(response) {
					$("#fb-logout").after(' (' + response.name + ')');
				});
			} else {
				// no user session available, someone you dont know
				$("#facebook-invite-toggle").hide();
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
<?php endif; ?>
<?php echo getFlash('account'); ?>
<h1>Konto inställningar</h1>
<div class="box">
<h2>Byt lösenord</h2>
<?php 
form::open('change-password',ROOT . 'submit', 'post', 'change-password');
form::password('Gamla lösenord','old-password');
form::password('Nya lösenord','new-password');
form::password('Bekräfta nya lösenord','new2-password');
form::submit('Ändra lösenord');
form::close();
?>
</div>
<div class="box">
<h2>Byt smeknamn</h2>
<?php 
form::open('change-name',ROOT . 'submit', 'post', 'change-name');
form::text('Smeknamn','name', $info['username']);
form::submit('Ändra smeknamn');
form::close();
?>
<p>OBS! Länkar på dina bilder kommer också att ändras!</p>
</div>
<div class="box">
<h2>Byt e-post</h2>
<?php 
form::open('change-email',ROOT . 'submit', 'post', 'change-email');
form::text('E-post','email', $info['email']);
form::submit('Ändra epost');
form::close();
?>
</div>
<?php 

/* // facebook invite
if ($oauth->get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET)) : ?>
<h2>Bjud in dina vänner</h2>
<a id="facebook-invite-toggle">via Facebook</a>
<span>
<fb:serverFbml>
<script type="text/fbml">
<fb:fbml>
    <fb:request-form
        method='POST'
        type='bli medlem'
		action='<?php echo uri::fqdn();?>'
        content='Vill du bli medlem på Blogg*zida? 
            <fb:req-choice url="<?php echo uri::scheme() . uri::host(); ?>/?r=<?php echo $info['username']; ?>" 
                label="Yes" />'
        <fb:multi-friend-selector 
            actiontext="Bjud in dina vänner till Blogg*zida.">
    </fb:request-form>
</fb:fbml>
</script>
</fb:serverFbml>
</span>
<?php endif; */ ?>

<?php // Connections 
if ($oauth->facebook_connected($user->logged_in())) : ?>
<div class="box">
<h2>Kopplingar</h2>
<p>Facebook <a href="<?php echo ROOT; ?>disconnect/facebook" id="fb-logout">Koppla bort</a></p>
</div>
<?php endif; ?>