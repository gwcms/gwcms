
{call e field="service_url_list"
	type="textarea"	
	note="apply needed before setting new to main_service_url"
}

{$serv_url_opts = explode(',',$item->service_url_list)}
{$tmp =[]}
{foreach $serv_url_opts as $url}
	{$tmp[$url]=$url}
{/foreach}

{call e field="main_service_url" type="select"	options=$tmp empty_option=1}