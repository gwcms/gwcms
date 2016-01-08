{extends file="default_list.tpl"}


{block name="init"}

	
	{$display_fields=[
orderid=>1,handler=>1,action=>1,paytext=>1,p_firstname=>1,p_lastname=>1,p_email=>1,amount=>1,
currency=>1,country=>1,test=>1,payment=>1,m_pay_restored=>1,status=>1,requestid=>1,payamount=>1,paycurrency=>1,
type=>1,handler_state=>1,insert_time=>1,update_time=>1]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	{$dl_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[edit,delete]}
	
	{$dl_filters=$display_fields}
	
	
	{gw_unassign var=$display_fields.image} 	
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}