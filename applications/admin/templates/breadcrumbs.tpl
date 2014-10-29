
{$list=$app->path_arr}
{foreach $list as $i => $val}
	{$list[$i].noln=1}
{/foreach}

{if is_array($breadcrumbs_attach)}
	{$list=array_merge($list, $breadcrumbs_attach)}
{/if}

{if count($list)}
	<div id="breadcrumbs">
	
	{foreach $list as $path}
	
		{if $path.title}
			{$title=$path.title}
		{else}
			{$item=GW::getInstance('GW_ADM_Page')->getByPath($path.path)}
			{if $item}
				{$title=$item->title}
				
				{$do=$item->getDataObject()}
				{if $do}
					{$dot=$do->title|default:$item->data_object_id}
				{/if}

				{if $dot}
					{$title="`$title` (`$dot`)"}
				{/if}
				
			{else}
				{$title=$app->fh()->viewTitle($path.name)}
			{/if}
		{/if}
		
		{if !$title}
			{$title=$path.name}
		{/if}
	
			
		{if $path@last}
			{$title}
		{else}
			<a href="{if $path.noln}{$ln}/{/if}{$path.path}">{$title}</a> &raquo;
		{/if}	
	
	{/foreach}
	</div>
{/if}

