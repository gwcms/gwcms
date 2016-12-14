{include file="default_form_open.tpl" width="900px"}


{include file="elements/input.tpl" name=title}
{include file="elements/input.tpl" name=image1 type=image}
{include file="elements/input.tpl" name=name_orig}
{include file="elements/input.tpl" type=textarea name=description}
{include file="elements/input.tpl" name=rate type=select options=[0,1,2,3,4,5,6,7,8,9,10]}
{include file="elements/input.tpl" name=recommend}

{include file="elements/input.tpl"  name=imdb type=code_json height=200px nopading=1}  

{include file="default_form_close.tpl"}