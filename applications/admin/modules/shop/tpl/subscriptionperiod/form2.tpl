{include file="default_form_open.tpl" form_width="1040px"  changes_track=1} 
{$width_title="150px"}

{$labelright=1}

{call e field=enatos type=bool stateToggleRows="enatos"} 





<style>
	.e_horizontal_group .input_label_td{ text-align:right;width:100px; }
	.e_horizontal_group td{ padding: 2px 8px 2px 2px;}
</style>

<tr>
	<td colspan="2">
		<table style="width:100%" class="e_horizontal_group">
			<tr>
				
		{call e notr=1  field="composer_id"
			type="select_ajax"
			modpath="shop/composers"
			options=[]
			preload=1
			after_input_f=editadd
		}

		{call e notr=1 field=title} 
		{call e notr=1 field=subtitle} 
	

	</tr><tr>
		{call e notr=1 field=price} 
		{call e notr=1 field=pages} 
		{call e notr=1 field=year}
	
	</tr><tr>
		{call e field=weight notr=1  } 
		{call e field=size notr=1} 
		{call e field=qty notr=1} 
	</tr><tr>
		{call e notr=1 field=remote_id} 
		{call e notr=1 field=addinfo type=select options=[-1=>'not processed','0'=>'empty info','1'=>'has info']}
		{call e notr=1 field=imgpro type=select options=["-1"=>'not processed','0'=>'not found', '2'=>'hlmgbdealers (NATOS2)','3'=>'musicshopeurope (NATOS3)']}
	</tr><tr>
		{call e field=ismn notr=1 } 
		{call e field=isbn notr=1} 
	</tr><tr>	
		{call e field=ean notr=1}
		{call e field=upc notr=1}
	</tr></table>
	</td>
</tr>





{call e field=pdf type=file rowclass="enatos"}
{call e field=description type=textarea i18n=4 layout=wide}

{include file="default_form_close.tpl"}
