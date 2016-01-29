<textarea  id="{$id}" name="{$input_name}" style="display:none">{$value|escape}</textarea>
<pre id="{$id}_aceeditor" style="width: {$width|default:"98%"}; {if !$rows}height: {$height|default:"auto"};{/if} {if $border}border:1px solid silver;{/if}"></pre>

<script src="{$app->sys_base}vendor/ace-builds/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
	var {$id}editor = ace.edit("{$id}_aceeditor");
	//editor.setTheme("ace/theme/twilight");
	{$id}editor.session.setMode("ace/mode/{$codelang}");
    
    
	var {$id}textarea = $('#{$id}');
	{$id}editor.getSession().setValue({$id}textarea.val());
	{$id}editor.getSession().on('change', function(){
		{$id}textarea.val({$id}editor.getSession().getValue());
	});   
</script>

<style>
#{$id}_aceeditor{
	margin:0;
}
</style>
