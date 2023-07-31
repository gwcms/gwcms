
{if !$readonly}
	&nbsp;
	{$input_name_del=sprintf($input_name_pattern,"delete_composite")}

	
	
	<span class='trash-group'>
		<i class="fa fa-trash-o compositeRemoveTrigger link" title="{GW::l('/g/REMOVE')} {$title}"></i>
	
		<input style="display:none" type="checkbox" name="item[delete_composite][{$name}][]" value="{$id}"> 
	</span>
	
	
	<script>
			require(['gwcms'], function(){
				$(".compositeRemoveTrigger:not([data-initdone='1'])").click(function() {
					$(this).toggleClass('fa-trash-o').toggleClass('fa-trash').toggleClass('text-danger');
					$(this).parents('.trash-group').find('input').prop('checked', $(this).hasClass('fa-trash'));
					
				}).attr('data-initdone',1);	
			})
	</script>
{/if}
