<?php
/*
 * Function file
 *
 * @author			Han Lin Yap
 * @website			http://www.zencodez.net/
 * @created			23rd April 2010
 * @last-modified	23rd April 2010
 * @version			1.0
 * ----------------------------------------
 * Change log:
v1.0 - 23th April 2010

 */

// part(int) start from 0
function get_url($skip, $part=0) {
	$path = parse_url(uri::fqdn(), PHP_URL_PATH);
	$parts = substr($path, strlen("/".$skip."/"));
	$parts = explode("/", $parts);
	return (is_array($parts)&&count($parts)>$part) ?  $parts[$part] : false;	
}

function happy_date($date) {
	$time = strtotime($date);
	$ago = time() - $time;
	
	// less then 5 seconds
	if ($ago < 5) {
		return "nu";
	}
	// less then 1 minute
	if ($ago < 60) {
		return $ago . " sekunder sedan";
	}
	// less then 1 hour
	if ($ago < 60*60) {
		return ceil($ago / 60) . " minuter sedan";
	}
	// less then 1 day
	if ($ago < 24*60*60) {
		return ceil($ago / 60 / 60) . " timmar sedan";
	}
	// less then 1 week
	if ($ago < 7*24*60*60) {
		return ceil($ago / 24 / 60 / 60) . " dagar sedan";
	}
	
	return date('Y-m-d',$time);
}

// PATH to file
function get_page($path) {
	global $pages;
	
	// Remove slash (/) in begining
	$path = substr($path, 1);
	
	// Default file
	if ($path == "")
		return $pages['default'];
		
	$paths = explode('/',$path);
	
	// Find path in $pages
	while(sizeof($paths) > 0) {
		if (array_key_exists(implode('/', $paths), $pages))	{
			$page = $pages[implode('/', $paths)];
			if (substr($page, 0, 1)!= "*") {
				if (implode('/', $paths) != $path)
					break;
			} else {
				$page = substr($page, 1);
			}
					
			return $page;
		}
		array_pop($paths);
	}
	
	// No file found
	header("HTTP/1.1 404 Not Found");
	die();	
}

// add media files like css or js
function add_external($url,$type) {
	global $externalResource;
	
	if (file_exists(BASE_DIR . $url)) {
		$url .= '?' . filemtime(BASE_DIR . $url);
	}
	
	switch ($type) {
		case 'css':
			$externalResource[$type][] = '<link rel="stylesheet" type="text/css" href="'.$url.'" />';
			break;
		case 'js':
			$externalResource[$type][] = '<script src="'.$url.'"></script>';
	}
}

function load_external($type='all') {
	global $externalResource;
	$htmlExternal = '';
	if ($type=='all') {
		if (array_key_exists('css',$externalResource)) {
			// css first
			$htmlExternal .= "\n\t<!-- Assets - CSS -->\n\t";
			$htmlExternal .= implode("\n\t",$externalResource['css']);
			$htmlExternal .= "\n\t";
		}
		if (array_key_exists('js',$externalResource)) {
			// js second
			$htmlExternal .= "\n\t<!-- Assets - Javascript -->\n\t";
			$htmlExternal .= implode("\n\t",$externalResource['js']);
			$htmlExternal .= "\n\t";
		}
		// remove from array
		unset($externalResource['js']);
		unset($externalResource['css']);
		
		// others
		foreach ($externalResource AS $key => $value) {
			$htmlExternal .= "\n\t<!-- Assets - ".$key." -->\n\t";
			$htmlExternal .= implode("\n\t",$externalResource[$key]);
			$htmlExternal .= "\n\t";
		}
	} else {
		// user specifik type
		$htmlExternal .= "\n\t<!-- Assets - ".$type." -->\n\t";
		$htmlExternal .= implode("\n\t",$externalResource[$type]);
		$htmlExternal .= "\n\t";
	}
	echo $htmlExternal;
}
?>