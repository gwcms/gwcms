
{$addlitag=true}


		
{list_item_action_m 
		caption=GW::l('/m/VIEWS/doSendTestEmail')
		url=[false,[act=>doSendTestEmail,id=>$item->id]] 
		query_param=[mail, "Nurodykite gavėjo adresą"]
		iconclass="fa fa-fw fa-eye" 
		tag_params=[target=>'_blank', title=>"Bus siunčiamas laiškas nurodytu el. pašto adresu"]}



{*{list_item_action_m url=["`$item->id`/createduplicate", [act=>doClone, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption=GW::l('/g/VIEWS/doClone')}*}
{list_item_action_m url=["`$item->id`/form", [act=>doClone, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption=GW::l('/g/VIEWS/doClone')}

{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE') shift_button=1}
