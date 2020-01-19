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
	
<style>
	#gw_input_item__title__ .ln_contain_3{ width: {round(100/count(GW::$settings.LANGS),2)-0.3}% }
	
</style>	

{$width_title=100px}



{call e field=type type=select options=$m->lang.TYPE_OPT}


{call e field=parent_id type=select options=$m->getParentOpt($item->id) default=$smarty.get.pid}
{call e field=pathname}



{call e field=title i18n=3 i18n_expand=1}
{call e field=meta_description }


{call e field=template_id options=$lang.EMPTY_OPTION+$m->getTemplateList() type=select}
{call e field=link}

{*
{call e field=gallery_id type=gallery_folder title=$lang.GALLERY_FOLDER}
*}


{call e field=active type=bool}

{if GW::s('MULTISITE')}
	{call e field=multisite type=bool}	 
{/if}

{$tpl = $item->getTemplate()}

{if $update}
	{call e field=in_menu type=bool  i18n=3 i18n_expand=1}
	
	{$add_site_css=1}
	{$input_name_pattern="item[input_data][%s]"}
	{$ck_set='medium'}
	{foreach $item->getInputs() as $input}
		{$if18n=$input->get(multilang)}
		{if $if18n} {$if18n=4} {else} {$if18n=0} {/if}
		
		{$opts=[]}
		{if strpos($input->get('type'),'select_ajax')!==false}
			{$opts.preload=1}
			{$opts.options=[]}
			
			{if $tpl}
				{$opts.modpath=$input->get('path')}		
			{/if}
		{/if}

		{$opts=array_merge($opts,(array)$input->get(params))}
		{$tmpval=$item->getContent($input->get('name'))}
		
		{if strpos($input->get('type'),'multiselect_ajax')!==false}
			{$valgetf=getContentJsonDecode}
		{else}
			{$valgetf=getContent}
		{/if}
		
		{call e field=$input->get(name) 
			type=$input->get(type) 
			note=$input->get(note) 
			title=$input->get(title) 
			params_expand=$opts
			valget_func=$valgetf
			i18n=$if18n}
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

{function name=df_submit_button_tplvarsedit}
	{if $item->id}
		<a target="_blank"
			class="btn btn-default pull-right" 
			target="_blank"
			href="{$app->buildUri("sitemap/templates/{$item->template_id}/tplvars")}" 
			title="{GW::l('/MAP/childs/templates/childs/tplvars/title')}"
			style="margin-right:2px;"><i class="fa fa-object-ungroup"></i> </a>
	{/if}
{/function}

{$submit_buttons=[save,apply,preview,cancel]}
{if $item->template_id && $app->user->isRoot()}
	{$submit_buttons[]=tplvarsedit}
{/if}

{include file="default_form_close.tpl" extra_fields=[id,path,unique_pathid,insert_time,update_time]}