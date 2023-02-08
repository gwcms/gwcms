	{if $options_fix}
		{$tmp=[]}
		{foreach $options as $opt}
			{$tmp[$opt]=$opt}
		{/foreach}
		{$options=$tmp}
	{/if}
	
      <span class="input-group-btn">	

		
	<div class="btn-group">
	    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
	     <span class="caret"></span></button>
	    <ul class="dropdown-menu" role="menu">
		    {foreach $options as $val => $title}
			<li><a class='dropdown-option' data-value='{$val}'>{$title}</a></li>
		{/foreach}
	    </ul>
	  </div>		
		
      </span>
	    
{if !GW::globals(after_input_dropdown_options)}
	{GW::globals(after_input_dropdown_options,1)}
	<script>
		require(['gwcms'], function(){
			$('.dropdown-option').click(function(){
				$($(this).parents('.input-group').get(0)).find('input').val( $(this).data('value') )  
			})
		})

	</script>
	    
	<style>
		.input-group { height: 32px; }
	</style>
{/if}

