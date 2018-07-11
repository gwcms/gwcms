{$default_form_before_form}

{function "e"}
	{include file="elements/input.tpl" name=$field}
{/function}


<form id="itemform" class="itemform" action="{$formendpoint|default:$smarty.server.REQUEST_URI}" method="post"  enctype="multipart/form-data" onsubmit="gwcms.beforeFormSubmit(this)"  >

<table style="width:{if $form_width}{$form_width}{else}600px{/if}" >
<tr>
<td>

{assign var="width_title" value="30%" scope="root"}


<input class="gwSysFields" type="hidden" name="act" value="do:{$action|default:"save"}" />

{if !$nohiddenitemid}
<input class="gwSysFields" type="hidden" name="item[id]" value="{$item->id}" />
{/if}

{if !$item->id}
	<input class="gwSysFields" type="hidden" name="item[temp_id]" value="{$item->temp_id}" />
{else}
	<input class="gwSysFields" type="hidden" name="last_update_time" value="{$item->update_time}" data-ignorechanges="1" />
{/if}


{*if $item->id}
	<input class="gwSysFields" type="hidden" name="item[update_time_check]" value="{if $item->update_time_check}{$item->update_time_check}{else}{$item->update_time}{/if}" />
{/if*}


<script>
	var changes_track={if $changes_track}1{else}0{/if};
	var gw_auto_save={if $item->id && $auto_save }1{else}0{/if};
			
	require(['forms'], function(){ gw_forms.initForms() })	
</script>


			{if $input_tabs}
				{include "tools/input_tabs.tpl"}
			{/if}

	<div class="row panel gwlistpanel">
		<div class="panel-body">
				
			<table class="gwTable gwcmsTableForm">
				<tbody>
