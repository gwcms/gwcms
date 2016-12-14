
<li>
	{list_item_action_m url=[false,[act=>doGetImdbInfo,id=>$item->id]] iconclass="fa fa-info" confirm=1 caption="imdbinfo"}
</li>


{$imdb = json_decode($item->imdb)}
{if $imdb}
	
	<table style="max-width:500px">
{foreach $imdb as $key => $val}
	<tr><th>{$key}</th><td style="white-space: normal">{$val}</td></tr>
{/foreach}
	</table>

{/if}
