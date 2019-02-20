{if $smarty.get.smallform}{$form_width="400px"}{/if}

{include file="default_form_open.tpl"}

{call e field=participant_num}
{call e field=date}

{call e field=start_time}
{call e field=end_time}


{include 
	file="elements/input_select_edit.tpl" 
	name=type_id type=select 
	empty_option=1
	datasource=$app->buildUri('scheduler/types',['type'=>'slot'])
}

{*
{call e field=group_id}
*}

{call e field=description}

{$extra_fields=[duration,id,insert_time,update_time]}

{include file="default_form_close.tpl"}