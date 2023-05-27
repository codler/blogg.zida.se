<?php
/*
 * template: footer
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	24 april 2010
 * @version			1.0
 * ----------------------------------------
*/
if (!defined('BASE_DIR')) die('No direct script access allowed');
?>
<footer>
<a href="http://www.zencodez.net">Copyright © 2010 Han Lin Yap</a>
</footer>
	</div><!-- wrapper-inner -->
</div><!-- wrapper -->
<?php if (PRODUCTION == false) : ?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-1944741-4");
pageTracker._setDomainName("blogg.zida.se");
pageTracker._trackPageview();
} catch(err) {}</script>
<?php endif; ?>
</body>
</html>