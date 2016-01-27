<?php

//todo - padaryt universalu

trait Module_Import_Export_Trait
{	
	
	function doExportPhotos()
	{
		ob_start();
		if(!$this->filters['competition_id'])
			die('per konkursa atskirai tik veikia');
		
		$competition = IPMC_Competition::singleton()->find(['id=?', $this->filters['competition_id']]);
		
		$vars = $this->viewList();
				
		$log=[];
		$i=1;
		
		$workdir=GW::s('DIR/REPOSITORY').'KonkursuFoto/';	
		
		if(!is_dir($workdir))
			mkdir($workdir);
		
		$comp_title = strtolower(GW_String_Helper::truncate($competition->title,60,'-'));
		$zipname = $competition->id.'_'.$comp_title.'_'.date('ymd_His').'.zip';
		
		$zipname = GW_File_Helper::cleanName($zipname);
		$zip = $workdir.$zipname;
		
		
		//isvalyti ankstensius eksportus
		
		$photos = [];
		
		foreach($vars['list'] as $item)
		{
			if($item->user->printprofilefoto)
			{
				$from = $item->user->printprofilefoto->getFilename();
				
				$list_number=sprintf("%03d", $item->list_number).str_replace('0','',$item->list_number-floor($item->list_number));
				$to = "{$list_number}_{$item->user->name}_{$item->user->surname}.jpg";
				$to = GW_File_Helper::cleanName($to);
				

				$photos[$from]=$to;
			}
		}
		
		//d::dumpas($photos);
		GW_File_Helper::unlinkOldTempFiles($workdir,'24 hour');
		GW_File_Helper::createZip($photos, $zip);
		
		//shell_exec($cmd = "cd $workdir && zip $zip ".basename($copy_dir).'/*');
		
		if(!file_exists($zip))
		{
			d::dumpas('nesukurtas zip failas');
		}
		
		$errors = ob_get_contents();
		
		if($errors)
		{
			die($errors);
		}
		
		$zip = str_replace(GW::s('DIR/ROOT'), Navigator::getBase(), $zip);
		
		
		header('Location: '.$zip);
	}	
}