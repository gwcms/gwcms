<?php

class GW_Extension_i18next
{
	private $parent;
	public $obj = false;
	public $cacheNotSaved=[];
	
	
	
	
	/*
	public $before_save_to_after = [];
	

	
	function eventHandler($event, &$context_data = array()) {
		
		switch ($event){
			
			case 'BEFORE_SAVE':

				
				if($troutchanged)
				{
					
				}
				
				
				//unset$this->changed_fields[]
			break;
		}
		
		parent::eventHandler($event, $context_data);
	}	
	*/
	
	public $map = [];
	public $select_add = '';
	public $joins_add = [];
	
	
	function schemaQuery($type, $key){
		return  GW::$context->app->i18nSchemaQuery($type, $key);
	}
	
	function __construct($parent, $name)
	{
		$this->parent = $parent;
		$parent->registerObserver(['extension', $name]);
		
		$p = $this->parent;
		$idx = 0;
		
		
		$tableid = $this->schemaQuery('type', $p->table);
		
			
		
		
		
		foreach($p->i18next_langs as $extln){
			foreach($p->i18n_fields as $field => $x){
				
				$fieldid = $this->schemaQuery('field', $field);			
				
				
				$idx++;
				$talias = "tr{$extln}{$idx}";
				$this->select_add .= (($this->select_add) ? ',':'') . "$talias._value AS {$field}_{$extln}";
				$this->joins_add[]=['left',
				    "gw_i18n_{$extln} AS $talias",
				    "a.id = $talias._id AND $talias._type=$tableid AND $talias._field=$fieldid"];
					
				$p->extra_cols = array_merge($p->extra_cols, ["{$field}_{$extln}"]);
				$this->map["{$field}_{$extln}"] = ['field'=>$field, 'ln'=>$extln, 'talias'=>$talias];
			}
		}
			
		
		//d::dumpas($this->parent);
		
	}
	
	function __replaceFieldnames(&$src)
	{
		foreach($this->map as $field => $inf){
			//a.`short_title_fr`
			$src = str_replace("a.`$field`","{$inf['talias']}.`_value`", $src);
			$src = str_replace("a.$field","{$inf['talias']}.`_value`", $src);
			$src = str_replace("`$field`","{$inf['talias']}.`_value`", $src);
			$src = str_replace("$field","{$inf['talias']}.`_value`", $src);
		}	
	}
	
	function __removeFromSelect(&$select){
		$select = explode(',', $select);
		
		
		foreach($select as $idx => $selelm){
			
		
			$selelm = trim($selelm,' `');
			
			foreach($this->map as $field => $inf){
				if($selelm==$field){
					unset($select[$idx]);
				}
			}				
		}
		
		$select = implode(', ', $select);
	}
	
	function attachExtLangArgs(&$options)
	{
		if(isset($options['skip_i18next']))
			return false;
		
		$select = isset($options['select']) ? $options['select'] : 'a.*';
		
		if(isset($options['conditions'])){
			if(is_array($options['conditions']))
				$options['conditions'] = GW_DB::prepare_query($options['conditions']);
			
			$this->__replaceFieldnames($options['conditions']);
		}
		
		if(isset($options['order']))
			$this->__replaceFieldnames($options['order']);
		

		$this->__removeFromSelect($select);
		//a.`title_fr`
		
		
	
		
		if($this->select_add)
			$select.=($select ? ', ': ''). $this->select_add;	
		
		//d::ldump($select);
		$options['select'] = $select;
		
		if ($this->joins_add)
			$options['joins'] = array_merge($options['joins']??[], $this->joins_add);
		
		
		//d::ldump($options);
		
	}
	
	
	function saveNotSaved()
	{
		$tableid = $this->schemaQuery('type', $this->parent->table);
		
		$values = [];
		foreach($this->cacheNotSaved as $field => $val){
			$map = $this->map[$field];
			
			$fieldid = $this->schemaQuery('field', $map['field']);
			
			$values[$map['ln']][] = ['_type'=>$tableid, '_field'=>$fieldid,'_id'=>$this->parent->get('id'), '_value'=>$val];
			
			unset($this->cacheNotSaved[$field]);
		}
		
		foreach($values as $ln => $rows)
			$this->parent->getDb()->multi_insert("gw_i18n_$ln", $rows, true);
		
	}
	
	
	
	function eventHandler($event, &$context_data = [])
	{			
		//d::ldump($event);
		
		switch ($event) {

			case 'BEFORE_SAVE':
				
				foreach($this->map as $field => $info){
					$this->parent->ignore_fields[$field] = 1;
				
					if(isset($this->parent->changed_fields[$field]) || (!$this->parent->id)){
						$this->cacheNotSaved[$field] = $this->parent->content_base[$field];
						unset($this->parent->changed_fields[$field]);
						
						
					}
					
					//d::dumpas($this->cacheNotSaved);
				}	
				
				if($this->parent->id){
					//d::dumpas('test');
					$this->saveNotSaved();
				}
			break;
			
			case 'BEFORE_LIST':
				$this->attachExtLangArgs($context_data);
			break;
		
			case 'AFTER_SAVE':
				$this->saveNotSaved();
				
				
				foreach($this->map as $field => $info){
					unset($this->parent->ignore_fields[$field]);
				}				
				
			break;
			
			case 'BEFORE_DELETE':
				foreach($this->parent->i18next_langs as $extln){
					$conds = GW_DB::buidConditions(['_id'=>$this->parent->id, '_type'=>$this->parent->table]);
					$this->parent->getDb()->delete("gw_i18n_$extln", $conds);
				}
			break;

			
		}
	}
	


}
