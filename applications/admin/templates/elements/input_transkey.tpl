{capture assign=addnew}
      <span class="input-group-btn transkeyControls" id="transkeyControls{$name}" data-name="{$name}">
		<button class="btn btn-default searchBnt"  type="button" ><i class="fa fa-plus-circle"></i></button>
		<button class="btn btn-default editBnt" type="button"><i class="fa fa-pencil-square-o"></i></button>		  
      </span>
{/capture}


{if !isset($GLOBALS.init_transkey_input_done)}		
		{$GLOBALS.init_transkey_input_done=1}
		<script type="text/javascript">
			require(['gwcms'], function(){
				var transkey_url='{$app->buildUri('datasources/translations/form',[clean=>2,dialog=>1])}';
				var search_transkey = '{GW::l('/M/datasources/SEARCH_TRANSKEY')}';
				var edit_transkey = '{GW::l('/M/datasources/EDIT_TRANSKEY')}';
			
				function TransKeyControls(obj)
				{
					this.name = obj.data('name');					
					this.searchBtn = obj.find('.searchBnt');
					this.editBnt = obj.find('.editBnt');
					this.inputobj = $('#itemform select[name="item['+this.name+']"]');
					var transkeyobj = this;
					
					
					this.selected = function (context)
					{
						console.log(context)
						if(context.item)
						{
							var item = context.item
							
							this.transkeyobj.inputobj.html("")
							this.transkeyobj.inputobj.select2("trigger", "select", { data: { id: item.title, text: item.title } });					
						}
					}
					
					this.searchBtn.click(function(){
						gwcms.open_dialog2({ url: transkey_url, iframe:1, title: search_transkey, close_callback: this.selected })
					})
					
					this.editBnt.click(function(){						
						var key = transkeyobj.inputobj.val();		
						if(!key)
							return false;
					
						var url = gw_navigator.url(transkey_url, { key: key })
						gwcms.open_dialog2({ url: url, iframe:1, title:edit_transkey, close_callback:this.selected })
					})
					
					this.resetTransKey = function()
					{
						this.transkeyobj.inputobj.html("")
						this.transkeyobj.inputobj.val("").trigger("change"); 
					}
					
					obj.on('chageevent', function(){ console.log('change'); })
					
										
				}
				
				$('.transkeyControls').each(function(){ $(this).data('transkeyobj', new TransKeyControls($(this))) })
			
		});
		</script>
{/if}	

<script>
	function transkey{$name}changed(){ $('#transkeyControls{$name}').trigger('chageevent');  }
</script>

{include file="elements/input.tpl" 
	type=select_ajax 
	maximumSelectionLength=1
	options=[]
	preload=1
	onchangeFunc="transkey`$name`changed"
	datasource=$app->buildUri('datasources/translations/keysearch') 
	after_input=$addnew
}
	
