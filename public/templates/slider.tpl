{literal}

    <script type="text/javascript" charset="utf-8">
        window.onload = function () {
            var container = $('div.sliderGallery');
            var ul = $('ul', container);
            
            var itemsWidth = ul.innerWidth() - container.outerWidth();
            
            $('.slider', container).slider({
                min: 0,
                max: itemsWidth,
                handle: '.handle',
                stop: function (event, ui) {
                    ul.animate({'left' : ui.value * -1}, 100);
                },
                slide: function (event, ui) {
                    ul.css('left', ui.value * -1);
                }
            });
        };
    </script>
{/literal}
<div id="container">
	<div class="sliderGallery">
		<ul style="left: 0px;"> 
			{include_php file="modules/slider.php"}
			{$output}
		</ul>
		<div class="slider ui-slider">
			<div class="handle" style="left: 0px;"></div>
			<div class="slider-text"></div>
		</div>
	</div>
</div>
</div>
	<div id="ui-datepicker-div" style="display: none;"></div>
	<div class="contentbg_bot"></div>

<div class="contentbg_top"></div>
<div class="contentbg_mid">
	{if isset($request->path_arr[2])}
		<div class="content">
		{if isset($activeProduct)}
		<div class="fargevalg">
			{foreach $colorSerie as $color}
    			{$img = $color->image}
    			<a href="{$request->ln}/{$request->path_arr[1]['path_clean']}/{$color->id}"><img src="tools/img.php?id={$img->key}&width=60&height=60" title="{$color->color}" alt="{$color->color}"/></a>
    		{/foreach}
		</div>
		<div class="pakkeserie">
    
    		<div class="pakke_overskrift">Produktserie</div>
    
    		<div class="pakke_innhold">
    
    		<table class="pakke_tekst" align="center" border="0" cellspacing="0" cellpadding="0">
    		<tr>
    		{foreach $productSerie as $prod}
    			{$img = $prod->image}
    			<td><a href="{$request->ln}/{$request->path_arr[1]['path_clean']}/{$prod->id}"><img src="tools/img.php?id={$img->key}&width=60&height=60" {if $prod->type_title == "Bordkort"}style="padding-top: 20px;"{/if}/></a></td>
    		{/foreach}
 			</tr>
  			<tr>
    			{foreach $productSerie as $prod}
    			<td><a href="{$request->ln}/{$request->path_arr[1]['path_clean']}/{$prod->id}">{$prod->type_title}</a></td>
    		{/foreach}
  			</tr>
			</table>
    		</div>
    	</div>
		{$activeProdInfo=$activeProduct->getInfo()}
    	<div class="produktinformasjon">
				<div class="produktinformasjon_bilde"><img src="tools/img.php?id={$activeProduct->image->key}" alt="{$activeProduct->desc}" title="{$activeProduct->desc}" {if $activeProdInfo->type_title == "Bordkort"}style="padding-top: 50px;"{/if}/></div> 
				<div class="produktinformasjon_text_overskrift">{$activeProdInfo->type_title}</div>
				<div class="produktinformasjon_text_informasjon">
					• Format: {$activeProdInfo->width}x{$activeProdInfo->height}mm<br />
					• Papir tykkelse: {$activeProdInfo->paperSize}<br />
					• Papir type: {$activeProdInfo->paperType} <br />
					• Fargeprint: {if $activeProdInfo->fullcolor == 1}Ja{else}Nei{/if}<br />
					• Sider: {if $activeProdInfo->folded == 1}4{else}2{/if}<br />
					{if $activeProdInfo->envelope == 1}• Konvolutt: Ja{/if}</p>
				</div>
				{if $activeProdInfo->red_price == 0}
					<div class="produktinformasjon_text_pris">{$finalPrice = $activeProdInfo->price + $activeProdInfo->mod_price}{$finalPrice}</div>
				{else}
					<div class="produktinformasjon_text_pris_f">Før:{$finalPrice = $activeProdInfo->price + $activeProdInfo->mod_price}{$finalPrice}</div>
					<div class="produktinformasjon_text_pris">{$finalPrice = $activeProdInfo->price + $activeProdInfo->mod_price - $activeProdInfo->red_price}{$finalPrice}</div>
				{/if}
				<div class="produktinformasjon_text_pris2">kr/stk</div>
				<div class="produktinformasjon_knapp_valg">
					<div class="awesome"><a href="{$request->ln}/{$request->path_arr[1]['path_clean']}/{$activeProduct->id}/edit">Velg denne</a></div>
				</div>
				
		</div>
		{else}
		Produkt eksisterer ikke.
		{/if}
		</div>
	{else}
		{$page->getContent("forsidecontent")}  
	{/if} 	
</div>
