<?php

include_once __DIR__.'/gw_movie.class.php';

class Module_Movies extends GW_Common_Module
{	
	
	use Module_Import_Export_Trait;		

	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;			
		
	}	
	
	function getListConfig()
	{
		
		$cfg = array('fields' => [
			'id' => 'Lof', 
			'title' => 'Lof',
			'image'=> 'L',
			'rate' => 'lof',
			'insert_time'=>'lof',
			'update_time'=>'lof'	
			]
		);
		
		//$cfg['filters']['project_id'] = ['type'=>'select','options'=>$this->options['project_id']];
			
			
		return $cfg;
	}	
	
	
	function doGetImdb()
	{
		$imdb = $imdb = new Imdb();
		$info = $imdb->getMovieInfo($_REQUEST['title']);
		
		
		$dir = GW::$dir['REPOSITORY'].($dir1='images/movies/');
		$fn= preg_replace('/[^a-z0-9\(\)_-]/i','_', trim($info['title']));
		
		if($info['poster_small'])
			file_put_contents($dir.($tmp=$fn.'.jpg'), file_get_contents($info['poster_small']));
		
		$images['poster']=$dir1.$tmp;
		
		$info['local_images']=$images;
		
		echo GW_Json_Format_Helper::f($info);
		exit;
		
		
	}
}

?>
