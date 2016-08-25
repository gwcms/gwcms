{extends file="default_list.tpl"}


{block name="init"}

	{function name=dl_actions_halt}
			{gw_link do="haltProc" params=[id=>$item->id] title="sigterm"}
			{gw_link do="haltProc" params=[id=>$item->id,sigkill=>1] title="SIGKILL!"}
	{/function}

	{$fields=[
		id=>1,
		cmd=>1
	]}
	
	{$dl_fields=array_keys($fields)}
	{$do_toolbar_buttons=[]}
	
	{$dl_actions=[halt]}
	
	{*auto reload*}
	<script type="text/javascript">
		setInterval("location.href=location.href", 30000);
	</script>
	
{/block}