{$addlitag=1}

	{list_item_action_m url=[false,[id=>$item->id,act=>doLoginAs,'redirect_url'=>'']] 
		tag_params=[target=>'_blank'] iconclass="fa fa-sign-in" caption="Login to site as `$item->title`"}

	{list_item_action_m url=[false,[id=>$item->id,act=>doJoinAccounts]] 
		iconclass="fa fa-cog" caption="Sujungti paskyras"}



{if $item->removed}
	{list_item_action_m url=[false,[id=>$item->id,act=>doRecover]] 
		iconclass="fa fa-cog" caption="Atstatyti pašalintą paskyra"}
{/if}



{list_item_action_m url=[false,[id=>$item->id,act=>doSetUpLicense]] 
		tag_params=[target=>'_blank'] iconclass="fa fa-sign-in" caption="auto set licence number"}
		

{if $app->user->isRoot()}
	{dl_actions_clone}
{/if}