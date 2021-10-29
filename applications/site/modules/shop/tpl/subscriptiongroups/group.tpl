
{include "default_open.tpl"}
{include "product_display.tpl"}


{$link=$app->buildUri("direct/shop/subscriptiongroups/group/{FH::urlStr($item->title)}",[id=>$item->id])}
		


			
	<div class="cellim">
		{call name="product_image" product=$item size=$imsize crop=1}
	</div>

	{*background-image:url('applications/site/assets/img/{if $item->gender_limit=='f'}woman{else}man{/if}.svg')"*}

	<div class="cellcont" >
		<a href="{$link}" style="color:inherit;text-decoration:none">
		<h3>{$item->title}</h3>




		<div class="event_description">
			{$item->keyval->description|nl2br}
		</div>





	</div>

			
<br/>

{include "default_close.tpl"}

