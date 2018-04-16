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
			'description'=>'lof',
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
	
	function doBackgroundAterInsert($item)
	{
		if(! $item)
			$item = $this->getDataObjectById();
		
		$search = str_replace(['(',')'], ' ', $item->title);
		
		$imdb = new IMDB2($search);
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
	
	function __extendMovieDatabase($item)
	{
		if($item->mdbid){
			$data = $this->extendFromMovieDB($item->mdbid);
			if(!$item->title)
				$item->title = $data['title'];
			
			$item->imdb = json_encode($data, JSON_UNESCAPED_SLASHES);;
			$item->updateChanged();
		}		
	}
	
	function __eventAfterSave($item)
	{
		if(!$item->imdb){
			//Navigator::backgroundRequest("lt/movies?act=do:BackgroundAterInsert&id=".$item->id);
			//$this->setMessage("Imdb update background process started");
			$this->doBackgroundAterInsert($item);
		}
		
		$this->__extendMovieDatabase($item);
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
	
	
	
	function __movie2Html($item)
	{
		$imgrep = "http://image.tmdb.org/t/p/w92/";
		
		$votes = '<i class="fa fa-star"></i> '.$item->vote_average.' ('. $item->vote_count .')';
		$year ="($item->year)";
		
		
		$html = '<div class="clearfix">' .
		    '<div class="col-sm-1">' .
		    '<img src="' . $imgrep.$item->poster_path. '" style="max-width: 100%" />' .
		    '</div>' .
		    '<div clas="col-sm-10">' .
		    '<div class="clearfix">'.
		    "<div class='col-sm-10'> $item->title $year $votes <br> $item->overview </div>" .
		    '</div>';	
		
		return $html;
	}
	
	function searchMovies($query_str, $page)
	{	
		$args = http_build_query(['query'=>$query_str, 'api_key'=>'c26927cb6d010fa46c750bd8babd5dd0', 'page'=>$page]);
		$json = file_get_contents("https://api.themoviedb.org/3/search/movie?".$args);
		
		$resp = json_decode($json);
		
		
		$list=[];
		
		foreach($resp->results as $id => $item){
			list($item->year) = explode('-', $item->release_date);
			$list[]=['id'=>$item->id, "title"=>$item->title.' '.$item->year, 'html'=>$this->__movie2Html($item)];		
		}
		
		
		$res = ['items'=>$list];
		
		if(isset($resp->total_results))
			$res['total_count'] = $resp->total_results;

		return $res;
	}
	
	function doSearchMovies()
	{
		
		$query_str = $_GET['q'];
		$page = $_GET['page'] ?? 1;
		
		$res = $this->searchMovies($query_str, $page);
		
		echo json_encode($res);
		exit;	
				
		//poster base url http://image.tmdb.org/t/p/w92//6K5JOW6HmrwJnP0VILq667cspnS.jpg
		// "w92", "w154", "w185", "w342", "w500", "w780"	
	}
	
	function extendFromMovieDB($id)
	{
		$args = http_build_query(['language'=>'en-US', 'api_key'=>'c26927cb6d010fa46c750bd8babd5dd0']);
		$json = file_get_contents("https://api.themoviedb.org/3/movie/".$id."?".$args);
		$item = json_decode($json);
		
		
		
		list($item->year) = explode('-', $item->release_date);
		
		$f = function($genres){ $l=[]; foreach($genres as $g)$l[]=$g->name; return implode(' ', $l); };
		
		$return = [
		    "mdbid"=>$id,
		    "title"=>$item->title,
		    "poster_external"=>"http://image.tmdb.org/t/p/w92/".$item->poster_path, 
		    "poster_external_big"=>"http://image.tmdb.org/t/p/w780/".$item->poster_path,
		    "overview"=>$item->overview,		    
		    "year"=>$item->year,
		    "genres"=>$f($item->genres),
		    "vote"=>$item->vote_average.' ('. $item->vote_count .')',
		    "runtime"=>$item->runtime,
		    "imdb_id"=>$item->imdb_id,
		    "original_language"=>$item->original_language,
		    "budget"=>$item->budget
		];
		
		return $return;
	}
	
	function doFixMoviews()
	{
		$list = $this->model->findAll("id > 533");
		
		foreach($list as $item)
		{
			$query_str= $item->title;
			
			if($item->imdb && ($dec=json_decode($item->imdb)) && $dec->mdbid)
				continue;
				
			
	
			$query_str = preg_replace('/\(\d\d\d\d\)/','', $query_str);
			$query_str = preg_replace('/20\d\d/','', $query_str);
			$query_str = preg_replace('/19\d\d/','', $query_str);
			
			$res = $this->searchMovies($query_str, 1);
						
			if(isset($res["items"][0]))
			{
				$item->mdbid = $res["items"][0]['id'];
				$this->__extendMovieDatabase($item);
			}
			
		}
	}
	
	
}

?>
