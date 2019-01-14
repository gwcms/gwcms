{if $readonly}
	{foreach $value as $selected}
		{$options[$selected]}{if !$selected@last},{/if}
	{/foreach}
{else}
	
{$tmpopt=[]}
{foreach $value as $id}	
	{$tmpopt[$id]=$options[$id]}
{/foreach}
	
<select multiple="multiple" class="form-control gwselect2 " id="{$id}" name="{$input_name}" 
		style="width: {$width|default:"100%"}; {if $height}height:{$height};{/if}"
		>
	{html_options options=$tmpopt+$options selected=$value}
</select>

	
	
{if $sorting}
	{capture append=footer_hidden}
	<script type="text/javascript">
		
		require(['vendor/select2/js'],function(){
	
			$('#{$id}').on('gwinitselect', function(){
				

		//to prevent auto ordering after select
		$('#{$id}').on("select2:select", function (evt) {
			var element = evt.params.data.element;
			var $element = $(element);
			$element.detach();
			$(this).append($element);
			$(this).trigger("change");
		});	

		

		//allow ordering			
		$.fn.select2.amd.require([
			'select2/utils',
			'select2/dropdown',
			'select2/dropdown/attachBody'
		], function (Utils, Dropdown, AttachBody) {
			
			
			
			var select = $('#{$id}');
			var container = select.select2().data('select2').$container;
		

			container.find('ul').sortable({
			containment: 'parent',
			update: function(event, ui) { 				
				$(ui.item[0]).parent().find('.select2-selection__choice').each(function(){ 
					var elm = Utils.__cache[$(this).data('select2Id')];
					var element = elm.data.element;
					var $element = $(element);
					$element.detach();
					select.append($element);
					select.trigger("change");
				})
				}
			});
		})




			})
		})




	</script>
	{/capture}	
	
{/if}	
	
	
	
	
	
{if !$gwcms_input_select2_loaded}
	{$m->addIncludes("bs/select2css", 'css', "`$app_root`static/vendor/select2/css.css")}
	
	{capture append=footer_hidden}
	<script type="text/javascript">
		require(['vendor/select2/js'], function(){ 		
          $(".gwselect2").select2();
		  $(".gwselect2").trigger('gwinitselect')
		});
	</script>
	{/capture}
	
	{assign var=gwcms_input_select2_loaded value=1 scope=global}	
{/if}
	

{/if}