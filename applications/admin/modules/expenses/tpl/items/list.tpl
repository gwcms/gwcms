{extends file="default_list.tpl"}

{block name="init"}
	{$dl_inline_edit=1}
	{$dl_fields=[image,file,parent_id,expense_date,expense_month,title,amount,type,coefficient,child_amount,status,note]}
	{$dl_actions=[reprocess,edit,delete]}

	{if $m->write_permission}
		{$do_toolbar_buttons=[upload,addnew,filters,hidden,search]}
		{$do_toolbar_buttons_hidden=[dialogconf,print,dialogconf2]}
		{include file="`$m->tpl_dir`/upload_inlist.tpl"}
	{/if}

	{function dl_cell_image}
		{if $item->image}
			{include file="tools/image_preview.tpl" image=$item->image width=55 height=55 fancybox=1}
		{/if}
	{/function}

	{function dl_cell_file}
		{if $item->file}
			<i class="fa fa-file-o"></i> {$item->file->original_filename|escape}
		{/if}
	{/function}

	{function dl_cell_type}
		{$options.type[$item->type]|default:$item->type}
	{/function}

	{function dl_cell_status}
		{$options.status[$item->status]|default:$item->status}
	{/function}

	{function dl_actions_reprocess}
		{list_item_action_m url=[false,[act=>doReprocess,id=>$item->id]] iconclass="fa fa-refresh" caption="Apdoroti iš naujo"}
	{/function}
{/block}
