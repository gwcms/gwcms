{extends file="default_list.tpl"}


{block name="init"}

	{function name=dl_cell_title}
		{$item->title} / {$item->info->model}
	{/function}
	
		
	{$dl_fields=[path,title]}
	{$dl_smart_fields=[title]}
	{$do_toolbar_buttons=[info]}	
	
	
	{function name=dl_actions_addlang}
		
		{foreach GW::$settings.LANGS as $lang}
			{list_item_action_m url=[false,[id=>$item->id,act=>doAddLang,model=>$item->info->model,modlang=>$lang]] caption="+`$lang`"}
		{/foreach}
		
		{foreach GW::$settings.LANGS as $lang}
			{list_item_action_m url=[false,[id=>$item->id,act=>doDropLang,model=>$item->info->model,modlang=>$lang]] caption="-`$lang`"}
		{/foreach}		
		
		
	{/function}
	
	
	{$dl_actions=[addlang]}
	
{/block}