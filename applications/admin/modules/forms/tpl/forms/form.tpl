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
{call e field=title i18n=4}
{call e field=description type=textarea i18n=4}


{if !$custom_cfg.no_idname}
	{call e field=idname}
{/if}







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