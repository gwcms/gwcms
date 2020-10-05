
		<!-- FOOTER BLOCKS -->
		{foreach $footer_hidden as $block}
			{$block}
		{/foreach}
		<!-- FOOTER BLOCKS -->

		
	{if $m->includes}
		<!-- SYS INCLUDES -->
		{foreach $m->includes as $include}
			{if $include.0=='js'}
				<script type="text/javascript" src="{$include.1}"></script>
			{elseif $include.0=='css'}
				
				{if Navigator::isAjaxRequest()}
					<script>
						$(function(){
							$( document.createElement('link') ).attr({
								href: '{$include.1}',
								type: 'text/css',
								rel: 'stylesheet'
							}).appendTo('head');						
					    });
					</script>
				{else}
					<link rel="stylesheet" type="text/css" href="{$include.1}" />
				{/if}
				
				
				
			{elseif $include.0=='jsstring'}			
				<script type="text/javascript">{$include.1}</script>
			{/if}
		{/foreach}
		<!-- /SYS INCLUDES -->
	{/if}