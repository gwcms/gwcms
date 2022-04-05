{include file="default_form_open.tpl" form_width="100%"}


<style>
	.input_label_td{ width: 150px; }
</style>


{$fields="
method
order_id
amount
processed
insert_time
update_time"}

{$fields = explode("\n", $fields)}
{foreach $fields as $field}
	{call e readonly=1}
{/foreach}

{call e field=data type=code_json height="500px"}


{include file="default_form_close.tpl"}