{include file="inputs/input_text.tpl"}

			
{if !$input_autocomplete_loaded}
	{*
	<link href="{$app->sys_base}/vendor/bootstrap-ajax-typeahead/demo/assets/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" />
	*}
	<script src="{$app->sys_base}vendor/bootstrap-ajax-typeahead/js/bootstrap-typeahead.js" type="text/javascript"></script>


	{assign scope=global var=input_autocomplete_loaded value=1}
{/if}			
			
<script type="text/javascript">
		

	//metodas pritaikytas kad galetu pakeitus data-source atributa reinicilizuoti
	$('#{$id}').on('reinit', function(event, firstrun){
		
		
			var obj=$(this)
			
			
			obj.typeahead('destroy')
			
			if(!obj.attr('data-source'))
				return false;
			
			console.log('reinit ' +this.className+'; data-source:'+obj.attr('data-source'));
				
				
			obj.typeahead({
				ajax: { 
					url: obj.attr('data-source'),
					triggerLength: 1				    

					  },
				scrollBar:true,
				displayField: 'title',
				val: 'id',	
				onSelect: function(item){  
					obj.val(item.text);
					obj.attr('last-selected-id', item.value );
					obj.attr('last-selected-title', item.text);
					
					obj.trigger( "object:selected", [item.value, item.text]);
			
					console.log(item);
				}
		});
		
		if(obj.attr('selected-id'))
		{
			$(this).addClass('autocomplete_object_selected')
		}
		
	}).keyup(function(){
		if(this.value==$(this).attr('last-selected-title') && $(this).attr('last-selected-id')){
			$(this).trigger( "object:selected", [$(this).attr('last-selected-id'), $(this).attr('last-selected-title')]);
			
		}else{
			$(this).trigger( "object:deselected");
			
		}
		
	}).on( "object:selected", function( event, id, title) {
		//$(this).
		$(this).addClass('autocomplete_object_selected')
		$(this).data('recenty-deselected', false)
		
		$(this).attr('selected-id', id );

	}).on( "object:deselected", function( event ) {
		//$(this).
		$(this).removeClass('autocomplete_object_selected')
		$(this).data('recenty-deselected', true)
		$(this).attr('selected-id', false );
		
	}).change(function(){


		var obj = $(this);
		
		//console.log({ "recently delelected": $(this).data('recenty-deselected'), 'previuos id': $(this).attr('last-selected-id') })
		/*
		if(obj.data('recenty-deselected') && 
			obj.attr('last-selected-id') && 
			obj.val() != obj.attr('last-selected-title') &&
			!confirm("You had selected previously: \""+obj.attr('last-selected-title')+"\". Are you sure you want deselect it?")
			
		)
		{
			obj.val(obj.attr('last-selected-title'));
			obj.keyup();
			console.log('SELECTED BACK');
		}*/
		
		obj.data('recenty-deselected', false)
	}).trigger("reinit",[true]);
	

</script>