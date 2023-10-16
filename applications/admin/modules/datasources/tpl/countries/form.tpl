{include file="default_form_open.tpl"}

{call e field=code note="<a target='_blank' href='https://www.nationsonline.org/oneworld/country_code_list.htm'>A2 (ISO)</a>"}

{foreach GW::$settings.LANGS as $lncode}
	{call e field="title_$lncode" type=textarea height="50px"}
{/foreach}	

{foreach $app->i18next as $lncode => $x}
	{call e field="title_$lncode" type=textarea height="50px"}
{/foreach}	



{call e field=aka type=tags}


{$cols=$item->getColumns()}

{if isset($cols.fake)}
	{call e field=fake type=bool hidden_note="if country is not real please tick this box"}
{/if}

{include file="default_form_close.tpl"}