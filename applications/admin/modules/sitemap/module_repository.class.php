<?php

class Module_Repository extends GW_Common_Module 
{

	
	function init()
	{	
		parent::init();

		$this->model = new GW_FSFile();
		
		
		$dir = GW::s('DIR/REPOSITORY');
		
		if(isset($_GET['parent']) && $_GET['parent'] !='..')
		{
			$relpath = str_replace('../', '', $_GET['parent']);
			$path = $dir.$relpath.'/';
			
			
			if(is_dir($path)){
				$dir = $path;
				$this->breadcrumbsAttach();
			}
			
						
		}
		$this->model->root_dir = $dir;
		$this->model->base_dir = GW::s('DIR/REPOSITORY');
	
		
		$this->app->carry_params['clean']=1;
		$this->app->carry_params['parent']=1;
		
		//d::dumpas($this->app);
		
			
		
	}
	
	

	
	function viewFileSelect()
	{
		
		
		
	}
	
	
	function isImage($filename)
	{
		return in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif']);
	}
	
	function viewFilesList()
	{
		$dir = $_GET['dir'];			
		$dir = str_replace('../', '', $dir);
		$root=GW::s('DIR/REPOSITORY');
		$files0 = glob($root.$dir.'*');
		
		$files = [];
		$dirs = [];
		
		usort( $files0, function( $a, $b ) { return filemtime($a) - filemtime($b); } );
		
		foreach($files0 as $idx =>  $file){
			$relative = str_replace($root,'',$file);
			
			if(is_dir($file))
				$dirs[$file] = $relative;
			else
				$files[$file] = $relative;
		}
		
		if(isset($_GET['ftype']) && $_GET['ftype']=='image')
			foreach($files as $idx => $file)
				if(!$this->isImage($file))
					unset($files[$idx]);	
				
		$this->tpl_vars['dirs'] = $dirs;
		$this->tpl_vars['files'] = $files;
	}
	

	
	
	function doUpload()
	{
		$files = GW_File_Helper::reorderFilesArray('files');
		
		$dir = $_GET['dir'];			
		$dir =  GW::s('DIR/REPOSITORY').str_replace('../', '', $dir);
		
		if(!is_dir($dir))
			die('bad dir');
		
		foreach($files as $file)
		{
			if($this->isSecureFile($file)){
				$name = GW_File_Helper::cleanName($file['name']);
				move_uploaded_file($file['tmp_name'], $dir.'/'.$name);
			}
		}
		
		die('OK');
	}
	
	function doMkDir()
	{
		$name = preg_replace('/[^a-z0-9]/i', '_', $_GET['foldername']);
		
		$new = $this->model->root_dir.$name;
			
		if(file_exists($new) || is_dir($new)){
			$this->setError("/G/VALIDATION/FILE_EXISTS");
		}else{
			mkdir($new, 0777);
			
			if(is_dir($new)){
				$this->setPlainMessage(GW::l('/m/NEW_FOLDER_ADDED'));
			}else{
				$this->setPlainMessage(GW::l('/m/CREATE_FAILED_MAYBE_CHECK_PERMISSIONS'), GW_MSG_ERR);
			}
		}
		
		
		$this->jump();
	}
	
	
	
	
	
	
	function getListConfig()
	{
		
		//d::dumpas();
		
		$cfg = parent::getListConfig();
		
		$cfg['inputs']['filename']=['type'=>'text'];
	
						
		$cfg["fields"]['ico'] = 'L';
		
		return $cfg;
	}


	function viewPreview()
	{
		
		$item = $this->getDataObjectById();
		
		$this->tpl_vars['item']=$item;
	}
	

	function __eventAfterList()
	{
		$this->tpl_vars['max_upload_size'] = ini_get("upload_max_filesize");
	}

	
	
	function isSecureFile($file)
	{
		if($file['name'] == '.user.ini' || $file['name'] == '.htacess' || strtolower(pathinfo($file['name'], PATHINFO_EXTENSION))=='php')
		{

			$opt=[
			    'subject'=>GW::s('PROJECT_NAME').' security: someone is trying upload suspicious file rejected: '.$file['name'],
			    'body'=>'<pre>'.json_encode([
				'uid'=>$this->app->user->id, 
				'user_title'=>$this->app->user->title,
				'client'=>$_SERVER['REMOTE_ADDR'].' | '.$_SERVER['HTTP_USER_AGENT']
				], JSON_PRETTY_PRINT),
			];

			GW_Mail_Helper::sendMailDeveloper($opt);

		}else{
			return true;
		}	

	}
	
