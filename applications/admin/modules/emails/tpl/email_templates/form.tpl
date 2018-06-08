{include file="default_form_open.tpl" form_width="1000px"}

<style>
	.input_label_td{ width: 150px; }
	.custsender .ln_contain{ width:30%;}
	.bodyinputs .ln_contain{ margin-bottom: 10px; }
</style>


{include file="elements/input.tpl" name=ln_enabled type=bool  i18n_expand=1 default=1 onchange="lnenabler(ln,state,this)" i18n=3}

<script>
	function lnenabler(ln, state, obj)
	{
		require(['forms'], function(){ 			
			gw_forms.lnEnable(ln, state, obj) 
		})
	}
</script>


{include file="elements/input.tpl" name=admin_title}

{if !$custom_cfg.no_idname}
	{include file="elements/input.tpl" name=idname}
{/if}

{include file="elements/input.tpl" name=owner_type}
{include file="elements/input.tpl" name=owner_field}


{include file="elements/input.tpl" name=custom_sender type=bool stateToggleRows="custsender"}
{include file="elements/input.tpl" name=sender type=text rowclass="custsender" i18n=3 }





{if $custom_cfg.vars_hint}
	{$tmpnote=GW::l($custom_cfg.vars_hint)}
{/if}


{include file="elements/input.tpl" name=subject i18n=4 hidden_note=$tmpnote}


{if $item->body_editor == 0}
	{$ck_options=[toolbarStartupExpanded=>false]}
	{$bodyInpType=htmlarea}
{elseif $item->body_editor == 1}
	{$bodyInpType=textarea}
{elseif $item->body_editor == 2}
	{$bodyInpType=code_smarty}
{/if}

{include file="elements/input.tpl" type=$bodyInpType name=body i18n=4 rowclass="bodyinputs" hidden_note=$tmpnote height=$item->body_editor_height|default:"200px"}	



{include file="elements/input.tpl" type=select name=format_texts options=GW::l('/m/OPTIONS/format_texts') readonly=isset($custom_cfg.format_texts_ro)}
{include file="elements/input.tpl" type=select name=body_editor options=GW::l('/m/OPTIONS/body_editor') readonly=isset($custom_cfg.body_editor_ro) hidden_note=GW::l('/m/FIELDS_NOTE/PUSH_APPLY_TO_TAKE_EFFECT')}
{include file="elements/input.tpl" type=select name=body_editor_height options=GW::l('/m/OPTIONS/body_editor_height') readonly=isset($custom_cfg.body_editor_height_ro) hidden_note=GW::l('/m/FIELDS_NOTE/PUSH_APPLY_TO_TAKE_EFFECT')}


{include file="elements/input0.tpl" name=config value=json_encode($custom_cfg) type=hidden}



{include file="default_form_close.tpl"}