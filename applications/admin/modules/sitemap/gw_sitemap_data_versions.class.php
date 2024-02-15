<?php


class gw_sitemap_data_versions extends GW_Data_Object
{
	public $i18n_fields = ['title'=>1];
	public $default_order = '`time` DESC';


	function getCurrentContent()
	{
		$orig = gw_sitemap_data::singleton()->find(["`key`=? AND ln=? AND page_id=?", $this->key, $this->ln, $this->page_id]);
		
		return $orig ? $orig->content : false;
	}
	
	function uncompressDiff()
	{
		$u = $this->find(["id=?",$this->id], ["select"=>'uncompress(diff) as udiff']);
		return $u->udiff;
	}
}