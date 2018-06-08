<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



class GW_NL_Link extends GW_Data_Object
{
	var $table = 'gw_nl_links';

	
	function storeAll($links, $letter_id)
	{
		$rows = [];
		
		foreach($links as $link)
		{
			$rows[] =  ['letter_id'=>$letter_id, 'link'=>$link];
		}
		
		
		if($rows)
			$this->getDB()->multi_insert($this->table, $rows, true);
	}
	
	function getAllidLink($letter_id)
	{
		return $this->getAssoc(['id', 'link'], ['letter_id=?', $letter_id]);
	}
	function getAllLinkId($letter_id)
	{
		return $this->getAssoc(['link', 'id'], ['letter_id=?', $letter_id]);
	}	
	
	function storeNew($links, $letter_id)
	{		
		$prev_links = $this->getAllidLink($letter_id);		
		$new_links = array_diff($links, $prev_links);
		
				
		$this->storeAll($new_links, $letter_id);
	}

}			