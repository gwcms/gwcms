{extends file="default_list.tpl"}



{block name="init"}

	{$users = $app->user->getOptions()}


	{function name=dl_cell_user_id}
		{$users[$item->user_id]}
	{/function}
	
	{function dl_cell_comment}
		{GW_Link_Helper::parse($item->comment)} 
		{if $item->update_time!='0000-00-00 00:00:00' && $item->update_time!=''}
			<small><a href="#" onclick="return false" title='Redaguota {$item->update_time}'>(R)</a></small>
		{/if}
	{/function}

	{$dl_output_filters=[insert_time=>short_time]}
		
	{$dl_smart_fields = [user_id,description,comment]}
	
	{$do_toolbar_buttons = []}
	{$dl_inline_edit=0}
	
	
	{$dl_actions=[edit,delete_ajax]}
	
	

{/block}	

{block name="after_list"}
<br />

<style>
	.gwViewsOrdersCont{ display: none}
	.gwListTable th{ font-size: 10px; color:#555;padding-top: 1px; padding-bottom:1px; display:none }
	.gwBodyClean2{ padding: 10px !important; }
</style>



<form action="{$m->buildUri(form,[id=>0])}" method="post"  enctype="multipart/form-data" >

<table class="gwTable" style="width:calc(100% - 15px);;margin-left:7px;">
<tr>

<td style="width:10px">	
	<button class="btn btn-primary"><i class="fa fa-save"></i> {GW::l('/g/VIEWS/addcomment')} </button>
</td>

<td>

	<input type="hidden" name="act" value="do:save" />
	<input type="hidden" name="item[id]" value="" />

	{include "elements/input_func.tpl"}
	{call e0 field=comment height="50px" autoresize=1 type=textarea}


</td>

</tr>
</form>

</table>   			

<hr id="lastelement">


</body>



{/block}


