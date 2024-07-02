{include file="default_form_open.tpl"}

{if isset($m->filters.number)}{$tmpdef=$m->filters.number}{/if}
{call e field=number default=$tmpdef}
{call e field=msg type=smsmessage  note="<i class='input_note'></i>" height=80px}

{if $app->user->isRoot()}
	{call e field=retry}
{/if}





{function name=df_submit_button_send}
	<button class="btn btn-warning float-rights" onclick="this.form.elements['submit_type'].value=7;"><i class="fa fa-floppy-o"></i> {GW::l('/g/SAVE')} + {GW::l('/A/VIEWS/doSend')} <i class="fa fa-paper-plane-o"></i></button>
{/function}

{if $item->status != 7}
	{if $m->write_permission}
		{$submit_buttons=[save,send]}
	{else}
		{$submit_buttons=[save]}
	{/if}
{else}
	{$submit_buttons=[save,apply,cancel]}
{/if}



{include file="default_form_close.tpl"}