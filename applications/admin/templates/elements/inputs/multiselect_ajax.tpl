{*this is workaroud to add [] to end of name for example multiple input name=group_ids[] single: name=group_id *}

{if !$maximumSelectionLength}
	{$maximumSelectionLength=999}
{/if}

{include file="elements/inputs/select_ajax.tpl"}