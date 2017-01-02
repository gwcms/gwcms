
{$dir = GW::$settings.DIR.VENDOR}
{$ck = GW::getInstance('ckeditor',"`$dir`/ckeditor/ckeditor_php5_gw.php")}


{$width=$width|default:"800"}


{$ck_editor_opt=['width'=>$width, 'language'=>$app->ln]}
{$ck_editor_opt['contentsCss']='applications/site/css/style.css'}



{if $remember_size}
	{$custom_size_name="`$name`_editor_size"}
	{$remember_size_id="`$id`_editor_size"}
	{$custom_size=explode('x', $item->$custom_size_name)}


	{if count($custom_size)==2}
		{$ck_options.width=$custom_size.0}
		{$ck_options.height=$custom_size.1}
	{/if}


	{include file="elements/input0.tpl" type=hidden name="`$name`_editor_size" value={$item->$cusom_size_name} id=$remember_size_id}

	<script type="text/javascript">
		require(['gwcms'],function(){
			CKEDITOR.instances['{$input_name}'].on('instanceReady', function (ev) {
									ev.editor.on('resize', function (reEvent) {
											var tmp = reEvent.sender.container.$
											$('#{$remember_size_id}').val(tmp.clientWidth + 'x' + tmp.clientHeight);

											//console.log(tmp.clientWidth+'x'+tmp.clientHeight)
									});		
		});	

	</script>	
{/if}



{*d::ldump([$ck_options,$ck_editor_opt])*}

{if $ck_options=='minimum'}
	{$ck_editor_opt['toolbarStartupExpanded']=false}
	{$ck_editor_opt['toolbar']='Basic'}
{elseif $ck_options=='basic'}
	{$ck_editor_opt['toolbar']='Basic'}
{elseif is_array($ck_options)}
	{$ck_editor_opt=$ck_options+$ck_editor_opt}
{else}

{/if}






{$ck->editor($input_name, $value, $ck_editor_opt)}




{*
<textarea {if $autoresize}class="ta_autoresize"{/if} name="{$input_name}" {if $tabs}onkeydown="return catchTab(this,event)"{/if} 
style="width: {$width|default:"100%"}; {if !$rows}height: {$height|default:"250px"};{/if}" {if $rows}rows="{$rows}"{/if} 
onchange="this.value=$.trim(this.value);" {if $hidden_note}title="{$hidden_note}"{/if}>{$value|escape}</textarea>
*}