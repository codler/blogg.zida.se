<?php
/*
 * This is file class
 *
 * @author			Han Lin Yap (aka Codler)
 * @website			http://www.zencodez.net
 * @last-modified	7th Mars 2010
 * @version			1.0
 * ----------------------------------------
*/
?>
<?php
// 22:24 2009-11-02

/* @Methods
 * delete($file) <> static
*/
class file 
{
    public static function delete($file) 
	{
		if (file_exists($file)) {
			unlink($file);
			return true;
		}
		return false;
    }
}

/* @Methods
 * checksum($file)
 * folderSize($path,$recusive=true)
 * safe(&$filename)
*/
class fileManagement 
{	
	// md5 checksum
	function checksum($file) 
	{
		return md5(file_get_contents($file));
	}

	// return extension with dot
	function extension($name) 
	{
		return strtolower(strrchr($name,"."));
	}
	
	// if path is file, return filesize
	function folderSize($path,$recusive=true) 
	{
		if (!file_exists($path)) return 0;
		if (is_file($path)) return filesize($path);
		$size = 0;
		foreach(glob($path."/*") AS $file) 
		{
			if ($file != "." && $file != "..") 
			{
				if ($recusive) 
				{
					$size += $this->folderSize($file,true);
				} 
				else 
				{
					$size += filesize($file);
				}
			}
		}
		return $size; // return bytes
	}
	
	// safe filename fancy url, vanilla url
	function safe(&$name) 
	{
		$name = preg_replace("/[^a-z0-9-]/", "-", strtolower($name));
		return $name;
	}
	
	// Execute external program optipng!
	function optipng($fullPath) 
	{
		// check if fullpath
		if (strpos($fullPath,"/") !== 0) 
			return false;
			
		exec("/usr/local/bin/optipng ".escapeshellcmd($fullPath),$output);
		return $output; // return array
	}
}

// TODO: Allowed Extension?
// move function unique to usermanagement?

/*	How to use (Minimal) 
	$fileUpload = new fileUpload("FILE-FORM-NAME");
	if ($fileUpload) {
		$fileUpload->setPath(dirname(__file__)."/"."PATH");
		if ($fileUpload->check())
			$fileUpload->save();
			// $fileUpload->save(true); // optimize image
	}
*/
// Upload class
class file_upload extends fileManagement
{
	var $tempName;
	var $filename;
	var $formName;
	var $size;
	var $type;
	
	var $extension;
	
	// Current directory
	var $path = ".";
	
	// Max 2MB
	var $maxSize = 2048000; // bytes
	
	function __construct($name) 
	{
		$this->formName = $name;
			
		$this->tempName = $_FILES[$this->formName]['tmp_name'];
		$this->filename = $_FILES[$this->formName]['name'];
		$this->size = $_FILES[$this->formName]['size'];
		$this->type = $_FILES[$this->formName]['type'];
		
		$this->filename = basename($this->filename);
		$this->extension = parent::extension($this->filename);
		// remove extention
		$this->filename = substr($this->filename,0,-strlen($this->extension));
		parent::safe($this->filename);
	}
	
	function check() 
	{
		if ($this->maxSize < $this->size)
			return false;
		
		$this->unique();
		
		return true;
	}
		
	function getExtension()
	{
		return $this->extension;
	}
	function getFilename() 
	{
		return $this->filename;
	}
	function getFullName()
	{
		return $this->path.$this->filename.$this->extension;
	}
	function getPath()
	{
		return $this->path;
	}
	
	function valid()
	{
		if (empty($_FILES))
			return false;
		
		// Error on upload
		if ($_FILES[$this->formName]["error"] > 0)
			return false;
			
		// security
		if (!is_uploaded_file($this->tempName))
			return false;
			
		return true;
	}
	
	function save($optimize=false) 
	{
		move_uploaded_file($this->tempName,$this->getFullName());
		
		// Optimize image if png-file
		if ($optimize) {
			if ($this->extension==".png")
				parent::optipng($this->getFullName);
		}
	}
	
	function setPath($path) 
	{
		$this->path = $path;
	}
	
	// In bytes
	function setSize($bytes) 
	{
		$this->maxSize = $bytes;
	}
	
	function unique() 
	{
		
		// Unique filename
		if (file_exists($this->path.$this->filename.$this->extension)) {
			$number = 1;
			while (file_exists($this->path.$this->filename.$number.$this->extension)){
				$number++;
			}
			$this->filename = $this->filename . $number; 
		}
	}
}

?>