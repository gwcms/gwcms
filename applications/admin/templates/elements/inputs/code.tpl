<textarea  
	id="{$id}" name="{$input_name}" style="display:none" {if $readonly}readonly="readonly"{/if} class="inp-code {if $class}{$class}{/if}" >{$value|escape}</textarea>
<pre id="{$id}_aceeditor" class="codeedit {if $class}{$class}{/if}" style="{if !$rows}height: {$height|default:"auto"};
     {/if} {if $border}border:1px solid silver;{/if}{if $width}width: {$width};{/if} " title="shift + [plus] - increases height"></pre>


{if $height_memory}
	<input id="{$id}_aceeditor_height" name="{call calcElmName field="`$field`_height"}" type="hidden" value="{$item->get("`$field`_height")}"  />
{/if}

<script src="{$app->sys_base}vendor/ace-builds/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
{*<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/src-min-noconflict/ace.js"></script>*}
 
<script>
	require(['gwcms'], function(){
		var {$id}editor = ace.edit("{$id}_aceeditor");
		//editor.setTheme("ace/theme/twilight");
		{$id}editor.session.setMode("ace/mode/{$codelang}");
						
		var {$id}textarea = $('#{$id}');
		{if $readonly}{$id}editor.setReadOnly(true){/if}
			
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
		
		//padidint sumazint laukelio auksti
		$('#{$id}_aceeditor').keypress(function(e) {

		  if((e.which==45 || e.which==43) && e.shiftKey)
		  {
			  var newheight=$(this).height()+(e.which==43 ? 100: -100);
			  
			  $(this).css('height', newheight )
				{$id}editor.resize() ;
				{$id}editor.renderer.updateFull() ;		
				 e.preventDefault();
				 
			{if $height_memory}
				$('#{$id}_aceeditor_height').val(newheight);
			{/if}	 
		  }	
		});
		
		{if $height_memory}
			var restore_height=$('#{$id}_aceeditor_height').val()-0;
			if(restore_height){
				$('#{$id}_aceeditor').css('height', restore_height );
				{$id}editor.resize() ;
				{$id}editor.renderer.updateFull() ;
			}
		{/if}
		

		var modified = 'ace-changed'; // css class
		{$id}editor.on('change', function(e) {
			var activeLine = e.start.row;
			if (e.action == "insert") {
				while (activeLine < (e.end.row+1)) {
					{$id}editor.session.removeGutterDecoration(activeLine, modified);
					{$id}editor.session.addGutterDecoration(activeLine, modified);
					activeLine++;
				}
			} else if (e.action == "remove") {
				while (activeLine < (e.end.row+1)) {
					{$id}editor.session.removeGutterDecoration(activeLine, modified);
					activeLine++;
				}
				{$id}editor.session.addGutterDecoration(e.start.row, modified);
			}
		});		

		
		
		
	})
</script>

<style>
#{$id}_aceeditor{
	margin:0;
}
.ace-changed {
	border-left: #FCAF3E 3px solid;
}
</style>
