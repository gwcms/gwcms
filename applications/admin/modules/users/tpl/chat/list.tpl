{extends file="default_list.tpl"}

{block name="init"}

	{function dl_cell_title}
		<a href="{$m->roomLink($item->id)}">{$m->getRoomDisplayTitle($item)|escape}</a>
	{/function}

	{function dl_cell_type}
		{$item->type|escape}
	{/function}

	{function dl_cell_member_usernames}
		{$meta = $m->getRoomListMeta($item->id)}
		{foreach $meta.members as $member}
			<a class="iframeopen" href="{$app->buildUri("users/usr/`$member->id`/form",[clean=>2])}">{$member->username|default:$member->title}</a>{if !$member@last}, {/if}
		{/foreach}
	{/function}
	{$dl_actions=[edit,delete]}
	{$dl_smart_fields=[title,member_usernames,type]}
	{$dl_output_filters=[insert_time=>short_time,last_message_time=>short_time,last_event_time=>short_time]}

	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden[] = dialogconf}
	
	{if $app->user->isRoot()}
		{$do_toolbar_buttons_hidden[] = dialogconf2}
		{$do_toolbar_buttons_hidden[] = live_chat_protocol}
	{/if}
	{$do_toolbar_buttons[] = search}

	{function do_toolbar_buttons_live_chat_protocol}
		{toolbar_button href=$m->buildUri(testlivechatprotocol) title="Live Chat Protocol" iconclass="fa fa-plug"}
	{/function}

{/block}
