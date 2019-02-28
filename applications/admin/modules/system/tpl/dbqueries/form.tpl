{include file="default_form_open.tpl"  form_width="100%"}

{$width_title=70px}

{call e field=name}

{call e field=sql type=code codelang=sql height=400px layout=wide nopading=1}  

{function name=df_submit_button_saveAndRun}
	<button class="btn btn-warning float-rights" onclick="this.form.elements['submit_type'].value=7;">
			<i class="fa fa-floppy-o"></i> {GW::l('/g/SAVE')} + Run <i class="fa fa-caret-square-o-right"></i>
	</button>
{/function}

{include file="default_form_close.tpl" submit_buttons=[save,saveAndRun,apply,cancel]}