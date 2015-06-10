
{$dir = GW::$settings.DIR.LIB}
{$ck = GW::getInstance('ckeditor',"`$dir`/ckeditor/ckeditor_php5_gw.php")}


{$width=$width|default:"800"}


{$ck_editor_opt=['width'=>$width, 'language'=>$app->ln]}

{if $ck_options=='minimum'}
	{$ck_editor_opt['toolbarStartupExpanded']=false}
	{$ck_editor_opt['toolbar']='Basic'}
{elseif $ck_options=='basic'}
	{$ck_editor_opt['toolbar']='Basic'}
{elseif is_array($ck_options)}
	{$ck_editor_opt=$ck_editor_opt+$ck_options}
{else}
	
{/if}

{$ck->editor($input_name, $value, $ck_editor_opt)}

{*
<textarea {if $autoresize}class="ta_autoresize"{/if} name="{$input_name}" {if $tabs}onkeydown="return catchTab(this,event)"{/if} 
style="width: {$width|default:"100%"}; {if !$rows}height: {$height|default:"250px"};{/if}" {if $rows}rows="{$rows}"{/if} 
onchange="this.value=$.trim(this.value);" {if $hidden_note}title="{$hidden_note}"{/if}>{$value|escape}</textarea>
*}