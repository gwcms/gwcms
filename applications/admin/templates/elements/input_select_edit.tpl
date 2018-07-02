
{*
params required:

datasource=$app->buildUri('module/submodule')  
module should have viewOptions viewForm

*}

{$GLOBALS.input_edit_select=$GLOBALS.input_edit_select+1}



{capture assign=addnew}
	
      <span class="input-group-btn">
			
			{*{$url=$app->buildUri('competitions/composers/form',[id=>0,clean=>1,'RETURN_TO'=>$m->buildUri('iframeclose')])}*}
			{**}
			{*gwcms.open_iframe({ url:'{$url}', title:'Sukurti naują kompositorių' });*}
			{*gw_dialog.open('{$url}', { width: 400 })*}
			{**}
			<script>
				
				require(['gwcms'], function(){
					
					
				jQuery.fn.extend({
				  selEditUpdate: function(id) {
							var url = this.data('url');
							
							var args = { baseadd:'/options', clean:2, dialog:1 };
							
							if(id) args[id] = id;
							
							url = gw_navigator.url(url, args)
							
							var sel = this;
							
							$.get({
								url: url,
								success: function(data){ 
									
									if(id){
										sel.find("[value='"+id+"']").text(data[id]);
									}else{
										//uzkrauti visa sarasa
										var seledtedval = sel.val();
										var isssetval = sel.selEditIsValueSet();
										
										if( isssetval )
											sel.find(':selected').remove()
										
										for(var key in data)
										{
											sel.append($("<option />").val(key).text(data[key]));
										}
										
										if( isssetval )
											sel.val(seledtedval);
																			
									}
									$('.selectpicker').selectpicker('refresh');
								},
								dataType: 'json'
							      });
				  },
				  selEditIsValueSet: function(){
					  return $(this).val() && $(this).val()!='0';
				  },
				  selEditIsActiveEdit: function(){
					  var index=$(this).data('input-index');	
						
						
					if( this.selEditIsValueSet() ){
						$('.edSelForm'+index).removeClass('disabled').prop( "disabled", false );
					}else{
						$('.edSelForm'+index).addClass('disabled').prop( "disabled", true );
					}
				  }
				});					
					
					
					$(".edSelPlus:not([data-initdone='1'])").click(function(){
						
						var index=$(this).data('input-index');	
						var sel = $('.editSelect'+index+' select');
						var url = sel.data('url')
						var id = sel.val();
						
						url = gw_navigator.url(url, { baseadd:'/form', id: 0, clean:2, dialog:1 })						
						
						var selecthappend = function(context){ 
							console.log(context)
							
							if(context.item){
								sel.append($('<option>', {
								    value: context.item.id,
								    text: context.item.title
								}));
								
								sel.val(context.item.id);
								
								$('.selectpicker').selectpicker('refresh');
							}
						}
									
					
						gwcms.open_dialog2({ url: url, iframe:1, title:'Kurti įrašą', close_callback:selecthappend })
						
						
					}).attr('data-initdone',1);

					$(".edSelForm:not([data-initdone='1'])").click(function(){
						
						
						var index=$(this).data('input-index');	
						var sel = $('.editSelect'+index+' select');
						var url = sel.data('url')
						var id = sel.val();
						
						if(isNaN(id))
						{
							url = gw_navigator.url(url, { baseadd: '/form', idkey: id, clean:2, dialog:1 })	
						}else{
							url = gw_navigator.url(url, { baseadd: '/form', id: id, clean:2, dialog:1 })	
						}
						
						
						var selecthappend = function(){ sel.selEditUpdate(id) }
						
						gwcms.open_dialog2({ url: url, iframe:1, title:'Redaguoti įrašą', close_callback:selecthappend })
						
						
					}).attr('data-initdone',1);
					
					
					$(".editSelect select:not([data-initdone='1'])").change(function(){
						$(this).selEditIsActiveEdit();
					}).each(function(){
						
						//load options if it is not present
						if($(this).data('ajaxload'))
						{
							$(this).selEditUpdate(false)
						}
						
						$(this).selEditIsActiveEdit();
						
					}).attr('data-initdone',1);
					
				})
				
				
			</script>
		  
		<button class="btn btn-default edSelPlus " data-input-index="{$GLOBALS.input_edit_select}" type="button" ><i class="fa fa-plus-circle"></i></button>
		<button class="btn btn-default edSelForm edSelForm{$GLOBALS.input_edit_select}" data-input-index="{$GLOBALS.input_edit_select}" type="button" ><i class="fa fa-pencil-square-o"></i></button>		  
      </span>
	
	
{/capture}



{$tag_params=["data-input-index"=>$GLOBALS.input_edit_select,"data-url"=>$datasource]}



{if !$options}
	
	{if !$value}
		{$value=$item->$name|default:$default}
	{/if}	
	
	{if $value}
		{$options=[$value=>'Loading...']}
	{else}
		{$options=[]}
	{/if}
	
	
	{$tag_params["data-ajaxload"]=1}
	{$tag_params["data-selected"]=$value}
{/if}

{include file="elements/input.tpl" 
	type="select"
	after_input=$addnew
	class="`$class` editSelect editSelect`$GLOBALS.input_edit_select`"
	btngroup_width="150px"
}
	
<style>
	.editSelect{  }
	.editSelect button{ height:32px; width:auto; }
</style>
