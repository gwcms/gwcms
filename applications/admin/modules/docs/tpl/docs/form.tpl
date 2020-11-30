{include file="default_form_open.tpl" form_width="1000px"}

<style>
	.input_label_td{ width: 150px; }
	.custsender .ln_contain{ width:30%;}
	.bodyinputs .ln_contain{ margin-bottom: 10px; }
</style>


{call e field=ln_enabled type=bool  i18n_expand=1 default=1 onchange="lnenabler(ln,state,this)" i18n=3}

<script>
	function lnenabler(ln, state, obj)
	{
		require(['forms'], function(){ 			
			gw_forms.lnEnable(ln, state, obj) 
		})
	}
</script>


{call e field=admin_title}

{if !$custom_cfg.no_idname}
	{call e field=idname}
{/if}










{if $custom_cfg.vars_hint}
	{$tmpnote=GW::l($custom_cfg.vars_hint)}
{/if}


{call e field=title i18n=4 hidden_note=$tmpnote}


{call e field="form_id" type=select_ajax modpath="forms/forms" options=[] after_input_f="editadd" preload=1 hidden_note=GW::l('/m/FIELD_NOTE/PUSH_APPLY_TO_TAKE_EFFECT')}


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
<pre style="height:auto;max-height:80px;overflow-y: scroll;padding:1px" >
{foreach $fieldnames as $fieldname}
{literal}{{/literal}$form.{$fieldname}|escape{literal}}{/literal}
{/foreach}
</pre>	
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

{*
{call e field=owner_type readonly=$tmpreadonly}
{call e field=owner_field readonly=$tmpreadonly}
*}

{include file="default_form_close.tpl"}