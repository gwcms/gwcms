{include file="default_form_open.tpl" changes_track=1 action="saveConfig"}

{include "`$m->tpl_dir`/config.tpl"}


{if $smarty.get.dialog_iframe}
	{$submit_buttons=[save,cancel]}
{else}
	{$submit_buttons=[save,apply,cancel]}
{/if}


{include file="default_form_close.tpl"}