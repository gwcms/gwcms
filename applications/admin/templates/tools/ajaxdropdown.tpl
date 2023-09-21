
<div class="btn-group dropright gwcmsAction" style="display: inline">

	<i class="fa fa-bars dropdown-toggle dropdown-toggle-icon gwcms-ajax-dd" data-toggle="dropdown" data-url="{$item.actions}"></i>	
	    <ul class="dropdown-menu dropdown-menu-right">
		<li><i class="fa fa-spinner fa-pulse"></i></li>
	    </ul>
</div>									    
						
{if !isset($GLOBALS.dropdown_init_done)}
	{$GLOBALS.dropdown_init_done=1}
	<script type="text/javascript">
		require(['gwcms'], function(){
		gwcms.initDropdowns();
	});
	</script>
{/if}