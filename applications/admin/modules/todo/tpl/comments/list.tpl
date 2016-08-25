{extends file="default_list.tpl"}



{block name="init"}
	{$no_standart_cms_frame=1}
	{$users = $app->user->getOptions()}


	{function name=dl_cell_user_create}
		{$users[$item->user_create]}
	{/function}
	
	{function dl_cell_description}
		{GW_Link_Helper::parse($item->description)} 
		{if $item->update_time!='0000-00-00 00:00:00' && $item->update_time!=''}
			<small><a href="#" onclick="return false" title='Redaguota {$item->update_time}'>(R)</a></small>
		{/if}
	{/function}

	{$dl_output_filters=[insert_time=>short_time]}
		
	{$dl_smart_fields = [user_create,description]}
	{$dl_fields = [description,user_create,insert_time]}
	
	{$do_toolbar_buttons = []}
	
	{$dl_actions=[edit,delete]}
	
	
	{$url_return_to=$app->path}
	{$url_relative_path=$app->path}
{/block}	

{block name="after_list"}
<br />




<form action="{$app->app_base}{$ln}/{$app->path}/form?id=0" method="post"  enctype="multipart/form-data" >

<table class="gwTable" style="width:calc(100% - 15px);;margin-left:7px;">
<tr><th colspan="2" style="text-align:left">{GW::l('/m/VIEWS/addcomment')}</th></tr>
<tr>

<td style="width:10px">	
	<button class="btn btn-primary"><i class="fa fa-save"></i> {$lang.SAVE}</button>
</td>

<td>

	<input type="hidden" name="act" value="do:save" />
	<input type="hidden" name="item[id]" value="" />
	<input type="hidden"  name="item[user_create]" value="{$comment->user_create|default:$app->user->id}"  />


	{$m->addIncludes("jq/autoresize", 'js', "`$app_root`static/js/jq/autoresize.jquery.min.js")}

	<textarea class="form-control ta_autoresize" name="item[description]"  
	style="width: 100%; height: 100px;"  
	onchange="this.value=$.trim(this.value);" ></textarea>
</td>

</tr>
</form>

</table>   			

<br />
<br />
<br />


</body>



{/block}


