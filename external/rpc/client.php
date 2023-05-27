<?php 
// 2009-02-26
require_once('clientXMLRPC.inc.php');
	$appname = "bloggzida";
	$client = new IXR_Client(base64_decode('aHR0cDovL3JlbW90ZS56ZW5jb2Rlei5uZXQvc2VydmVyLnBocA=='));

	if ($client->query('activation.checkapp', $appname)) 
	{
		if($client->getResponse() )
		{
			if ($client->query('activation.checkapp.message', $appname)) 
			{
				if($client->getResponse() )
				{
				die($client->getResponse());
				}
			}
		}
	}

?>