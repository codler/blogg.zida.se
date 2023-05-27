<?php 
/**
 * Image browse - custom for ckeditor
 * 
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			13th May 2010
 * @last-modified	13th May 2010
 * @version			1.0
 * @package 		theme
 */
/* ----------------------------------------
 * Change log:
v1.0 - 13th May 2010
Created
 */

if (!defined('BASE_DIR')) die('No direct script access allowed');
$title .= " - Bild galleri från Facebook";

$oauth = new sso(DB_PREFIX, $db);
if ($oauth->get_facebook_cookie(FACEBOOK_APP_ID, FACEBOOK_SECRET)) : ?>
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
		
		FB.api(/* { method: 'photos.getAlbums'	} */ '/me/albums', 
		function(response) {
			$.each(response.data, function (key, value) {
				if (value.privacy == "everyone") {
					FB.api( '/' + value.id + '/photos',
					function (response) {
						$.each(response.data, function (key, value) {
							$("#gallery").append("<img height='100px' bigsrc='" + value.source + "' src='" + value.picture + "' />.");
							value.source
						});
					});
				}
			});
			
/* 			$.each(response, function (key, value) {
				//if (value.name == "Fan Check Photos") {
					if (value.can_upload == true) {
						var aid = value.aid;
						//console.log(value.aid);
						FB.api({ method: 'photos.get', aid: aid }, 
						function(response) {
							$.each(response, function (key, value) {
								$("#gallery").append("<img height='100px' bigsrc='" + value.src_big + "' src='" + value.src_small + "' />.");
								//console.log(value.src_big);
							});
						});
					}
				//}
			}) */
		});
		
		// ckeditor
		// Helper function to get parameters from the query string.
		function getUrlParam(paramName)
		{
			var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
			var match = window.location.search.match(reParam) ;
		 
			return (match && match.length > 1) ? match[1] : '' ;
		}
		$("#gallery img").live('click', function () {
			var funcNum = getUrlParam('CKEditorFuncNum');
			var fileUrl = $(this).attr('bigsrc');
			window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
			window.close();
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
<div id="gallery"></div>