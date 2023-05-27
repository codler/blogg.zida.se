<?php
/*
 * This is url class
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	22nd Mars 2010
 * @version			1.1
 * ----------------------------------------
*/
?>
<?php
class url
{
	var $url;
	function __construct($rawUrl) 
	{
		$this->url = trim($rawUrl);
		$this->url = substr($this->url,0,254);
		$this->url = str_replace('å','a',$this->url);
		$this->url = str_replace('ä','a',$this->url);
		$this->url = str_replace('ö','o',$this->url);
	}
	function blog() 
	{
		return preg_replace("/[^a-z0-9]/",'',strtolower($this->url));
	}
	
	function file() 
	{
		return $this->post();
	}
	
	function post() 
	{	
		return preg_replace("/[^a-z0-9-]/", "-", strtolower($this->url));
	}
	
	function getUrl() 
	{
		return $this->url;
	}
	function setUrl($url) 
	{
		$this->url = $url;
	}
}
?>