	function doStore()
	{
		$files = GW_File_Helper::reorderFilesArray('files');
		
		foreach($files as $file)
		{
			if($this->isSecureFile($file))
				move_uploaded_file($file['tmp_name'], $this->model->root_dir.$file['name']);	
		}
	}
	
	
	function breadcrumbsAttach()
	{
		
		$breadcrumbs_attach=[];
		
		$parents = explode('/', $_GET['parent']);
		$parents = array_filter($parents);
		//$parents = array_reverse($parents);
		$path = [];	
		

		$breadcrumbs_attach[]=Array
		(
			'path'=> $this->builduri(false, ['parent'=>""],['level'=>2]),
			'title'=>GW::l('/m/ROOT_DIR')
		);

		
		foreach($parents as $name){
			$path[]=$name;
			
			
			$breadcrumbs_attach[]=Array
			(
				'path'=> $this->builduri(false, ['parent'=>implode('/', $path)],['level'=>2]),
				'title'=>$name
			);
		
		}
		
		$this->tpl_vars['breadcrumbs_attach'] =& $breadcrumbs_attach;
	}	
	
	
	
	function moveFile($file, $dest, $succmsg_skip=false)
	{
		$new = $dest.'/'.basename($file);
		
		if(!file_exists($file))
		{
			$this->setError(GW::l('/g/GENERAL/ITEM_NOT_EXISTS'). ' ('.$file.')');
			return false;
		}
			
		if(file_exists($new) || is_dir($new)){
			$this->setError(GW::l("/G/VALIDATION/FILE_EXISTS").' ('.$new.')');
			return false;
		}else{
			rename($file, $new);
			
			if(file_exists($new)){
				if(!$succmsg_skip)
					$this->setMessage(GW::l('/m/ITEM_MOVED'));
				
				return true;
			}else{
				$this->setMessage(GW::l('/m/FAILED_MAYBE_CHECK_PERMISSIONS').' ('.$new.')');
				return false;
			}
		}
	}
	
	function doDragDrop()
	{		
		$base = $this->model->base_dir;
		$dropto = $base.$_GET['dropto'];
		$file = $base.$_GET['itemid'];
		
		
		$this->moveFile($file, $dropto);
		

		$this->jump();
		
	}
	
	
	
