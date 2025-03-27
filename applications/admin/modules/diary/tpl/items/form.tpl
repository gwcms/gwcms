{include file="default_form_open.tpl"  changes_track=1 form_width="100%"}
{$width_title="100px"}

{call e field=parent_id type=select options=$item->getParentOpt() default=$smarty.get.pid}

{call e field=type type=select options=$m->lang.GALLERY_ITEM_TYPE_OPT}

{*contentsCss=>'applications/admin/css/ckstyle1.css',*}
{*
	extraPlugins=>autogrow,
	height=>500,
	autoGrow_maxHeight=>800
*}

{$ck_options=[
	toolbarStartupExpanded=>false, 
	autoParagraph=>false, 
	enterMode=>'CKEDITOR.ENTER_BR'
]}

{call e field=text type=htmlarea layout=wide}

{$curr=date('Y-m-d H:i:s')}
{call e field=time default=$curr}



{include file="default_form_close.tpl"}