<?php
$expires = 60*60*24*14;
header("Pragma: public");
header("Cache-Control: maxage=".$expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');


switch($_GET['ln'])
{
	case 'lt':
		?>
		
date_picker_locale = 
{
	firstDay: 1,
	days: ['sekmadienis','pirmadienis','antradienis','trečiadienis',
	       			'ketvirtadienis','penktadienis','šeštadienis'],
	daysShort: ['sek','pir','ant','tre','ket','pen','šeš'],
	daysMin: ['Se','Pr','An','Tr','Ke','Pe','Še'],
	months: ['Sausis','Vasaris','Kovas','Balandis','Gegužė','Birželis',
	  		'Liepa','Rugpjūtis','Rugsėjis','Spalis','Lapkritis','Gruodis'],
	monthsShort: ['Sau','Vas','Kov','Bal','Geg','Bir',
	      		'Lie','Rugp','Rugs','Spa','Lap','Gru'],
	weekMin: ''
}	
	
		<?php
	break;

	case 'no':
		?>
		
date_picker_locale = 
{
	days: ['sekmadienis','pirmadienis','antradienis','trečiadienis',
	       			'ketvirtadienis','penktadienis','šeštadienis'],
	daysShort: ['sek','pir','ant','tre','ket','pen','šeš'],
	daysMin: ['Se','Pr','An','Tr','Ke','Pe','Še'],
	months: ['Sausis','Vasaris','Kovas','Balandis','Gegužė','Birželis',
	  		'Liepa','Rugpjūtis','Rugsėjis','Spalis','Lapkritis','Gruodis'],
	monthsShort: ['Sau','Vas','Kov','Bal','Geg','Bir',
	      		'Lie','Rugp','Rugs','Spa','Lap','Gru'],
	weekMin: ''
}	
	
		<?php
	break;	

}