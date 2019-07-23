{include file="default_form_open.tpl" action=importExportTree}

{call e field=parent_id type=select options=$m->getParentOpt($item->id) default=$smarty.get.pid required=1}


{call e field=action type=select options=[export=>Eksportuoti,import=>Importuoti] empty_option=1 rowclass=ie_action}

{call e field=importfile type=file rowclass=row_import}
{call e field=show type=bool rowclass=row_export}
{call e field=include_content type=bool rowclass=row_export}
{call e field=export_type type=select options=[item_and_childs=>'Puslapį ir vaikus',only_childs=>'Pasirinkto puslapio vaikus',page_only=>'Tik puslapį'] rowclass=row_export}



<style>
	.row_export{ display:none }
	.row_import{ display:none }
</style>

<script>
	require(['gwcms'], function(){
		$('.ie_action').change(function(){
			$('.row_import, .row_export').hide();
			var act = $('.ie_action select').val()
			$('.row_'+act).fadeIn();
		}).change();
	})
	
</script>


{$submit_buttons=[submit,cancel]}

{include file="default_form_close.tpl"}