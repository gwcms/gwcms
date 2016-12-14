
<li>
	{list_item_action_m url=[false,[act=>doGetImdbInfo,id=>$item->id]] iconclass="fa fa-info" confirm=1 caption="imdbinfo"}
</li>


{$imdb = json_decode($item->imdb)}
{if $imdb}
	<table>
{foreach $imdb as $key => $val}
	<tr><th>{$key}</th><td style="width:200px">{$val}</td></tr>
{/foreach}
	</table>
{/if}
