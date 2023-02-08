
<div id="{$id}" class="changesarea" contentEditable="true">{$value}</div>
<textarea id="{$id}_ta"  style="display:none" name="{$input_name}">{$value|escape}</textarea>
<script>
	require(['gwcms'], function(){


		var elm = $('#{$id}').keydown(function(){
			$('#{$id}_ta').val($(this).html());
		}).keypress(function(){
			$(this).keydown();
		}).keyup(function(){ $(this).keydown(); })

	})

</script>


{if !isset(GW::$globals.html_inp_autoresize)}
	{GW::$globals.html_inp_autoresize=1}


<style>
	.changesarea {
		display: inline-block;
		border: solid 1px silver;
		max-height: 200px;
		min-width: 200px;
		max-width: 80vw;
		overflow: auto;
		padding: 2px 5px 2px 5px;;
		margin: 1px;
	}
</style>

{/if}


