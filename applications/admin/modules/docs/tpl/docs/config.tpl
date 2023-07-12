
{call e field=allow_sign_again type=bool}

{call e field=steps 
	type=multiselect 
	options=GW::l('/m/OPTIONS/steps')
	empty_option=1 
	sorting=1
	value_format=json1
	hidden_note='galimi variantai: perziuret sablona, uzpildyti duomenis, perziureti uzpildyta / pasirasyti<br>uzpildyti duomenis, perziureti / pasirasyti su marksign'
	
}