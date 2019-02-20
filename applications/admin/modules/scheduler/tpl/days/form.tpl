{include file="default_form_open.tpl"}

{*
{call e field=title}
*}
{call e field=date type=date}



{call e0 field=group_id type=hidden}
{call e field=start_time type=select options=$time_options}
{call e field=end_time  type=select options=$time_options}
{call e field=slot_duration type=select options=$time_options}
{call e field=break_after_x_slots type=number}
{call e field=break_time  type=select options=$time_options}
{call e field=desciption}




{*
	2	group_id
	3	date	date			No	None		Change Change	Drop Drop	

Distinct values Distinct values
	4	start_time	varchar(5)	utf8_general_ci		No	None		Change Change	Drop Drop	

	5	end_time	varchar(5)	utf8_general_ci		No	None		Change Change	Drop Drop	

	6	slot_duration	varchar(5)	utf8_general_ci		No	None		Change Change	Drop Drop	
	7	break_after_x_slots	tinyint(4)			No	None		Change Change	Drop Drop	
	8	break_time	varchar(5)	utf8_general_ci		No	None		Change Change	Drop Drop	
	9	desciption	varchar(255)	utf8_general_ci		No	None		Change Change	Drop Drop	
	10	insert_time	datetime			No	None		Change Change	Drop Drop	
	11	update_time
*}

{include file="default_form_close.tpl"}