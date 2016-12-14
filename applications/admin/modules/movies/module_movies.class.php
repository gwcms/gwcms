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
	
	function doGetImdbInfo()
	{
		$item = $this->getDataObjectById();
		
		
		
		$imdb = new IMDB2($item->title);
		if($imdb->isReady){
			$imdb_api = array();
			$imdb_api['castArray'] = $imdb->getCastArray();
			$imdb_api['directorArray'] = $imdb->getDirectorArray();
			$imdb_api['genreArray'] = $imdb->getGenreArray();
			$imdb_api['genreString'] = $imdb->getGenreString();
			$imdb_api['mpaa'] = $imdb->getMpaa();
			$imdb_api['description'] = $imdb->getDescription();
			$imdb_api['plot'] = $imdb->getPlot();
			$imdb_api['imdbID'] = $imdb->getImdbID();
			$imdb_api['imdbURL'] = $imdb->getUrl();
			$imdb_api['poster'] = $imdb->getPoster();
			$imdb_api['rating'] = $imdb->getRating();
			$imdb_api['runtime'] = $imdb->getRuntime();
			$imdb_api['title'] = $imdb->getTitle();
			$imdb_api['AKA'] = $imdb->getAka();
			$imdb_api['languagesArray'] = $imdb->getLanguagesArray();
			$imdb_api['languagesString'] = $imdb->getLanguagesString();
			$imdb_api['trailer'] = $imdb->getTrailer();
			$imdb_api['isTV'] = $imdb->isTvShow();
			$imdb_api['type'] = $imdb->getType();
			$imdb_api['year'] = $imdb->getYear();
			$imdb_api['userComments'] = $imdb->getUserComments();
			$imdb_api['parentalGuide'] = $imdb->getParentalGuide();
			echo "<PRE>";
			print_r($imdb_api);
			echo "</PRE>";
			
			$item->image = $imdb->getPoster();
			$item->updateChanged();
		}else{
			echo $imdb->status;
		}
		

		
	}
	
	function doBackgroundAterInsert()
	{
		$item = $this->getDataObjectById();
		
		$imdb = new IMDB2($item->title);
		$imdb_api = [];
		if($imdb->isReady){
			$imdb_api = array();
			$imdb_api['genreString'] = $imdb->getGenreString();
			$imdb_api['description'] = $imdb->getDescription();
			$imdb_api['plot'] = $imdb->getPlot();
			$imdb_api['imdbID'] = $imdb->getImdbID();
			$imdb_api['poster'] = $imdb->getPoster();
			$imdb_api['rating'] = $imdb->getRating();
			$imdb_api['runtime'] = $imdb->getRuntime();
			$imdb_api['title'] = $imdb->getTitle();
			$imdb_api['year'] = $imdb->getYear();
			$imdb_api['time'] = date('Ymd');
			
			
			if($imdb_api['imdbID'] && $imdb_api['poster'] != 'N/A'){	
				$item->name_orig = $imdb_api['title'].' '.$imdb_api['year'];
				$tmpfilename=GW::s('DIR/TEMP').'imdbposter_'.GW_File_Helper::cleanName($item->name_orig).'_'.date('Ymd_hmi').'.jpg';

				$data=file_get_contents($imdb_api['poster']);

				file_put_contents($tmpfilename, $data);

				$image = Array
				    (
				    'new_file' => $tmpfilename,
				    'size' => filesize($tmpfilename),
				    'original_filename' => GW_File_Helper::cleanName($item->name_orig).'.jpg',
				);

				$item->set('image1', $image);	
				$item->validate();//resizes image

				$item->update();

				unlink($tmpfilename);
			}else{
				['error'=>'404'];
			}
		}else{
			$imdb_api=['error'=>$imdb->status];
		}
		
		$item->imdb= json_encode($imdb_api, JSON_UNESCAPED_SLASHES);
		$item->updateChanged();
	}
	
	function __eventBeforeSave($item)
	{
		$item->title = str_replace('.', ' ', $item->title);
	}
	
	function __eventAfterSave($item)
	{
		if(!$item->imdb){
			Navigator::backgroundRequest("lt/movies?act=do:BackgroundAterInsert&id=".$item->id);
			$this->setMessage("Imdb update background process started");
		}
	}
	
	function doUpdateAllWithoutImdb()
	{
		$list = $this->model->findAll('imdb=""',['limit'=>10]);
		
		foreach($list as $item)
		{
			Navigator::backgroundRequest("lt/movies?act=do:BackgroundAterInsert&id=".$item->id);
		}
		
		$this->setMessage("Passed for execution ".count($list).' background processes');
	}
	
}

?>
