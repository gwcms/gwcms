{extends file="default_list.tpl"}

{block name="open_tpl"}
		{include file="head.tpl"}
		<body style="height: auto">
		
		{include file="messages.tpl"}
{/block}

{block name="init"}

	{$users = $app->user->getOptions()}


	{function name=dl_cell_user_create}
		{$users[$item->user_create]}
	{/function}
	
	{function dl_cell_description}
		{$item->description|make_links_blank} 
		{if $item->update_time!='0000-00-00 00:00:00'}
			<small><a href="#" onclick="return false" title='Redaguota {$item->update_time}'>(R)</a></small>
		{/if}
	{/function}		
		
	{$dl_smart_fields = [user_create,description]}
	{$dl_fields = [description,user_create,insert_time]}
	
	{$dl_toolbar_buttons = []}
	
	{$dl_actions=[edit,delete]}
{/block}	

{block name="after_list"}
<br />




<form action="{$ln}/{$app->path}/form?id=0" method="post"  enctype="multipart/form-data" >

<table class="gwTable" style="width:100%">
<tr><th colspan="2" style="text-align:left">Add new comment</th></tr>
<tr>

<td style="width:10px">	
	<input type="submit" value="{$lang.SAVE}" />
</td>

<td>

	<input type="hidden" name="act" value="do:save" />
	<input type="hidden" name="item[id]" value="{$comment->id}" />
	<input type="hidden"  name="item[user_create]" value="{$comment->user_create|default:$app->user->id}"  />


	<script type="text/javascript" src="{$app_root}js/autoresize.jquery.min.js"></script>
	<script type="text/javascript">
		
		$(document).ready(function(){ 

			$('.ta_autoresize').autoResize(); 
			
		});
	</script>

	<textarea class="ta_autoresize" name="item[description]"  
	style="width: 100%; height: 100px;"  
	onchange="this.value=$.trim(this.value);" >{$comment->description|escape}</textarea>
</td>

</tr>
</form>

</table>   			

<br />
<br />
<br />


</body>



{/block}

{block name="close_tpl"}
{/block}
