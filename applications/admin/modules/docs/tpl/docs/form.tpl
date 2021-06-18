{include file="default_form_open.tpl" form_width="100%"}

<style>
	.input_label_td{ width: 150px; }
	.custsender .ln_contain{ width:30%;}
	.bodyinputs .ln_contain{ margin-bottom: 10px; }
</style>


{call e field=ln_enabled type=bool  i18n_expand=1 default=1 onchange="lnenabler(ln,state,this)" i18n=3}

{*
{call e field="selectcfg" type=multiselect options=[hidden_note,note,placeholder,config,size,linkedfields] options_fix=1}

<script>
	require(['gwcms'], function(){
		$('#item__selectcfg______').change(function(){			
			
		
			$(this).find('option').each(function(){
				
				var value = $(this).val()
				
				$('#gw_input_item__'+value+'__, .field_'+value).toggle($(this).is(":selected"))
						
			})
			
			
		}).change()			
	})
</script>
*}

<script>
	function lnenabler(ln, state, obj)
	{
		require(['forms'], function(){ 			
			gw_forms.lnEnable(ln, state, obj) 
		})
	}
</script>


{function "display_field_var"}{strip}
	{if $input->get(i18n)}{$i18n_suff="_{$app->ln}"}{else}{$i18n_suff=""}{/if}
	{$var="{$vargroup}_{$groupid}.{$input->get(fieldname)}{$i18n_suff}"}
	{if $input->type == 'date'}
		{literal}{{/literal}FH::dateHuman(${$var},[year=>1]){literal}}{/literal}
	{elseif $input->type==number}
		{literal}{{/literal}${$var}{literal}}{/literal}, ({literal}{{/literal}GW_Sum_To_Text_Helper::sum2text(${$var}, {$app->ln}){literal}}{/literal});
	{else}
		{literal}{{/literal}${$var}|escape{literal}}{/literal}
	{/if}
{/strip}{/function}

{call e field=admin_title}

{if !$custom_cfg.no_idname}
	{call e field=idname}
{/if}



{if $custom_cfg.vars_hint}
	{$tmpnote=GW::l($custom_cfg.vars_hint)}
{/if}


{call e field=title i18n=4 hidden_note=$tmpnote}


{call e field="form_id" type=select_ajax modpath="forms/forms" options=[] after_input_f="editadd" preload=1 hidden_note=GW::l('/m/FIELD_NOTE/PUSH_APPLY_TO_TAKE_EFFECT') empty_option=1}

{call e field="doc_vars" type="multiselect_ajax"  modpath="forms/forms" options=[] after_input_f="editadd" preload=1 hidden_note=GW::l('/m/FIELD_NOTE/PUSH_APPLY_TO_TAKE_EFFECT')}

{if $item->id > 25}
	
	{foreach $item->doc_forms as $groupid => $form}
		{call e field="keyval/vars_{$groupid}" type="multiselect_ajax" title="{GW::l('/m/FIELDS/doc_adm_fields')} \"{$form->title}\" atsakymai"  
			modpath="forms/answers"  
			value_format=json1
			sorting=1
			source_args=[owner_id=>$form->id] options=[] after_input_f="editadd" preload=1 
			hidden_note=GW::l('/m/FIELD_NOTE/PUSH_APPLY_TO_TAKE_EFFECT')}
	{/foreach}

{else}
	{foreach $item->doc_forms as $groupid => $form}
		{call e field="keyval/vars_{$groupid}" type="select_ajax" title="{GW::l('/m/FIELDS/doc_adm_fields')} \"{$form->title}\" atsakymas"  modpath="forms/answers"  source_args=[owner_id=>$form->id] options=[] after_input_f="editadd" preload=1 hidden_note=GW::l('/m/FIELD_NOTE/PUSH_APPLY_TO_TAKE_EFFECT')}
	{/foreach}	
{/if}









{if $item->body_editor == 0}
	{$ck_set='minimum'}
	{$ck_options.height=$item->body_editor_height|default:"200px"}
	{$bodyInpType=htmlarea}
{elseif $item->body_editor == 1}
	{$bodyInpType=textarea}
{elseif $item->body_editor == 2}
	{$bodyInpType=code_smarty}
	
{/if}



{call e field=body type=$bodyInpType i18n=4 rowclass="bodyinputs" hidden_note=$tmpnote height=$item->body_editor_height|default:"200px"}	


