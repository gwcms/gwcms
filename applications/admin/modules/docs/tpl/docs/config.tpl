
{*
{call e field=allow_sign_again type=bool}
*}

{call e field=steps 
	type=multiselect_ajax 
	options=GW::l('/m/OPTIONS/steps')
	empty_option=1 
	sorting=1
	value_format=json1
	hidden_note='galimi variantai: perziuret sablona, uzpildyti duomenis, perziureti uzpildyta / pasirasyti<br>uzpildyti duomenis, perziureti / pasirasyti su marksign'
	
}


{if $m->feat(act_of_acceptance)}
	{call e field="act_of_acceptance" type="select_ajax"  modpath="docs/docs" options=[] after_input_f="editadd" preload=1}
{/if}

{if $app->user->isRoot()}

	{call e field="features" type=multiselect options=GW::l('/m/OPTIONS/features') value_format=json1}

	{call e field="enable_unsafe" type=bool}

{/if}