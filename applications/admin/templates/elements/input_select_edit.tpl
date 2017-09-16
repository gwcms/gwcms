
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
							url = this.data('url');
							
							url = gw_navigator.url(url+'/options', { id: id, clean:2, dialog:1 })
							
							var sel = this;
							
							$.get({
								url: url,
								success: function(data){ 
									sel.find("[value='"+id+"']").text(data[id]);
									$('.selectpicker').selectpicker('refresh');
								},
								dataType: 'json'
							      });
				  },
				});					
					
					
					$(".edSelPlus:not([data-initdone='1'])").click(function(){
						
						var index=$(this).data('input-index');	
						var sel = $('.editSelect'+index+' select');
						var url = sel.data('url')
						var id = sel.val();
						
						url = gw_navigator.url(url+'/form', { id: 0, clean:2, dialog:1 })						
						
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
						
						url = gw_navigator.url(url+'/'+id+'/form', { id: id, clean:2, dialog:1 })
						
						var selecthappend = function(){ sel.selEditUpdate(id) }
						
						gwcms.open_dialog2({ url: url, iframe:1, title:'Redaguoti įrašą', close_callback:selecthappend })
						
						
					}).attr('data-initdone',1);
					
					
					$(".editSelect select:not([data-initdone='1'])").change(function(){

						
						var index=$(this).data('input-index');	
						
						
						if(!(this.value-0)){
							$('.edSelForm'+index).addClass('disabled').prop( "disabled", true );
						}else{
							$('.edSelForm'+index).removeClass('disabled').prop( "disabled", false );
						}
						
						
						
					}).attr('data-initdone',1);
					
				})
				
				
			</script>
		  
		<button class="btn btn-default edSelPlus " data-input-index="{$GLOBALS.input_edit_select}" type="button" ><i class="fa fa-plus-circle"></i></button>
		<button class="btn btn-default edSelForm edSelForm{$GLOBALS.input_edit_select}" data-input-index="{$GLOBALS.input_edit_select}" type="button" ><i class="fa fa-pencil-square-o"></i></button>		  
      </span>
	
	
{/capture}


{$tag_params=["data-input-index"=>$GLOBALS.input_edit_select,"data-url"=>$datasource]}

{include file="elements/input.tpl" 
	type="select_plain"
	after_input=$addnew
	class="`$class` editSelect editSelect`$GLOBALS.input_edit_select`"
	btngroup_width="150px"
}
	
<style>
	.editSelect{  }
	.editSelect button{ height:32px; width:auto; }
</style>
