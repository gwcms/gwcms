{*
{include file="common.tpl"}

{function name=do_toolbar_buttons_preview} 
	{toolbar_button 
		title=GW::l('/m/VIEWS/doPreview') 
		iconclass='fa fa-external-link' 
		href=$app->buildUri(false,[act=>doPreview,id=>$item->id]) 
		tag_params=[target=>'_blank']}
{/function}	
	
{$do_toolbar_buttons[]=preview}
*}

{include file="default_form_open.tpl" form_width="100%"}


<script>

	require(['gwcms'], function(){
			
			$('#item__type__').change(function(){
					
						
				if($(this).val()==2) {
					$('#gw_input_item__link__').fadeIn();
				}else{
					$('#gw_input_item__link__').hide();
				}
				
				if($(this).val()==0) {
					$('#gw_input_item__template_id__').fadeIn();
				}else{
					$('#gw_input_item__template_id__').hide();
				}				
			}).change();
	})	
	
</script>	
	
	
	
{$width_title=100px}

{include file="tools/lang_select.tpl"}



{call e field=type type=select options=$m->lang.TYPE_OPT}


{call e field=parent_id type=select options=$m->getParentOpt($item->id) default=$smarty.get.pid}
{call e field=pathname}
{call e field=title}
{call e field=meta_description}


{call e field=template_id options=$lang.EMPTY_OPTION+$m->getTemplateList() type=select}
{call e field=link}

{*
{call e field=gallery_id type=gallery_folder title=$lang.GALLERY_FOLDER}
*}


{call e field=active type=bool}

{if $update}
	{call e field=in_menu type=bool}
	
	{$add_site_css=1}
	{$input_name_pattern="item[input_data][%s]"}
	{$ck_set='medium'}
	{foreach $item->getInputs() as $input}
		{call e field=$input->get(name) 
			type=$input->get(type) 
			note=$input->get(note) 
			title=$input->get(title) 
			value=$item->getContent($input->get(name)) 
			params_expand=$input->get(params)
			i18n=1}
	{/foreach}
{/if}




{function name=df_submit_button_preview}
	{if $item->id}
		<a target="_blank"
			class="btn btn-default pull-right"  
			onclick="if(event.shiftKey || event.ctrlKey){ window.open(gw_navigator.url(this.href,{ 'shift_key':1 }), '_blank');return false }"
			href="{$app->buildUri(false,[act=>doPreview,id=>$item->id]) }" 
			style="margin-left:2px;" title="{GW::l('/m/PREVIEW_SHIFTKEY')}"><i class="fa fa-external-link"></i> {GW::l('/m/VIEWS/doPreview') }</a>
	{/if}
{/function}

{$submit_buttons=[save,apply,preview,cancel]}

{include file="default_form_close.tpl" extra_fields=[id,path,unique_pathid,insert_time,update_time]}