	{if $modpath}
		{$source_args = $source_args|default:[]}
		{$tmppath=explode('/', $modpath,2)}
		{if !$datasource}
			{$datasource=$app->buildUri("`$tmppath.0`/`$tmppath.1`", $source_args+[clean=>2,pick=>1])}
		{/if}
	{/if}
		
	
	<button class="btn btm-sm  textopts_dialog" data-url="{$datasource}" onclick="return false"><i class="fa fa-search"></i> {GW::l('/g/TEXT_TEMPLATES')}</button>		
		


	    
{if !GW::globals(after_input_dropdown_options2)}
	{GW::globals(after_input_dropdown_options2,1)}
	<script>
		var picker_context=false;

		function textSelected(context){
			
			gwcms.showMessages([{ type:0, 'text': JSON.stringify(context.payload), title:"Atnaujintas tekstas" }])
			
			for(var lang in context.payload ){
				console.log(lang+' ::: '+context.payload[lang]);

				
				picker_context.find('.ln_contain_'+lang+' textarea').val( context.payload[lang]  )  
			}
			
			
		}		
		
		require(['gwcms'], function(){
			$('.textopts_dialog').click(function(){
				picker_context = $($(this).parents('.input-group').get(0))
				
				gwcms.open_dialog2({ minWidth:500, minHeight:500, url: $(this).data('url'), iframe:1, title:"{GW::l('/g/TEXT_TEMPLATES')}", close_callback:{$textSelectedFunc|default:'textSelected'} })
				
			})
		})

	</script>
	    
	<style>
		.input-group { height: 32px; }
	</style>
{/if}

