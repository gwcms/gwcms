{$addlitag=1}


{if $app->user->isRoot()}
		{list_item_action_m 
			url=[false,[act=>doMarkAsPayd,id=>$item->id]] 
			iconclass="fa fa-legal text-danger" 
			caption="(Root only) Test actions after pay - please verify if this is test order"

		}
{/if}

