<?php


class Module_ChangeTrack extends GW_Common_Module
{	
	public $primitive_change_render = false;
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		
		$this->app->carry_params['owner_type']=1;
		$this->app->carry_params['owner_id']=1;
		$this->app->carry_params['transaction_id']=1;
		$this->app->carry_params['user_id']=1;
		$this->app->carry_params['clean']=1;
		
		if(isset($_GET['owner_type']))
			$this->filters['owner_type']=$_GET['owner_type'];
		
		if(isset($_GET['owner_id']))
			$this->filters['owner_id']=$_GET['owner_id'];
		
		if(isset($_GET['transaction_id']))
			$this->filters['transaction_id']=(int)$_GET['transaction_id'];

		if(isset($_GET['user_id']))
			$this->filters['user_id']=(int)$_GET['user_id'];
		
		if(isset($this->filters['owner_id']) || isset($this->filters['transaction_id']))
			$this->list_params['paging_enabled'] = false;
		
		
		
		if(isset($this->filters['owner_type']))
			$this->checkOwnerPermission($this->filters['owner_type']);
		
	}
	
	
	
	function checkOwnerPermission($owner_type, $error = true)
	{
		if(!($res = GW_Permissions::canAccess($owner_type, $this->app->user->group_ids)))
		{
			$this->setError(GW::l('/G/GENERAL/ACTION_RESTRICTED').' ("'.$owner_type.'"; "'.$res.'")');
		}
		
		return $res;
	}
	
	
	function canBeAccessed($item, $opts=[]) 
	{
		if($item){
			$item->load_if_not_loaded();
	
			$result = $this->checkOwnerPermission($item->owner_type);
		}else{
			$result = $this->app->user->isRoot();
		}

		if (!isset($opts['die']) || $result)
			return $result;

		$this->jump();
	}	
	
	
	function getListConfig()
	{

		$cfg = parent::getListConfig();
		
		if(isset($this->filters['owner_type'])){
			unset($cfg['fields']['owner_type']);
		}
		
		if(isset($this->filters['owner_id'])){
			unset($cfg['fields']['owner_id']);
			unset($cfg['fields']['user_id']);
			unset($cfg['fields']['field']);
			unset($cfg['fields']['diff']);
			unset($cfg['fields']['last']);
			unset($cfg['fields']['undone']);
		}
		
		if(isset($this->filters['transaction_id'])){
			unset($cfg['fields']['transaction_id']);
		}

		if(isset($this->filters['user_id'])){
			unset($cfg['fields']['user_id']);
		}
		
		$cfg['fields']['username'] = 'Lo';
		$cfg['fields']['note'] = 'Lo';
		$cfg['fields']['transaction_id'] = 'Lo';
		$cfg['fields']['changestable'] = 'xL';
		
		unset($cfg['fields']['update_time']);
		//unset($cfg['fields']['diff']);
		unset($cfg['fields']['old']);
		unset($cfg['fields']['new']);

		return $cfg;
	}
		
	function __eventBeforeListParams(&$params)
	{		
		
		$params['key_field']='id';
		
		
		$params['select']="a.*, usr.username, TRIM(CONCAT(COALESCE(usr.name,''), ' ', COALESCE(usr.surname,''))) as usertitle";

			
		$params['joins']=[
		    ['left','gw_users AS usr','a.user_id = usr.id'],
		];	
								
		
		
	}	
	
	function __eventAfterList(&$list)
	{
		if(isset($this->filters['owner_id'])){
			foreach($list as $item){
				if(!empty($item->undone))
					$item->row_class = trim(($item->row_class ?? '').' changetrack-row-undone');
				elseif(!empty($item->last))
					$item->row_class = trim(($item->row_class ?? '').' changetrack-row-last');
			}
		}
		
		$this->primitive_change_render = count($list) > 250;
		$this->tpl_vars['primitive_change_render'] = $this->primitive_change_render;
		
		if($this->primitive_change_render){
			$this->setMessageEx([
				'text' => GW::l('/G/changetrack/PLAIN_CHANGE_VIEW_ENABLED'),
				'type' => GW_MSG_INFO,
			]);
		}
		
	}

	protected function getOwnerModuleKey($owner_type)
	{
		$parts = explode('/', (string)$owner_type);
		return $parts[0] ?? '';
	}

	protected function getOwnerSubmoduleKey($owner_type)
	{
		$parts = explode('/', (string)$owner_type);
		return $parts[1] ?? '';
	}

	protected function splitMultilangFieldVariant($field)
	{
		$field = (string)$field;
		if(!$field || strpos($field, '_') === false)
			return [null, null];
		
		$langs = array_merge((array)GW::s('LANGS'), (array)GW::s('i18nExt'));
		$langs = array_values(array_unique(array_filter($langs)));
		
		if(!preg_match('/^(.*)_([a-z]{2,10})$/i', $field, $m))
			return [null, null];
		
		$base_field = $m[1];
		$lang_code = strtolower($m[2]);
		
		if(!$base_field || !in_array($lang_code, $langs))
			return [null, null];
		
		return [$base_field, $lang_code];
	}

	protected function formatFieldLangSuffix($lang_code)
	{
		$lang_code = strtoupper((string)$lang_code);
		if(!$lang_code)
			return '';
		
		return ' <span title="'.htmlspecialchars($lang_code, ENT_QUOTES, 'UTF-8').'">('.htmlspecialchars($lang_code, ENT_QUOTES, 'UTF-8').')</span>';
	}

	function getOwnerTypeLabelMeta($owner_type)
	{
		$modpath = explode('/', (string)$owner_type, 2);
		$short = '';
		
		if(count($modpath) > 1){
			$short = GW::l('/M/'.$modpath[0].'/MAP/childs/'.$modpath[1].'/title', ['asis' => 1]);
			if($short === '/M/'.$modpath[0].'/MAP/childs/'.$modpath[1].'/title')
				$short = '';
		}else{
			$short = GW::l('/M/'.$modpath[0].'/MAP/title', ['asis' => 1]);
			if($short === '/M/'.$modpath[0].'/MAP/title')
				$short = '';
		}
		
		if(!$short)
			$short = (string)$owner_type;
		
		return [
			'short' => $short,
			'title' => (string)$owner_type,
		];
	}

	protected function translateFieldLabel($owner_type, $field)
	{
		return $this->getFieldLabelMeta($owner_type, $field)['short'];
	}

	protected function getFieldLabelMeta($owner_type, $field)
	{
		$module = $this->getOwnerModuleKey($owner_type);
		$field_key = strpos($field, '/') !== false ? substr($field, strrpos($field, '/') + 1) : $field;
		list($field_key_base, $field_lang_code) = $this->splitMultilangFieldVariant($field_key);
		$field_variants = [$field_key];
		
		if($field_key_base)
			$field_variants[] = $field_key_base;
		
		$field_variants[] = $field;
		$field_variants = array_values(array_unique(array_filter($field_variants)));
		$short = '';
		$long = '';
		
		foreach($field_variants as $variant){
			if(!$variant)
				continue;
			
			$candidates = [
				['type' => 'short', 'path' => "/M/{$module}/FIELDS_SHORT/{$variant}"],
				['type' => 'long', 'path' => "/M/{$module}/FIELDS/{$variant}"],
				['type' => 'short', 'path' => "/A/FIELDS_SHORT/{$variant}"],
				['type' => 'long', 'path' => "/A/FIELDS/{$variant}"],
			];
			
			foreach($candidates as $candidate){
				$tmp = GW::l($candidate['path'], ['asis' => 1]);
				if(!$tmp || $tmp === $candidate['path'])
					continue;
				
				if($candidate['type'] == 'short' && !$short)
					$short = $tmp;
				
				if($candidate['type'] == 'long' && !$long)
					$long = $tmp;
			}
		}
		
		if(!$short)
			$short = $long ?: $field_key;
		
		if(!$long)
			$long = $field_key;
		
		if($field_lang_code){
			$lang_suffix = $this->formatFieldLangSuffix($field_lang_code);
			$short .= $lang_suffix;
			$long = trim(strip_tags((string)$long)).' ('.strtoupper($field_lang_code).')';
		}
		
		$title_parts = [$field];
		if(trim(strip_tags((string)$long)) !== trim(strip_tags((string)$short)))
			$title_parts[] = trim(strip_tags((string)$long));
		
		return [
			'short' => $short,
			'long' => $long,
			'raw' => $field,
			'title' => implode(' | ', array_filter($title_parts)),
		];
	}

	protected function translateFieldValue($owner_type, $field, $value)
	{
		if(is_array($value) || is_object($value))
			return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		
		if($value === null)
			return 'null';
		
		if($value === '')
			return 'empty';
		
		$module = $this->getOwnerModuleKey($owner_type);
		$field_key = strpos($field, '/') !== false ? substr($field, strrpos($field, '/') + 1) : $field;
		
		$candidates = [
			"/M/{$module}/OPTIONS/{$field_key}/{$value}",
			"/A/OPTIONS/{$field_key}/{$value}",
		];
		
		foreach($candidates as $path){
			$tmp = GW::l($path, ['asis' => 1]);
			if($tmp && $tmp !== $path)
				return trim(strip_tags((string)$tmp)).' ('.(string)$value.')';
		}
		
		return (string)$value;
	}

	protected function normalizeInlineDiffText($value)
	{
		return GW_Change_Track_Render_Helper::normalizeText($value);
	}

	protected function shouldUseSummaryLink($old, $new)
	{
		return GW_Change_Track_Render_Helper::shouldUseSummaryLink($old, $new);
	}

	protected function buildVersionsLink($change, $field)
	{
		return $this->app->buildUri(
			$change->owner_type.'/'.$change->owner_id.'/versions',
			['field' => $field, 'changeid' => $change->id, 'clean' => 2]
		);
	}

	protected function canUseInlineDiffInCurrentView()
	{
		if($this->primitive_change_render)
			return false;
		
		return isset($this->filters['owner_id']) || isset($this->filters['transaction_id']);
	}

	protected function buildPrimitiveChangeRows($change)
	{
		$rows = $this->buildTrackDiffRows($change->owner_type, $change->old, $change->new);
		
		foreach($rows as $field => &$row){
			if($this->shouldUseSummaryLink($row['old'], $row['new'])){
				$row['mode'] = 'summary_link';
				$row['summary'] = GW_Change_Track_Render_Helper::buildLargeTextSummary($row['old'], $row['new']);
				$row['human_summary'] = GW_Change_Track_Render_Helper::buildHumanSummaryFromTexts($row['old'], $row['new']);
				$row['versions_link'] = $this->buildVersionsLink($change, $field);
				continue;
			}
			
			$row['mode'] = 'old_new';
		}
		unset($row);
		
		foreach((array)$change->diff as $field => $patch){
			$human = GW_Change_Track_Render_Helper::buildHumanSummaryFromPatch($patch);
			$rows[] = [
				'label' => $this->getFieldLabelMeta($change->owner_type, $field),
				'old' => '',
				'new' => '',
				'mode' => 'summary_link',
				'human_summary' => $human,
				'versions_link' => $this->buildVersionsLink($change, $field),
			];
		}
		
		return $rows;
	}

	protected function shouldUseInlineDiff($old, $new)
	{
		if(!$this->canUseInlineDiffInCurrentView())
			return false;
		
		if(!is_string($old) || !is_string($new))
			return false;
		
		$old_norm = $this->normalizeInlineDiffText($old);
		$new_norm = $this->normalizeInlineDiffText($new);
		
		if($old_norm === '' && $new_norm === '')
			return false;
		
		$max_len = max(mb_strlen($old_norm), mb_strlen($new_norm));
		if($max_len > 4000)
			return false;
		
		if(($old_norm === '' || $new_norm === '') && $max_len >= 40)
			return true;
		
		if($max_len < 120)
			return false;
		
		similar_text($old_norm, $new_norm, $percent);
		return $percent >= 60;
	}

	protected function renderInlineDiffHtml($old, $new)
	{
		$old_norm = $this->normalizeInlineDiffText($old);
		$new_norm = $this->normalizeInlineDiffText($new);
		
		if($old_norm === '' && $new_norm === '')
			return '';
		
		return diff_helper::toHTML(diff_helper::compare($old_norm, $new_norm, true), '');
	}

	protected function loadObjectByChange($change)
	{
		if($change->owner_type == 'competitions/particservices'){
			$fields = array_keys((array)$change->old + (array)$change->new + (array)$change->diff);
			$ordered_service_markers = ['order_id', 'order_item_id', 'payd_time', 'canceled'];
			
			if(array_intersect($fields, $ordered_service_markers)){
				return Adb_Ordered_Participant_Services::singleton()->find(['id=?', $change->owner_id]);
			}
		}
		
		$page = GW_ADM_Page::singleton()->getByPath($change->owner_type.'/'.$change->owner_id);
		
		if(!$page)
			return false;
		
		return $page->getDataObject();
	}

	function buildTrackDiffRows($owner_type, $old, $new)
	{
		$old = (array)$old;
		$new = (array)$new;
		
		if(!$old && !$new)
			return [];
		
		$fields = array_unique(array_merge(array_keys($old), array_keys($new)));
		$rows = [];
		foreach($fields as $field){
			$old_exists = array_key_exists($field, $old);
			$new_exists = array_key_exists($field, $new);
			$old_raw = $old_exists ? $old[$field] : null;
			$new_raw = $new_exists ? $new[$field] : null;
			
			if($old_exists && $new_exists && $old_raw == $new_raw)
				continue;
			
			$rows[] = [
				'label' => $this->getFieldLabelMeta($owner_type, $field),
				'old' => $old_exists ? $this->translateFieldValue($owner_type, $field, $old_raw) : '-',
				'new' => $new_exists ? $this->translateFieldValue($owner_type, $field, $new_raw) : '-',
				'field' => $field,
			];
		}
		
		return $rows;
	}

	function buildChangeRows($change)
	{
		if($this->primitive_change_render)
			return $this->buildPrimitiveChangeRows($change);
		
		$diff = (array)$change->diff;
		$rows = $this->buildTrackDiffRows($change->owner_type, $change->old, $change->new);
		foreach($rows as &$row){
			$row['versions_link'] = !empty($row['field']) ? $this->buildVersionsLink($change, $row['field']) : null;
			
			if($this->shouldUseSummaryLink($row['old'], $row['new'])){
				$row['mode'] = 'summary_link';
				$row['summary'] = GW_Change_Track_Render_Helper::buildLargeTextSummary($row['old'], $row['new']);
				if(!empty($row['field']) && isset($diff[$row['field']]) && $diff[$row['field']])
					$row['human_summary'] = GW_Change_Track_Render_Helper::buildHumanSummaryFromPatch($diff[$row['field']]);
				else
					$row['human_summary'] = GW_Change_Track_Render_Helper::buildHumanSummaryFromTexts($row['old'], $row['new']);
				continue;
			}
			
			if($this->shouldUseInlineDiff($row['old'], $row['new'])){
				$row['mode'] = 'inline_diff';
				$row['inline_html'] = $this->renderInlineDiffHtml($row['old'], $row['new']);
			}else{
				$row['mode'] = 'old_new';
			}
		}
		unset($row);
		
		if(!$diff)
			return $rows;
		
		$obj = $this->loadObjectByChange($change);
		if(!$obj || !isset($obj->extensions['changetrack'])){
			if(!$rows)
				return $this->buildRawDiffFallbackRows($change, $diff);
			
			return $rows;
		}
		
		foreach($diff as $field => $patch){
			list($pastversion,) = $obj->extensions['changetrack']->getRevertedContent($field, $change->id);
			$headversion = (string)$obj->get($field);
			
			if($pastversion === $headversion)
				continue;
			
			$rows[] = [
				'label' => $this->getFieldLabelMeta($change->owner_type, $field),
				'old' => (string)$pastversion,
				'new' => $headversion,
				'mode' => 'old_new',
				'field' => $field,
				'versions_link' => $this->buildVersionsLink($change, $field),
			];
			
			$last_idx = array_key_last($rows);
			if($this->shouldUseSummaryLink((string)$pastversion, $headversion)){
				$rows[$last_idx]['mode'] = 'summary_link';
				$rows[$last_idx]['summary'] = GW_Change_Track_Render_Helper::buildLargeTextSummary((string)$pastversion, $headversion);
				$rows[$last_idx]['human_summary'] = GW_Change_Track_Render_Helper::buildHumanSummaryFromPatch($patch);
			}elseif($this->shouldUseInlineDiff((string)$pastversion, $headversion)){
				$rows[$last_idx]['mode'] = 'inline_diff';
				$rows[$last_idx]['inline_html'] = $this->renderInlineDiffHtml((string)$pastversion, $headversion);
			}
		}
		
		if(!$rows)
			return $this->buildRawDiffFallbackRows($change, $diff);
		
		return $rows;
	}

	protected function buildRawDiffFallbackRows($change, $diff)
	{
		$rows = [];
		
		foreach($this->buildTrackTextDiffRows($change->owner_type, $diff) as $row){
			$rows[] = [
				'label' => $row['label'],
				'field' => $row['field'],
				'mode' => 'raw_diff',
				'diff' => (string)$row['diff'],
				'versions_link' => !empty($row['field']) ? $this->buildVersionsLink($change, $row['field']) : null,
			];
		}
		
		return $rows;
	}

	function buildTrackTextDiffRows($owner_type, $diff)
	{
		$diff = (array)$diff;
		$rows = [];
		
		foreach($diff as $field => $value){
			$rows[] = [
				'label' => $this->getFieldLabelMeta($owner_type, $field),
				'field' => $field,
				'diff' => (string)$value,
			];
		}
		
		return $rows;
	}

	function renderTrackTextDiff($owner_type, $diff)
	{
		$rows = $this->buildTrackTextDiffRows($owner_type, $diff);
		$parts = [];
		
		foreach($rows as $row){
			$parts[] = trim(strip_tags((string)$row['label']['short'])).":\n".(string)$row['diff'];
		}
		
		return implode("\n\n", $parts);
	}


}
