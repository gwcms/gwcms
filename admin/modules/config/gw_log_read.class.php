<?php


class GW_Log_Read
{
	function cutXStr(&$haystack,$needle,$x)
	{
		$pos=0;$cnt=0;	
		while( $cnt < $x && ($pos=strpos($haystack,$needle,$pos)) !==false ){$pos++;$cnt++;}	
		return $pos==false ? false:substr($haystack,$pos,strlen($haystack));
	}
	
	
	function linesRead($file, $lines, &$fsize=0)
	{
		$f=fopen($file,'r');
		if(!$f)return Array();
		
		
		$splits=$lines*50;
		if($splits>10000)$splits=10000;
	
		$fsize=filesize($file);
		$pos=$fsize;
		
		$buff1=Array();
		$cnt=0;
	
		while($pos)
		{
			$pos=$pos-$splits;
			
			if($pos<0){ $splits+=$pos; $pos=0;}
	
			fseek($f,$pos);
			$buff=fread($f,$splits);
			if(!$buff)break;
			
			$lines -= substr_count($buff, "\n");
	
			if($lines <= 0)
			{
				$buff1[] = self::cutXStr($buff,"\n",abs($lines)+1);
				break;
			}
			$buff1[] = $buff;
		}
	
		return str_replace("\r",'',implode('',array_reverse($buff1)));
	}
	
	function offsetRead($file, &$offset)
	{
		$fsize=filesize($file);
	
		if($fsize == $offset)
			return '';
	
		if($fsize < $offset)
			$offset=0;
	
		$f=@fopen($file,'r');	
	
		fseek($f, $offset);
	
		$buff=fread($f, $fsize-$offset+1);
		$offset=$fsize;
	
		return	$buff;
	}
}