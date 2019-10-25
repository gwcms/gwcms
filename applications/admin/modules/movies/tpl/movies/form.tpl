{include file="default_form_open.tpl" form_width="100%"}
{$width_title=100px}


{call e field=title}

{call e field="mdbid"
	type=select_ajax 
	maximumSelectionLength=1
	options=[]
	datasource=$m->buildUri(false,[act=>doSearchMovies]) 
}



{call e field=description  type=textarea autoresize=1 height=50px}
{call e field=rate type=radio options=[0,1,2,3,4,5,6,7,8,9,10] separator='&nbsp;&nbsp;&nbsp;'}

{if $item->id}
	{call e field=recommend}

	{call e field=image1 type=image}
	{call e field=name_orig}
	{call e field=imdb type=code_json height=200px nopading=1 hidden_note="Clean area, and it will be updated"}  
{/if}

{include file="default_form_close.tpl"}