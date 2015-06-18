{include file="default_open.tpl"}
{$dl_actions=[invert_active,move,edit,delete]}
{include file="list/actions.tpl"}

<p>
{gw_link relative_path=form title=$lang.CREATE_NEW icon="action_file_add" params=[id=>0]}
&nbsp;&nbsp;
{$path=import}{if $m->parent->id > 0}{$path="`$m->parent->id`/`$path`"}{/if}
{gw_link relative_path=$path title=$m->lang.ZIP_IMPORT icon="import"}
&nbsp;&nbsp;&nbsp;&nbsp;
{$m->lang.FIELDS.adm_list_style}: {gw_link do=toggle_list_style title=$m->lang.GALLERY_ADM_STYLE_OPT[$m->config->adm_list_style]}
</p>

<br />

{if !count($list)}
	<p>{$lang.NO_ITEMS}</p>
{else}

{if !$m->config->adm_list_style}


<table class="gwTable gwActiveTable" style="width:auto">
<tr>
	<th></th>
	<th>{$app->fh()->fieldTitle(title)}</th>
	<th>{$app->fh()->fieldTitle(insert_time)}</th>
	<th>{$app->fh()->fieldTitle(update_time)}</th>
	<th>{$lang.ACTIONS}</th>
</tr>


{$thumbn_sz=$m->config->adm_thunmbnails_size}

{foreach from=$list item=item}
	{$id=$item->id}
	
<tr {if $smarty.get.id==$id}class="gw_active_row"{/if}>
	
	<td valign="center">
		{if $item->type==1}
			<img src="{$app_root}img/icons/folder.png" align="absmiddle" vspace="2" />
		{else}
			{$image=$item->image}
			<img src="{$app->sys_base}tools/imga/{$image->id}?size={$thumbn_sz}" align="absmiddle" vspace="2" />
		{/if}
		
		
	</td>
	
	<td>
		{if $item->type!=0}
			{gw_link params=[pid=>$id] title=$item->title}
		{else}
			{$item->title}
		{/if}
		
		{if $item->child_count}
			({$item->child_count})
		{/if}
	</td>
	<td>{$item->get('insert_time')}</td>
	<td>{$item->get('update_time')}</td>

	<td nowrap >
		{call dl_display_actions}
	</td>
</tr>

{/foreach}

</table>




{else}
{$dl_actions=[invert_active,edit,delete]}

	<script type="text/javascript">
		$(function() {
			$("#sortable").sortable({ items: 'li.sortable', update: function(){ $('#sortable_actions').fadeIn() } });
			$("#sortable").disableSelection();
			$('#sortable li')
				.mouseover(function(){  $(this).find('td:eq(2)').show() })
				.mouseout(function(){  $(this).find('td:eq(2)').hide() })
				
			$('#sortable .inactive').css('opacity', '0.5')
		});
	</script>
	<style type="text/css">
		#sortable { list-style-type: none; margin: 0; padding: 0; }
		#sortable li { margin: 3px 3px 30px 0; padding: 1px; float: left;height: 150px;  }
	</style>

	<div id="sortable_actions"  style="display:none">
		<button onclick="gw_sortable.apply('#sortable')">{$lang.APPLY_SORT}</button>
		<button onclick="location.href=location.href">{$lang.CANCEL}</button>
	</div>
	
{$index=0}

	<ul id="sortable">
{foreach from=$list item=item}

	<li class="{if $item->type==0}sortable{/if}{if !$item->active} inactive{/if}" {if $item->type==0}id="sortable_{$index++}_{$item->id}"{/if}>

		<table>
			<tr><td align="center" style="border:1px solid silver;height:136px">
				{if $item->type==1}{*folder*}
					{$src="{$app_root}img/icons/folder_128x128.png"}
					{$link=$app->fh()->gw_path([params=>[pid=>$item->id]])}
					
					{include file="`$m->tpl_dir`/folder_icon.tpl"}
					
				{else}{*image*}
					{$image=$item->image}
					{$src="{$app->sys_base}tools/imga/`$image->id`?size=128x128"}
					{$link=$app->fh()->gw_path([params=>[id=>$item->id], relative_path=>"`$item->id`/form"])}
					<a href="{$link}"><img src="{$src}" align="absmiddle" vspace="2" border="1" /></a>		
				{/if}
				
				
			</td></tr>
			<tr><td align="center">
				<a href="{$link}">{$item->title}</a>		
			</td></tr>
			<tr><td style="display:none;text-align:center">{call dl_display_actions}</td></tr>
			
		</table>
	</li>
{/foreach}

	</ul>
	
{/if}
{/if}



{include file="default_close.tpl"}