<?php 

class GW_Image_Helper
{
	static function formFromPhpInfoArr($php_info_arr)
	{
		$vals = Array
		(
			'new_file'=>$php_info_arr['tmp_name'],
			'size'=>$php_info_arr['size'],
			'original_filename'=> $php_info_arr['name'],
		);

		return $vals;
	}
    
	static function formFromFile($file)
	{
		$vals = Array
		(
			'new_file'=>$file,
			'size'=>filesize($file),
			'original_filename'=> pathinfo($file, PATHINFO_BASENAME),
		);

		return $vals;    
	}
    
	static function __setFiles(&$item) 
	{                                                                                                                                                      
	    foreach ($_FILES as $name => $value)
		    //dump($name + "=>" + $value);                                                                                                                   
		    if($item->isCompositeField($name))                                                                                                  
			    self::__setFile($item, $name);

	}
    	
	static function __setFile(&$item, $fieldname) 
	{ 
		if(($file = $_FILES[$fieldname]) && ($file['error'] != UPLOAD_ERR_NO_FILE))                                                                              
				return $item->set($fieldname, self::formFromPhpInfoArr($_FILES[$fieldname]));                                                          
	}

    
    
	static function setFileFromURL(&$item, $name, $url, $dim = NULL)
	{
		if ($url == 'http://') return;

		$file = array(
			'name'		=> basename($url),
			'tmp_name'	=> tempnam(_TEMP_DIR, "web_file"),
			);

		$file['error'] = UPLOAD_ERR_NO_FILE;

		if (self::__RemoteFileExists($url) && $content = file_get_contents($url))
		{
			$fp = fopen($file['tmp_name'], 'w+');
		   	fwrite($fp, $content);
		   	fclose($fp);
		   	$file['error'] = UPLOAD_ERR_OK;
		}

		$file['size'] = (int)@filesize($file['tmp_name']);

		return self::checkAndSetFile($item, $name, $file, $dim);
	}
    
	static function __RemoteFileExists( $url ) 
	{
		  $url_info = parse_url( $url );
		
		  if (empty($url_info['host']))
		  	return null;
		
		  if (empty($url_info["port"]))
		  	$url_info["port"] = 80;
		
		  $fp = fsockopen( $url_info["host"], $url_info["port"] );
		
		  $out  = "GET " . $url_info["path"] . " HTTP/1.1\r\n";
		  $out .= "Host: " . $url_info["host"] . "\r\n";
		  $out .= "Connection: Close\r\n\r\n";
		
		  fwrite( $fp, $out );
		  $t = fread( $fp, 12 );
		  fclose( $fp );
		
		  if( substr( $t, -3 ) == "200" ) {
		    return 1;
		  } else {
		    return 0;
		  }
	}
    
}