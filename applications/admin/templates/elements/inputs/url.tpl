{*
	jei taip kvieciama 
	{include file="elements/inputs/text.tpl"  placeholder=$placeholder|default:'http://'}
	tada placeholder ir kiti kintamieji kazkodel lieka/persinesa sekanciuose inputuose
*}
{include file="{$smarty.current_dir}/text.tpl" placeholder=$placeholder|default:'http://'}