	function getFoldersList($basedir)
	{
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basedir));

		foreach ($iterator as $file) {
			if ($file->isDir()) {
				$path = $file->getRealpath();
				//pirma salyga nerodyt hidden failu (prasidedanciu tasku) .sys .sys/images
				if (strpos($path, $this->model->base_dir . '.') !== 0 && strpos($path, $this->model->base_dir)===0){
					$path= str_replace($this->model->base_dir,'',$path);
					$folders[$path] = $path;
				}
			}
		}
		return $folders;
	}
	
	
	
	function filesAcceptIds($store)
	{
		if(isset($_POST['ids']))
		{
			$this->app->sess($store, explode(',', $_POST['ids']));
			exit;
		}else{
			return $this->getSelectedFileList($store);
		}
		
	}
	
	function viewDialogMoveItems()
	{
		$this->filesAcceptIds('moveitems');
		
		$this->tpl_vars['ids'] = $this->app->sess("moveitems");
		$list = $this->getFoldersList($this->model->base_dir);
		$this->smarty->assign('destination_list', $list);			
		
	}	
	
	
	function getSelectedFileList($store)
	{
		$files2move = $this->app->sess($store);
		$list = [];
		foreach($files2move as $id)
		{
			$list[] = $this->model->getPathById($id);
		}
		return $list;
	}
	
	function doDialogMoveItemsSubmit()
	{
		$base = $this->model->base_dir;
		$list = $this->getSelectedFileList('moveitems');
		
		$newdest = $base.$_POST['destination'];
		
		$cnt =0;
		$failed = 0;
		foreach($list as $file){
			if($this->moveFile($file, $newdest, true))
			{
				$cnt++;
			}else{
				$failed++;
			}
		}
		
		$this->setMessage("Moved: $cnt, Failed: $failed");
		
		$this->jumpAfterSave();
	}
	
	
	function doUploadzip()
	{
		if(!$_FILES['zipfile']['tmp_name'] || !preg_match('/\.zip$/', $_FILES['zipfile']['name'])) 
		{
			$this->setError('/G/GENERAL/FAIL');
			$this->jump();
		}
		
		$zip_dir = GW::s('DIR/TEMP').'gw_zipdir_'.rand(0, 9999).'/';
		GW_Install_Helper::createDir($zip_dir);
		
		chdir($zip_dir);
		//exec($cmd='unzip -j ' . $_FILES['zipfile']['tmp_name'] . ' -d' . $zip_dir);	
		
		
		
		$zip = new ZipArchive;
		if ($zip->open( $_FILES['zipfile']['tmp_name']) === TRUE) {
		    $zip->extractTo($this->model->root_dir);
		}
		$zip->close();
		unlink($_FILES['zipfile']['tmp_name']);
		
				
		$this->jump();	
	}
	
	
	function doDownloadZiped()
	{
		$item = $this->getDataObjectById();
		
		
		$base = $item->path;
		$archivename = $item->filename;
		
		foreach($item->files as $file){
			$zipfiles[$file] = str_replace('//','/',str_replace($base, '', $file));
		}
			
		
		if(count($zipfiles)){

			$reposdir = GW::s('DIR/REPOSITORY');
			$workdir="tempdownload/";
			@mkdir($reposdir.$workdir);
			GW_File_Helper::unlinkOldTempFiles($reposdir.$workdir,'24 hour');
			

			$zipname = $workdir.$archivename.'_'.date('ymd_His').'.zip';
			
			
			
			GW_File_Helper::createZip($zipfiles, $reposdir.$zipname);		

			header('Location: /repository/'.$zipname);
		}		
	}
	
	
	function viewDownloadMultiple()
	{
		
		$this->filesAcceptIds('downloaditems');
		
		$base = $this->model->base_dir;
		$list = $this->getSelectedFileList('downloaditems');		

		
		ob_start();
			
		
		$workdir=GW::s('DIR/REPOSITORY')."tempdownload/";
		@mkdir($workdir);
			
		$zip = $workdir.'download_repository_'.date('ymd_His').'.zip';
		
		$ziplist = [];
		foreach($list as $filename){
			
			if(is_dir($filename)){
				$files = GW_File_Helper::rglob($filename.'/*');
				
				//d::dumpas($files);
				
				foreach($files as $filename)
					$ziplist[$filename] = str_replace('//','/',str_replace($base, '', $filename));
				
				
			}else{
				$ziplist[$filename] = str_replace('//','/',str_replace($base, '', $filename));
			}
		}
		
		//d::Dumpas($ziplist);
		
		$errc=GW_File_Helper::createZip($ziplist, $zip, false);
		
		//shell_exec($cmd = "cd $workdir && zip $zip ".basename($copy_dir).'/*');
		
		
		if(!file_exists($zip))
		{
			
			d::dumpas(['nesukurtas zip failas','errc'=>$errc,$ziplist, $zip]);
			
		}else{
			GW_File_Helper::unlinkOldTempFiles($workdir,'24 hour');
		}
		
		
		$errors = ob_get_contents();
		ob_clean();
		
		if($errors)
		{
			die($errors);
		}
		
		$zip = str_replace(GW::s('DIR/ROOT'), Navigator::getBase(), $zip);
		
		
		header('Location: '.$zip);
	}	
	
	function doRemoveMultiple()
	{
		$this->filesAcceptIds('removeitems');
		
		$base = $this->model->base_dir;
		$list = $this->getSelectedFileList('removeitems');	

		$remcnt=0;
		
		foreach($list as $filename){
			$item = $this->model->createNewObject($this->model->getIdByPath($filename));
			if($item->delete())
				$remcnt++;
		}
		
		$this->setMessage("Removed items: $remcnt");
		
		$this->jump();
	}
	
	
	function __eventBeforeDelete($item)
	{		
		if(isset($_GET['shift_key'])){
			if($item->isdir){
				GW_Install_Helper::recursiveUnlink($item->path);
				mkdir($item->path);
				$this->setMessage("Removed Multiple files");
			}
		}
	}
	
	
	
}
