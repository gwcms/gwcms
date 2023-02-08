{if $modpath}
	{$source_args = $source_args|default:[]}
	{$tmppath=explode('/', $modpath,2)}
	{if !$datasource}
		{$datasource=$app->buildUri("`$tmppath.0`/`$tmppath.1`/{$optionsview|default:options}", $source_args)}
	{/if}

	{if !$form_url}
		{$form_url=$app->buildUri("`$tmppath.0`/`$tmppath.1`/form", [clean=>2,dialog=>1]+$source_args)}
	{/if}
	{if !$list_url}
		{$list_url=$app->buildUri("`$tmppath.0`/`$tmppath.1`/list", [clean=>2]+$source_args)}
	{/if}	
	{if !$object_title}
		{$object_title=GW::l("/M/`$tmppath.0`/MAP/childs/`$tmppath.1`/title")}
	{/if}
{/if}
			




<span class="input-group-btn addEditControls" id="addEditControls{$name}" 
	  data-name="{$name}" 
	  data-export_url="{$export_url}" 
	  data-import_url="{$import_url}" 
	  data-minimuminputlength="{$minimuminputlength}"
	  data-multiple="{if $type=="multiselect_ajax"}1{else}0{/if}">
	
	{if $type!="multiselect_ajax"}
		<button class="btn btn-default editBtn" type="button" data-title="{$object_title} :: {GW::l('/g/EDIT')}" data-shifttitle="{$object_title} :: {GW::l('/g/LIST_CONFIG')} " data-url="{$form_url}" data-listurl="{$list_url}"><i class="fa fa-pencil-square-o"></i></button>	
	{/if}
	{if $import_url && $export_url}
	<div class="btn-group">
		<div class="dropdown">
			<button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
				<i class="fa fa-pencil-square-o"></i>
			</button>
			
			<ul class="dropdown-menu dropdown-menu-right" style="">

					<li class="dropdown-header">{GW::l('/g/VALUE')}</li>
					<li><a class="valueIdsBtn" type="button" title="{$object_title} :: {GW::l('/g/VALUE')} ({GW::l('/g/AS_DB_ID')})">{GW::l('/g/AS_DB_ID')}</a></li>
					<li><a class="valueRowsBtn" type="button" title="{$object_title} :: {GW::l('/g/VALUE')} ({GW::l('/g/AS_CSV_ROWS')})">{GW::l('/g/AS_CSV_ROWS')}</a></li>
							
			</ul>
		</div>
	</div>	
	{/if}	
	
	
	{if !$modpath || ($modpath && $app->canAccessX($modpath, $smarty.const.GW_PERM_WRITE))}
		
		<button class="btn btn-default addBtn" type="button" title="{$object_title} :: {GW::l('/g/ADD')}"  data-url="{Navigator::buildURI($form_url,[id=>0])}"><i class="fa fa-plus-circle"></i></button>
	{else}
		{if $debug_edit_add_permission}
			{d::ldump("cant access {$modpath}")}
			
		{/if}
	{/if}

</span>
			
{if !isset($GLOBALS.init_addEdit_input_done)}		
	{$GLOBALS.init_addEdit_input_done=1}
	<script type="text/javascript">
		translate_submit = "{GW::l('/g/SUBMIT')}";
		
		//this will allow open dialog in root window, if this window is iframed
		require(['gwcms'], function(){  require(['pack/select_ajax/js'], function(){ 
				$('.addEditControls').each(function(){ $(this).data('ctrl', new addEditControls($(this))) })
		}) });
	</script>
{/if}

