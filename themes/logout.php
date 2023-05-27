<?php 
/*
 * template: Logout page
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	4 May 2010
 * @version			1.0
 * ----------------------------------------
*/
if (!defined('BASE_DIR')) die('No direct script access allowed');
$title .= " - Utloggad";

$user->logout();
redirect(ROOT, 5); // Redirect after 5 seconds
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
	
		FB.logout();
	};

	(function() {
		var e = document.createElement('script');
		e.src = document.location.protocol + '//connect.facebook.net/sv_SE/all.js';
		e.async = true;
		document.getElementById('fb-root').appendChild(e);
	}());
</script>
<!-- Facebook end -->
<h1>Du har loggat ut!</h1>