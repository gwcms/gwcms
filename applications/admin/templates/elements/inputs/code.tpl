<textarea  id="{$id}" name="{$input_name}" style="display:none" class="inp-code">{$value|escape}</textarea>
<pre id="{$id}_aceeditor" style="width: {$width|default:"98%"}; {if !$rows}height: {$height|default:"auto"};{/if} {if $border}border:1px solid silver;{/if}"></pre>

<script src="{$app->sys_base}vendor/ace-builds/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
	require(['gwcms'], function(){
		var {$id}editor = ace.edit("{$id}_aceeditor");
		//editor.setTheme("ace/theme/twilight");
		{$id}editor.session.setMode("ace/mode/{$codelang}");
			
		var {$id}textarea = $('#{$id}');
		{$id}editor.getSession().setValue({$id}textarea.val());
		{$id}editor.getSession().on('change', function(){
			{$id}textarea.val({$id}editor.getSession().getValue());
			{$id}textarea.change();	//track changes wont work without this
		});   	
		
		//all that shit is needed to initiate editor if it is hidden@startup
		setTimeout(function(){
			if(!$("#{$id}_aceeditor").is(':visible')){

				{$id}editorinterval=setInterval(function(){
						
					if($("#{$id}_aceeditor").is(':visible')){
						{$id}editor.resize() ;
						{$id}editor.renderer.updateFull() ;
						clearInterval({$id}editorinterval);
					}
				}, 1000);
			}
		},1000);
		
	


	})
</script>

<style>
#{$id}_aceeditor{
	margin:0;
}
</style>