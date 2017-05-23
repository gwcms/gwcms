{extends file="default_list.tpl"}


{block name="init"}


	{capture append="footer_hidden"}
		<style>
			.gwroot_node{ color: silver}
			.gwroot_node:first-child{
				padding: 5px;
			}

			body.dragging, body.dragging * {
				cursor: move !important;
			}

			.dragged {
				position: absolute;
				opacity: 0.5;
				z-index: 2000;
			}

			.gwlisttable > tbody > tr {
				cursor: move !important;
			}

			.gwlisttable tr.placeholder {
				display: block;
				background: red;
				position: relative;
				margin: 0;
				padding: 0;
				border: none; }
			/* line 103, /Users/jonasvonandrian/jquery-sortable/source/css/application.css.sass */
			.gwlisttable tr.placeholder:before {
				content: "";
				position: absolute;
				width: 0;
				height: 0;
				border: 5px solid transparent;
				border-left-color: red;
				margin-top: -5px;
				left: -5px;
				border-right: none; }
			.gw_separator {
				background-color: #ffc;
			}


		</style>
		<script>
			var action_url = "{$m->buildUri(false,['act'=>'doSavePositions'])}";
			var sortabledata;
			
			function saveSortings()
			{
				var tmp = JSON.stringify(sortabledata[0], null, ' ');
				
				$.post(action_url, { 'positions':tmp }, function(data){ 

					gwcms.showMessage(data, 0, 500);
					setTimeout("$('#updated_box').fadeOut();",3000);
				})	
			}
			
			
		require(['sortable'],function(){
			
			$(function () {
				
					var sortabegroup=$('.gwListTable').sortable({
						containerSelector: 'table',
						itemPath: '> tbody',
						itemSelector: 'tr',
						placeholder: '<tr class="placeholder"><td colspan="99" style="background-color:yellow">&nbsp;</td></tr>',

						onDrop: function ($item, container, _super) {

						  sortabledata = sortabegroup.sortable("serialize").get();

						  saveSortings();
						 _super($item, container);
						}					
					});		
			})			
			
			

				// Sortable column heads


		});		
		

		</script>
	{/capture}

	{$dl_actions=[]}
	
	
	{function name=do_toolbar_buttons_addseparator}
		{toolbar_button 
			title={GW::l('/m/VIEWS/addseparator')} 
			iconclass='gwico-Upload-SVG' 
			onclick="var ss=window.prompt('Section title');if(ss)location.href='`$m->buildUri('rearange',['act'=>'doAddSeparator','title'=>''])`'+ss;"}
	{/function}	
	
	{$do_toolbar_buttons=[addseparator]}	
	{$dl_fields=[icon,title,path,actions]}
	{$dl_smart_fields=[icon,path,title,actions]}
	
	{function dl_cell_icon}
		<center>{$item->info.icon}</center>
	{/function}
	{function dl_cell_title}
		{if $item->path!='separator'}
			{$item->title}
		{else}
			<i>{$item->title}</i>
		{/if}
	{/function}	
	{function dl_cell_path}
		{if $item->path!='separator'}
			{$item->path}
		{/if}
	{/function}
		
	
	{function name=dl_prepare_item}
		{if $item->path=='separator'}
			{$item->set('row_class', 'gw_separator')}
		{elseif !$item->active}
			{$item->set('row_class', 'gw_notactive')}
		{/if}
	{/function}			

	{function name=dl_cell_actions}
		
		{if $item->path=="separator"}
			{$dl_actions=[edit,delete]}
		{else}
			{$dl_actions=[invert_active]}
		{/if}	
		
		{call dl_display_actions}
		
	{/function}	

	
	{$dl_action_return_to=$app->path}
{/block}

