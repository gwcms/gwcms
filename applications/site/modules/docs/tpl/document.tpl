	{$body}
	
	{if $answer->signature}
		{include "{$smarty.current_dir}/digitalsignature.tpl"}
	{/if}
