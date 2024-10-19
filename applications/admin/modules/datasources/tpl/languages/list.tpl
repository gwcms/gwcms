{extends file="default_list.tpl"}


{block name="init"}

	{function name=do_toolbar_buttons_modact} 		
		{*
		{toolbar_button 
			title="Add system language"
			href=$m->buildUri(false, [act=>doGetFlags])}
		*}
	{/function}	
	
		
	
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print,modact]}	
	{*
	{$dlgCfg2MWdth=300}
	{$do_toolbar_buttons[] = dialogconf2}			
	*}
	
	{$do_toolbar_buttons[] = search}
	{$dl_smart_fields=[img_png,img_css]}
	
	{$dl_actions=[edit,delete]}
	


	{function dl_cell_img_png}
		<img src="{GW::s("STATIC_EXTERNAL_ASSETS")}flags/png/{$item->trcode}.png" style="height:12px;border:1px solid #eee">
	{/function}
	
	{function dl_cell_img_css}
		<img src="{GW::s("STATIC_EXTERNAL_ASSETS")}flags/oneimgcss/blank.gif" class="flag flag-{$item->trcode}"/>
		
	{/function}	
	
	
	{capture append=footer_hidden}
		<link type="text/css" href="{GW::s("STATIC_EXTERNAL_ASSETS")}/flags/oneimgcss/flags.css" rel="stylesheet" />
	{/capture}	
{/block}
