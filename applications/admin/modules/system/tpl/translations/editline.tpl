{include file="default_form_open.tpl" action=saveLine}

{$fields=array_merge(['ANY'],array_keys($langsfound))}


{call e0 field="lfid" type=hidden}
{call e0 field="lns" value=implode(',',array_keys($langsfound)) type=hidden}
{call e0 field="isnew" type=hidden}


{call e field="key" type=read}


{call e field="ANY" note="use if all langs value same AND leave lang fields empty"}

{foreach $langsfound as $ln => $x}
	{call e field=$ln}
{/foreach}






{function name=df_submit_button_autotrans}
	<button class="btn btn-default float-rights" onclick="this.form.elements['submit_type'].value=3;"><i class="fa fa-google"></i> Auto translate empty cells</button>
{/function}

{function name=df_submit_button_send}
	<button class="btn btn-warning float-rights" onclick="this.form.elements['submit_type'].value=7;"><i class="fa fa-floppy-o"></i> {GW::l('/g/SAVE')} + {GW::l('/A/VIEWS/doSend')} <i class="fa fa-paper-plane-o"></i></button>
{/function}


{$submit_buttons=[save,send,autotrans]}




{include file="default_form_close.tpl"}

