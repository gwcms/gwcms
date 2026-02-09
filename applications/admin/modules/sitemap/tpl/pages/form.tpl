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

{$input_tabs=[
	base => [brown,1],
	templatevars=> [green,1]
]}	

{if $additfields}
	{$input_tabs.extended=[darkviolet,0]}
{/if}



{*isplestiniai laukai*}
{$fields_config=[cols=>1,fields=>[]]}
{$m->addDynamicFieldsConfig($fields_config, $item)}


{foreach $fields_config.fields as $field}
	{if $field.tabs}
		{foreach $field.tabs as $tabid}
			{$input_tabs[$tabid]=[false,0]}
		{/foreach}
	{/if}
{/foreach}

{include file="default_form_open.tpl" form_width="100%"}




<script>

	require(['gwcms'], function(){
			
			$('#item__type__').change(function(){
					
						
				if($(this).val()==2 || $(this).val()==4) {
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



{call e field=type type=select options=GW::l('/m/TYPE_OPT')  tabs=[base]}


{call e field=parent_id type=select options=$m->getParentOpt($item->id) default=$smarty.get.pid tabs=[base]}
{call e field=pathname tabs=[base]}



{call e field=title i18n=3 i18n_expand=1 tabs=[base]}
{call e field=meta_description tabs=[base]}


{call e field=template_id options=GW::l('/g/EMPTY_OPTION')+$m->getTemplateList() type=select tabs=[base]}
{call e field=link tabs=[base]}

{*
{call e field=gallery_id type=gallery_folder title=GW::l('/g/GALLERY_FOLDER')}
*}


{call e field=active type=bool tabs=[base]}


{foreach $additfields as $field}
	{if $field=="icon"}
		{call e type=text tabs=[extended]}
	{/if}
	{if $field=="display_cond"}
		{call e type=text tabs=[extended]}
	{/if}
	{if $field=="display_badge"}
		{call e type=text tabs=[extended]}
	{/if}	
	{if $field=="inbrackets"}
		{call e type=text tabs=[extended]}
	{/if}		
{/foreach}



{include "tools/form_components.tpl"}
{call "build_form_normal"}
{*isplestiniai laukai*}

{if GW::s('MULTISITE') && $app->user->isRoot()}
	{call e field="site_id"
		type="select_ajax"
		modpath="sitemap/sites"
		options=[]
		preload=1
		tabs=[base]
	}
	
{/if}
{if GW::s('MULTISITE') && !$smarty.get.site_id && $app->site->id==1}
	{call e field=multisite type=bool  tabs=[extended]}	 
{/if}

{$tpl = $item->getTemplate()}


{if $update}
	{$versions=$item->getContentVersions()}
	
	{call e field=in_menu type=bool  i18n=3 i18n_expand=1  tabs=[base]}
	
	{$add_site_css=1}
	{$input_name_pattern="item[input_data][%s]"}
	{$ck_set='medium'}
	
	{$vars=[]}
	{foreach $item->getInputs() as $input}
		{$fieldname=$input->get(name)}
		{$vars[$fieldname]=$item->getContent($fieldname)}
	{/foreach}
	
	{foreach $item->getInputs() as $input}
		{$fieldname=$input->get(name)}
		{$title = $input->get(title)}

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
		
		
		{$opts=$m->injectVars($opts, $vars)}
		
		{$tmpval=$item->getContent($input->get('name'))}
		
		{if strpos($input->get('type'),'multiselect_ajax')!==false}
			{$valgetf=getContentJsonDecode}
		{else}
			{$valgetf=getContent}
		{/if}
		
		{$tmptype=$input->get(type)}
		{if $m->config->get("user/{$app->user->id}/editor")==2}
			{$tmptype=code_smarty}	
		{/if}
		{if $tmptype==htmlarea}
			{$tmptype=ckeditor422}
		{/if}
		
		{if $m->config->get("user/{$app->user->id}/editor_height")}
			{$ck_options.height=$m->config->get("user/{$app->user->id}/editor_height")}
			{$opts.height=$ck_options.height}
		{/if}
		
		{$note=$input->get(note)}
		{if $if18n}
			{capture assign=note}
				{$note}
				
					
					{$totalcnt=0}
					{capture assign=tmp}
				{foreach $versions as $key => $cnt}
					{$key=explode('/', $key)}
					{if $key.0==$fieldname}
						
						<a title="{$title} / {strtoupper($key.1)} / {GW::l('/g/FIELDS/changetrack')}" class='iframeopen' href="{$m->buildUri("{$item->id}/versions",[key=>$fieldname,page_id=>$item->id,ln=>$key.1,clean=>2])}">
							<span class='ln'>{$key.1}</span>:{$cnt}
						</a>
					{/if}
					{$totalcnt=$totalcnt+$cnt}
				{/foreach}
					{/capture}
					
				{if $totalcnt}
					<span class='changetrack'>
					<i class='fa fa-pencil'></i> {$tmp}
					</span>
				{/if}
			{/capture}
		{else}
			{capture assign=note}
				{$note}
				{$cnt=$versions["{$fieldname}/"]}
				{if $cnt}
					<span class='changetrack'>
					<a  title="{$title} / {GW::l('/g/FIELDS/changetrack')}"  class='iframeopen' href="{$m->buildUri("{$item->id}/versions",[key=>$fieldname,page_id=>$item->id,clean=>2,ln=>''])}">
						<i class='fa fa-pencil'></i> {$cnt}
					</a>	
					</span>
				{/if}
			{/capture}
		{/if}
		
		{call e field=$fieldname
			type=$tmptype
			note=$note
			title=$title 
			params_expand=$opts
			valget_func=$valgetf
			i18n=$if18n
			tabs=[templatevars]
		}
		
		
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
{if $item->template_id && GW_Permissions::canAccess('sitemap/tplvars', $app->user->group_ids)}
	{$submit_buttons[]=tplvarsedit}
{/if}

{capture append=footer_hidden}
	
{/capture}

{include file="default_form_close.tpl" extra_fields=[id,path,unique_pathid,insert_time,update_time]}