{$id=rand(0,99999999999)}
<div id="carousel-{$id}" class="carousel slide carousel-fade" data-ride="carousel" data-interval="5000" data-pause="hover" data-wrap="true">
	<a class="carousel-control-prev" href="#carousel-{$id}" role="button" data-slide="prev">
		<span class="carousel-control-prev-icon" aria-hidden="true">&nbsp;</span>

	</a><a class="carousel-control-next" href="#carousel-{$id}" role="button" data-slide="next">
		<span class="carousel-control-next-icon" aria-hidden="true">&nbsp;</span>

	</a>
	<div class="carousel-inner">
		{foreach $gallery as $im}
			{$img=$im->image}
			{$imurl="{$app_base}tools/img/{$img->key}&v={$img->v}&size={$size|default:'800x800'}{$imagecustom}"}		  
			<div class="carousel-item {if $im@first}active{/if}" style="{if $maxheight}max-height:{$maxheight};overflow:hidden{/if}">
				<img class="d-block w-100" src="{$imurl}">
			</div>
		{/foreach}

	</div>
	<ol class="carousel-indicators">
		{$cnt=0}
		
		{foreach $gallery as $im}
			<li data-target="#carousel-{$id}" data-slide-to="{$cnt}" class="{if $im@first}active{/if}"></li>								
				{$cnt=$cnt+1}
		{/foreach}
	</ol>
</div>
