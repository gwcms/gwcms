
{if $paging.length > 1}
	{for $i=1; $i<=$paging.length; $i++}
		<a href="{Navigator::buildURI(false, [page=>$i])}">
		
			{if $i==$paging.current}
				<b>{$i}</b>
			{else}
				{$i}
			{/if}
		</a>
	{/for}
{/if}