

{$addlitag=true}


{if $item->user_id}
	{list_item_action_m url=[false,[act=>doSyncWithLinked,id=>$item->id]] iconclass="fa fa-upload" confirm=1 caption=GW::l('/m/VIEWS/doSyncWithLinked')}
{/if}


{if $item->doc_id}
	{list_item_action_m href="/{$item->ln}/direct/docs/docs/document?id={$item->doc->key}&answerid={$item->id}&s=preview" iconclass="fa fa-globe text-mint" caption="Suformuotas dokumentas"}
	{list_item_action_m href="/{$item->ln}/direct/docs/docs/document?id={$item->doc->key}&answerid={$item->id}&act=doExportAsPdf" iconclass="fa fa-file-pdf-o" caption="Suformuotas dokumentas (pdf)"}
	
	{list_item_action_m url=[false, [act=>doCopyAnswerToOtherDoc, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption="Dubliuoti atsakymą į kitą dokumentą"}	
{/if}

	

<li class="divider"></li>

{dl_actions_clone}
{dl_actions_delete}
		


{if $m->feat(itax)}
	{list_item_action_m url=[false,[act=>doItaxSyncPurchase,id=>$item->id]] iconclass="fa fa-cloud-upload"  caption=GW::l('/m/VIEWS/doItaxSyncPurchase') shift_button=1}
{/if}