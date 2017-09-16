{include file="common.tpl"}

{function name=do_toolbar_buttons_preview} 
	{toolbar_button 
		title="Peržiūrėti svetainėje" 
		iconclass='fa fa-external-link' 
		href=$app->buildUri(false,[act=>doPreview,id=>$item->id]) 
		tag_params=[target=>'_blank', title=>"Peržiūrėti svetainėje"]}
{/function}	
	
{$do_toolbar_buttons[]=preview}

{include file="default_form_open.tpl" form_width="100%"}
{$width_title=100px}

{include file="tools/lang_select.tpl"}


{include file="elements/input.tpl" name=type type=select options=$m->lang.TYPE_OPT onchange="$('#gw_input_template_id')[this.value==0?'fadeIn':'fadeOut']().size()"}


{include file="elements/input.tpl" name=parent_id type=select options=$m->getParentOpt($item->id) default=$smarty.get.pid}
{include file="elements/input.tpl" name=pathname}
{include file="elements/input.tpl" name=title}
{include file="elements/input.tpl" name=meta_description}


{include file="elements/input.tpl" name=template_id options=$lang.EMPTY_OPTION+$m->getTemplateList() type=select}

{*
{include file="elements/input.tpl" name=gallery_id type=gallery_folder title=$lang.GALLERY_FOLDER}
*}


{include file="elements/input.tpl" type=bool name=active}

{if $update}
	{include file="elements/input.tpl" type=bool name=in_menu}
	
	{$add_site_css=1}
	{$input_name_pattern="item[input_data][%s]"}
	{foreach $item->getInputs() as $input}
		{include file="elements/input.tpl" 
			name=$input->get(name) 
			type=$input->get(type) 
			note=$input->get(note) 
			title=$input->get(title) 
			value=$item->getContent($input->get(name)) 
			params_expand=$input->get(params)
			i18n=1}
	{/foreach}
	
{/if}

{include file="default_form_close.tpl" extra_fields=[id,path,insert_time,update_time]}