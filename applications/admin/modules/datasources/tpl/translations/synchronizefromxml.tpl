{include "default_open.tpl"}

{if count($changes)}


	<table class="changetable gwTable">
		<tr>
			<th>Changes found: {count($changes)}</th>
			<th>Old value</th>
			<th>New value</th>
		</tr>
	{foreach $changes as $key => $change}
		<tr>
			<td>{$key}</td>
			<td><div class="changesarea" contentEditable="true">{$change.old|escape}</div></td>
			<td><div class="changesarea" contentEditable="true">{$change.new|escape}</div></td>
		</tr>
	{/foreach}

	</table>


	<br />

	<a class="btn btn-primary" href="{$app->buildUri(false,[commit=>1])}">Commit changes</a>
{else}
	No changes found
{/if}


<br /><br />

<table class="resulttable gwTable">
	<tr><th colspan="2">Sync info:</th></tr>
	
{foreach $results as $key => $value}
	<tr>
		<td class="resultkey">{$key}</td>
		<td><div class="resultval">{$value}</div></td>
	</tr>
{/foreach}

</table>


<style>
	.changesarea {
		display: inline-block;
		border: solid 1px silver;
		max-height: 200px;
		padding: 1px 5px 1px 5px;;
	}
	.resulttable{ width: 1000px; }
	.resultkey{ white-space: nowrap; padding: 2px 10px 2px 10px; }
	.resulttable .resultval{ 
		max-height: 150px; 
		width: 100%;
		overflow: auto;
		white-space: pre;
		
	}
</style>

{include "default_close.tpl"}