{html_radios name=$input_name selected=$value options=$options separator=$separator|default:'<br />'}

<style>
	input[type='radio']{ margin-left:3px;margin-right:3px;position:relative;top:2px; } 
</style>



{if $onchangeFunc}
	{capture append=footer_hidden}
	<script type="text/javascript">
	require(['gwcms'], function(){  
	
		$('input[type=radio][name="{$input_name}"]').change(function() {
			{$onchangeFunc}(this.value, this);
		});

	})
	</script>

	{/capture}
{/if}