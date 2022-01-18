
	{include "`$m->tpl_dir`payselect.tpl"}
	<p>
		{GW::ln('/m/PAY_METHOD_SELECT')}:
	</p>
	<div class="row">

		{call "pay_select_cart"}
	</div>