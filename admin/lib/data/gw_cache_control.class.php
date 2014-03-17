<?php


/**
 * Browser cache control classes
 * @author wdm
 *
 */

class GW_Cache_Control_File
{
	var $last_modified;
	var $etag;
	var $file;
	
	function __construct($file, $etag=false)
	{
		$this->last_modified=GW_Cache_Control::gmttime(filemtime($file));
		$this->etag = $etag;
		$this->file = $file;
	}
	
	function getEtag()
	{
		return $this->etag = ($this->etag ? $this->etag: md5_file($this->file));
	}
	

	function debug()
	{
		dump(
			Array
			(
				'last_modified'=>Array($this->last_modified, $_SERVER['HTTP_IF_MODIFIED_SINCE']),
				'etag'=>Array($this->getEtag(), $_SERVER['HTTP_IF_NONE_MATCH']),				
			)
		);
	}
	
	function setHeaders()
	{
		header("Last-Modified: ".$this->last_modified); 
		header("Etag: ".$this->getEtag()); 
		header("Cache-Control: public");	
	}
	
	/***
	 * returns true if request object is not modified
	 */
	function test()
	{
		return 
			@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == @strtotime($this->last_modified) || 
			trim($_SERVER['HTTP_IF_NONE_MATCH']) == $this->getEtag();
	}	
	
	function check()
	{
		if($this->test())
		{ 
		    header("HTTP/1.1 304 Not Modified"); 
		    exit; 
		}	
	}
}

class GW_Cache_Control
{
	static function gmttime($time)
	{
		return gmdate('D, d M Y H:i:s', $time).' GMT';
	}
		
	static function checkFile($file, $etag=false)
	{
		$cc = new GW_Cache_Control_File($file, $etag);
		$cc->setHeaders();
		//$cc->debug();
		$cc->check();
	}
	
	static function setExpires($diff='+1 hour')
	{
		header('Cache-Control: public');
		header('Pragma: cache');
		header('Expires: '.self::gmttime(strtotime($diff))); 
	}

}