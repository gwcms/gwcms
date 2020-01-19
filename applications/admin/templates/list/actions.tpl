{function list_item_action}
	{if $addlitag}<li>{/if}
	<a  class="{$action_class|default:gwcmsAction} {$action_addclass} {if $shift_button}shiftbtn{/if}"		
		{if $query_param || $onclick || $confirm}
		onclick="{strip}{if $query_param}var ss=window.prompt('{$query_param.1}');if(ss)location.href=gw_navigator.url(this.href, { '{$query_param.0}': ss  });{/if}
			{if $onclick}{$onclick};{/if}
			{if $confirm}{$app->fh()->gw_link_confirm(false)};{/if}
			return false;{/strip}"
		{/if}
		href="{$href|default:'#'}"
		{foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach}
		{if $title}title="{$title|escape}"{/if}
		>{if $iconclass}<i class="{$iconclass}"></i>{/if}{if $caption}<span {if $smallcap}class="gwactcapsmall"{/if}>{$caption}</span>{/if}</a>
	{if $addlitag}</li>{/if}
{/function}

{function list_item_title}{strip}
	{if isset($app->lang.VIEWS[$searchkey])}
		{$app->lang.VIEWS[$searchkey]}
	{elseif isset($m->lang.VIEWS[$searchkey])}
		{$m->lang.VIEWS[$searchkey]}
	{else}
		{$searchkey}
	{/if}
{/strip}{/function}

{function list_item_action_m}
	{if $url}
		{if $url_return_to}
			{$url.1.return_to=$url_return_to}
		{/if}			
		{if !$title && $url.1 && ($url.1.act || $url.0)}
			{$searchkey=$url.1.act|default:basename($url.0)}
			{call list_item_title assign=title}
		{/if}

		{if $autocaption && !$caption && $url.1}
			{$searchkey=$url.1.act|default:basename($url.0)}
			{call list_item_title assign=caption}
		{/if}
	{/if}
	
	{if $smarty.get.RETURN_TO}
		{$url.1.RETURN_TO=$smarty.get.RETURN_TO}
	{/if}
	

	
	{$href=$href|default:$m->buildUri($url.0,$url.1,$url.2)}
	{list_item_action}
{/function}


{function name=dl_actions_moveold}
	{gw_link do="move" icon="action_move_up" params=[id=>$item->id,where=>up] show_title=0}
	{gw_link do="move" icon="action_move_down" params=[id=>$item->id,where=>down] show_title=0}
{/function}

{function name=dl_actions_move}
	{if $m->canBeAccessed($item, [access=>$smarty.const.GW_PERM_WRITE,nodie=>1])}
		{list_item_action_m url=[false,[act=>doMove,id=>$item->id,where=>up]] iconclass="fa fa-arrow-circle-up text-success"}
		{list_item_action_m url=[false,[act=>doMove,id=>$item->id,where=>down]] iconclass="fa fa-arrow-circle-down text-info"}
	{/if}
{/function}




{function name=dl_actions_deleteold}
	{gw_link do="delete" icon="action_file_delete" params=[id=>$item->id] show_title=0 confirm=1}
{/function}

{function name=dl_actions_delete}
	{if $m->canBeAccessed($item, [access=>$smarty.const.GW_PERM_WRITE,nodie=>1])}
		{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 shift_button=1}
	{/if}
{/function}

{function name=dl_actions_delete_ajax}
	{dl_actions_delete action_addclass="ajax-link"}
{/function}




{function name=dl_actions_editold}
	{gw_link relative_path="`$item->id`/form" icon="action_edit" show_title=0}
{/function}

{function name=dl_actions_edit}
	{list_item_action_m url=["`$app->idInPath($item->id)`/form"] iconclass="fa fa-pencil-square-o text-brown"}
{/function}

{function name=dl_actions_editshift}
	{list_item_action_m url=["`$app->idInPath($item->id)`/form"] iconclass="fa fa-pencil-square-o text-warning"  shift_button=1}
{/function}

{function name=dl_actions_invert_activeold}
	
	{gw_link do="invert_active" icon="active_`$item->active`" params=[id=>$item->id] show_title=0}
{/function}


{function name=dl_actions_invert_active}
	{if $m->canBeAccessed($item, [access=>$smarty.const.GW_PERM_WRITE,nodie=>1])}
		{list_item_action_m url=[false, [act=>doInvertActive,id=>$item->id]] iconclass="fa fa-flag gw_active_`$item->active`"}
	{/if}
{/function}

{function name=dl_actions_invert_active_ajax}
	{dl_actions_invert_active action_addclass="ajax-link"}
{/function}


{function name=dl_display_actions}
	{foreach $dl_actions as $button_func}
		{call name="dl_actions_`$button_func`"}
	{/foreach}	
{/function}


{function name=dl_actions_clone}
	{list_item_action_m url=["`$item->id`/form", [act=>doClone, id=>$item->id]] iconclass="fa fa-files-o text-mint"}
{/function}

{function name=dl_actions_ext_actions}
	
	<div class="btn-group dropright gwcmsAction" style="display: inline;vertical-align: top;">

		<i class="fa fa-bars dropdown-toggle dropdown-toggle-icon gwcms-ajax-dd" data-toggle="dropdown" data-url="{$m->buildURI('itemactions',[id=>$item->id])}"></i>	
		    <ul class="dropdown-menu dropdown-menu-right">
			<li><i class="fa fa-spinner fa-pulse"></i></li>
		    </ul>
	</div>	
	
												
	{if !isset($GLOBALS.dropdown_init_done)}
		{$GLOBALS.dropdown_init_done=1}
		<script type="text/javascript">
			require(['gwcms'], function(){
			gwcms.initDropdowns();
		});
		</script>
	{/if}												
	
{/function}



{function dl_actions_inlineedit}
	{list_item_action_m action_class="inline_edit_trigger" iconclass="fa fa-pencil-square-o text-brown" onclick="return false" tag_params=["data-id"=>$item->id]}
{/function}



{function dl_cl_actions_invertactive}
	<option value="checked_action('{$m->buildUri(false,[act=>doSeriesAct,action=>doInvertActive])}', 1)">{GW::l('/A/VIEWS/doInvertActive')}</option>
{/function}

{function dl_cl_actions_dialogremove}
	<option value="checked_action('dialogremove')">{GW::l('/A/VIEWS/dialogremove')}</option>
{/function}

