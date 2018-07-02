{include file="default_form_open.tpl"  changes_track=1 form_width="100%"}
{$width_title="100px"}

{include file="elements/input.tpl" name=parent_id type=select options=$item->getParentOpt() default=$smarty.get.pid}

{include file="elements/input.tpl" name=type type=select options=$m->lang.GALLERY_ITEM_TYPE_OPT}


{$ck_options=[
	toolbarStartupExpanded=>false, 
	autoParagraph=>false, 
	contentsCss=>'applications/admin/css/ckstyle1.css',
	enterMode=>'CKEDITOR.ENTER_BR', 
	extraPlugins=>autogrow,
	height=>300,
	autoGrow_maxHeight=>800
	
]}

{include file="elements/input.tpl" name=text type=htmlarea layout=wide}

{$curr=date('Y-m-d H:i:s')}
{include file="elements/input.tpl" name=time default=$curr}



{include file="default_form_close.tpl"}