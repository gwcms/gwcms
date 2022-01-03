{include "default_form_open.tpl" action=MailS2 form_width="1000px" changes_track=1}

{$width_title="150px"}

{if $item->confirm==1}

	{call e0 field="template_id" type="hidden"}
	{call e field="template_id" type="read" value=$item->template->admin_title}
	{call e0 field="recipients" type="hidden"}
	
	
	<tr><td colspan="2">
		<table>
			<tr>
				<td>
		{foreach $item->recipient_rows as $row}
			<li><a class='partic' href="#" data-src="{$m->buildUri(false,[act=>domailpreview,row=>$row,template_id=>$item->template_id])}">{$row['lang']} {$row['to']|escape}</a>
		{/foreach}
				</td>
				<td>
					<iframe id="preview" src="" style="width:600px;height:800px;">
					</iframe>
				</td>
			</tr>
		</table>
	</td>
	</tr>
	

	{capture append=footer_hidden}
		<script>
			require(['gwcms'], function(){

				$('.partic').click(function(e){
					e.preventDefault();
					$('#preview').attr('src', $(this).data('src'));
				})
			})
		</script>
	{/capture}
	
	{call e field="confirmsend" type=select options=['yes'=>"Patvirtinu",'no'=>"Nepatvirtinu"] empty_option=1}	
	
	{function name=df_submit_button_send}
		<button class="btn btn-primary"><i class="fa fa-save"></i> Siųsti visus ({count($item->recipient_rows)})</button>
	{/function}		

	{$submit_buttons=[send]}
{else}
	
	{if $m->isjury} {$owner_field=jury_communication} {else} {$owner_field=participant_communication} {/if}

	{$opts=[vars_hint=>'/M/COMPETITIONS/FIELDS_HELP/invoice',format_texts_ro=>1,vals=>[format_texts=>2]]}
	{$owner=['owner_type'=>'competitions/participants','owner_field'=>$owner_field]}
	
	{include file="elements/input_select_mailtemplate.tpl" field="template_id" default_vals=[admin_title=>GW::l('/m/FIELDS/post_pay_mail_default'),idname=>participant_communication] tabs=[payment]}				
	
	{if !$item->recipients}
		{$recipients=[]}
		{foreach $list as $itm}
			{capture append=recipients}{$itm->id};{$itm->get('user/use_lang')};{$itm->recipient}{/capture}
		{/foreach}


		{$recipients=implode("\n", $recipients)}
	{/if}

	{call e field="recipients" type="textarea" default=$recipients autoresize=1}	
	{function name=df_submit_button_submit}
		<button class="btn btn-primary pull-right"> {$lang.NEXT} {$lang.STEP} <i class="fa fa-chevron-right" aria-hidden="true"></i></button>
	{/function}	
	{$submit_buttons=[submit]}
{/if}






{include file="default_form_close.tpl" extra_fields=false }