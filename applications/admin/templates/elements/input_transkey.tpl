

{capture assign=addnew}
	
      <span class="input-group-btn">
			{$url=$app->buildUri('datasources/translations/form',[clean=>2,dialog=>1])}

			<script>
				function searchTrans(url)
				{					
					gwcms.open_dialog2({ url: url, iframe:1, title:'{GW::l('/M/datasources/SEARCH_TRANSKEY')}', close_callback:transkeySelected })
				}
				
				function transkeySelected(context)
				{
					if(context.item)
					{
						var item = context.item
						var autocompleteInput = $('#itemform select[name="item[{$name}]"]');						
						autocompleteInput.html("")
						
						autocompleteInput.select2("trigger", "select", { data: { id: item.title, text: item.title } });					
					}
				}
				
				
				function editTransKey(url)
				{	
					var key = $('#itemform select[name="item[{$name}]"]').val();	
					
					url = gw_navigator.url(url, { key: key })
					gwcms.open_dialog2({ url: url, iframe:1, title:'{GW::l('/M/datasources/EDIT_TRANSKEY')}', close_callback:transkeySelected })
				}
				
				function transkeyChanged(idchanged)
				{
						if(idchanged){
							//gwcms.showMessages([{ type:0, 'text':'Translation key reset', title:"Translation key changed" }])
							
						}else{
							//gwcms.showMessages([{ type:0, title:"Translation key updated" }])
						}
				}
				
				function resetTransKey()
				{
						var autocompleteInput = $('#itemform select[name="item[{$name}]"]');
						autocompleteInput.html("")
						autocompleteInput.val("").trigger("change"); 
				}					
				
				
				
			</script>
		  
		<button class="btn btn-default" type="button" onclick="searchTrans('{$url}')"><i class="fa fa-plus-circle"></i></button>
		<button class="btn btn-default" type="button" onclick="editTransKey('{$url}')"><i class="fa fa-pencil-square-o"></i></button>		  
      </span>
	
	
{/capture}

{include file="elements/input.tpl" 
	type=select_ajax 
	maximumSelectionLength=1
	options=[]
	preload=1
	onchangeFunc="transkeyChanged"
	datasource=$app->buildUri('datasources/translations/keysearch') 
	after_input=$addnew
}
	
