{include "default_open.tpl"}	

<form action="{$smarty.server.REQUEST_URI}">
	<input name="text" placeholder="text1" value="{$smarty.get.text|escape}">
	<input name="text2" placeholder="text2" value="{$smarty.get.text2|escape}">
	<input name="fontsize" placeholder="fontsize" value="{$smarty.get.fontsize|default:5}">
	<input type="submit" />
</form>

	<br />
	
		<div class="row">
			{$i=0}
{foreach $fonts as $font}
	{$i=$i+1}
	
	<div class="col-md-3 imgcont">
		
	<img src="{$app_base}tools/favico?text={$smarty.get.text|default:"GW"}{if $smarty.get.text2}&text2={$smarty.get.text2}{/if}&fs={$smarty.get.fontsize|default:5}&font={$font}&nocache=1" style="width:16px;height:16px;background-color:white" />
	
	{$font}
	
	<span class="dim"></span>
	</div>
	
	{if $i==11}
		</div><div class="row">
	{/if}
	
{/foreach}
</div>
	<script>
		$(function(){
		
		$('.imgcont').each(function(){
			var im = $(this).find('img').get(0)
			$(this).find('span').text(im.height + 'x'+ im.width);
			
			if(im.height <=16 && im.width<= 16)
				$(this).find('span').css({ color: 'green' })
			
		})
		})
	</script>
	<style>
		.imgcont img{ border:0px solid gray }
	</style>

{include "default_close.tpl"}