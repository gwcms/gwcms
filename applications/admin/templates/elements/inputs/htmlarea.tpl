
{$dir = GW::$settings.DIR.LIB}
{$ck = GW::getInstance('ckeditor',"`$dir`/ckeditor/ckeditor_php5_gw.php")}


{$width=$width|default:"800"}
{$ck->editor($input_name, $value, ['width'=>$width, 'language'=>$app->ln])}


{*
<textarea {if $autoresize}class="ta_autoresize"{/if} name="{$input_name}" {if $tabs}onkeydown="return catchTab(this,event)"{/if} 
style="width: {$width|default:"100%"}; {if !$rows}height: {$height|default:"250px"};{/if}" {if $rows}rows="{$rows}"{/if} 
onchange="this.value=$.trim(this.value);" {if $hidden_note}title="{$hidden_note}"{/if}>{$value|escape}</textarea>
*}