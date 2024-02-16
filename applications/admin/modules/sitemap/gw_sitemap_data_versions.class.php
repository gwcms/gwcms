<?php


class gw_sitemap_data_versions extends GW_Data_Object
{
	public $i18n_fields = ['title'=>1];
	public $default_order = '`time` DESC';


	public $head_version_time = false;
	
	function getHeadVersion()
	{
		$orig = gw_sitemap_data::singleton()->find(["`key`=? AND ln=? AND page_id=?", $this->key, $this->ln, $this->page_id]);
		
		$this->head_version_time = $orig->update_time;
		
		return $orig ? $orig->content : false;
	}
	
	function uncompressDiff()
	{
		$u = $this->find(["id=?",$this->id], ["select"=>'uncompress(diff) as udiff']);
		return $u->udiff;
	}
	
	
	function getRevertedContent()
	{
		$list = $this->findAll(["`key`=? AND ln=? AND page_id=?", $this->key, $this->ln, $this->page_id]);
		
		$content = $head = $this->getHeadVersion();
		
		foreach($list as $item){
			
			$content = GW_String_helper::applyDiff($item->uncompressDiff(),$content);
			
			if($this->id == $item->id)
				break;
		}
		
		return $content;
		
		
	}
}