<br />
			
			
{foreach $options as $key => $opttitle}
	<div class="form-check form-check-inline mb-0">
	<label class="form-check-label mr-2">
		<input type="radio" name="{$input_name}" value="{$key|escape}" 
		       {if $value==$key}checked="checked"{/if} 
		       {if $readonly}readonly disabled{/if}
		       {if $required}required="1"{/if} 
		       class="form-check-input mr-1 {if $required}required{/if} "
		       > {$opttitle}
	</label>
	</div>
	{if $newline}<br>{/if}
	{if $separator}{$separator}{/if}
{/foreach}



{if $onchangeFunc || $stateToggle}
	{capture append=footer_hidden}
	<script type="text/javascript">
		//$(function(){
			$('input[type=radio][name="{$input_name}"]').change(function() {
				{if $onchangeFunc}{$onchangeFunc}(this.value, this);{/if}
				{if $stateToggle}
					var $hidden = $('.{$stateToggle}').parents('.inputContainer').hide();
					var val = $('input[name="'+this.name+'"]:checked').val();
					//$hidden.hide();
										
					
					if(val){
						
						
						var $shown = $('.{$stateToggle}_' + val).parents('.inputContainer');
						$shown.show();

						$shown.find('select.select2-hidden-accessible').each(function () {
							$(this).next('.select2-container').css('width', '100%');
						});
					}

					
				{/if}		
			}).first().change();
			
		//})

	</script>
	{/capture}

{/if}		
