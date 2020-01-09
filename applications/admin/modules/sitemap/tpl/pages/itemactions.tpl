{$addlitag=1}


{list_item_action_m url=["`$item->id`/form", [act=>doClone, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption=GW::l('/g/VIEWS/doClone')}
{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE') shift_button=1}
{list_item_action_m 
	url=[false,[act=>doPreview,id=>$item->id]] 
	iconclass="fa fa-external-link" 
	tag_params=[target=>'_blank'] caption=GW::l('/m/VIEWS/doPreview')}


	
{list_item_action_m url=[false,[act=>doExportTree,id=>$item->id]] iconclass="fa fa fa-upload" caption=GW::l('/g/VIEWS/export') shift_button=1}