{if $item->form && $item->form->elements}
	{$fieldnames=array_keys($item->form->elements)}

	
	{capture assign=tmp1}Form "{$item->form->admin_title}" variables{/capture}
{capture assign=tmp}
<textarea style="min-height:100px;width:100%;overflow-y: scroll;padding:1px" >
{foreach $fieldnames as $fieldname}
{literal}{{/literal}$form.{$fieldname}|escape{literal}}{/literal}
{/foreach}
{foreach $item->doc_forms as $groupid => $form}
{foreach $form->elements as $input}
{call  "display_field_var" vargroup=vars}
{/foreach}

{*foreach $item->doc_ext_fields as $groupid => $form}
{foreach $form->elements as $fieldname => $input}
{call "display_field_var"}
{/foreach}
{/foreach*}
{/foreach}

</textarea>	
{/capture}
		
	{call e type=read title=$tmp1 field="nevermind" value=$tmp}
{/if}


{$hidden_note_copy=1}
{call e field=format_texts type=select options=GW::l('/m/OPTIONS/format_texts') readonly=isset($custom_cfg.format_texts_ro)}
{call e field=body_editor type=select options=GW::l('/m/OPTIONS/body_editor') readonly=isset($custom_cfg.body_editor_ro) hidden_note=GW::l('/m/FIELDS_NOTE/PUSH_APPLY_TO_TAKE_EFFECT')}
{call e field=body_editor_height type=select options=GW::l('/m/OPTIONS/body_editor_height') readonly=isset($custom_cfg.body_editor_height_ro) hidden_note=GW::l('/m/FIELDS_NOTE/PUSH_APPLY_TO_TAKE_EFFECT')}


{call e field=config type=code_json height="100px"}
{call e field=admin_emails type=text}


{include file="elements/input_transkey.tpl" name=site_info_trans}

{if $app->user->isRoot()}
	{call e field=protected type=bool}
{/if}

{if $app->user->isRoot()}
	{$tmpreadonly=false}
{else}
	{$tmpreadonly=true}
{/if}

{call e field="doc_adm_fields" type="multiselect_ajax"  modpath="forms/forms" options=[] after_input_f="editadd" preload=1 hidden_note=GW::l('/m/FIELD_NOTE/doc_adm_fields')}



{foreach $item->doc_ext_fields as $groupid => $form}

	
		{capture assign=tmp}
			<tr><th colspan="99" class="th_h3 th_single">
				{$form->title} 
				<a class="iframeopen" href="{$app->buildUri("forms/forms/{$form->id}/elements",['clean'=>2])}" title='{$form->title} laukeliai'><i class="fa fa-pencil-square-o text-muted"></i></a>
			</th></tr>
		{/capture}
		{$fields_config.fields[]=$tmp}
		
	{foreach $form->elements as $input}
			
		{capture assign=tmp}
			{$input->get(note)}{if $input->get(note)}<br>{/if}
			<small>{call "display_field_var" vargroup=ext_fields}</small>
		{/capture}
			
		{if $input->size > 6}
			{$tmpcolsp=3}
		{else}
			{$tmpcolsp=1}
		{/if}
		
		{$field=[
			field=>"keyval/{$groupid}_{$input->get(fieldname)}",
			type=>$input->get(type),
			hidden_note=>$input->hidden_note,
			title=>$input->get(title),
			placeholder=>$input->placeholder,
			i18n=>$input->get(i18n),
			colspan=>$tmpcolsp,
			note=>$tmp
		]}
		{$opts=json_decode($input->get('config'),true)}	
		{if $input->get(inp_type)=='select_ajax'}
			{$opts.preload=1}
			{$opts.modpath=$input->get('modpath')}
		{/if}	
		{if is_array($opts)}
			{$field = array_merge($field, $opts)}
		{/if}	
		
		{$fields_config.fields[$field.field]=$field}
	
	{/foreach}
	

{/foreach}
{$fields_config.cols=2}


</table><table class="gwTable gwcmsTableForm">
{include "tools/form_components.tpl"}
{call "build_form_normal"}


{*
{call e field=owner_type readonly=$tmpreadonly}
{call e field=owner_field readonly=$tmpreadonly}
*}

{include file="default_form_close.tpl"}