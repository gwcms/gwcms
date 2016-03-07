{extends file="default_list.tpl"}


{block name="init"}

	{function name=dl_cell_title}
		{$item->title} / {$item->info.model}
	{/function}
	
		
	{$dl_fields=[path,title]}
	{$dl_smart_fields=[title]}
	{$dl_toolbar_buttons=[info]}	
	
	
	{function name=dl_actions_addlang}
		
		{foreach GW::$settings.LANGS as $lang}
			{gw_link do="addLang" params=[model=>$item->info.model,modlang=>$lang] title="+`$lang`"}
		{/foreach}
		
		{foreach GW::$settings.LANGS as $lang}
			{gw_link do="dropLang" params=[model=>$item->info.model,modlang=>$lang] title="-`$lang`"}
		{/foreach}		
		
		
	{/function}
	
	
	{$dl_actions=[addlang]}
	
{/block}