{if $m->list_params['page_by']}
{php}
	$vars = FH::getTplVars($template, Array('m','query_info','app'));
	
	$params =& $vars['m']->list_params;

	$current=(int)$params['page'] ? (int)$params['page'] : 1;
	$length=ceil($vars['query_info']['item_count'] / $params['page_by']);

	if($length<2)
		return;
		
	$template->assign('paging', Array
	(
		'current'=>$current,
		'length'=>$length,
		'first'=> $current < 2 ? 0 : 1,
		'prev'=>  $current <= 2 ? 0 : $current-1,
		'next'=>  $current >= $length-1 ? 0 : $current+1,
		'last'=>  $current >= $length   ? 0 : $length,
	));	
{/php}

{if $paging.length > 1}
	
{assign var="paging_tpl_page_count" value=$paging.length scope=parent}	

<table class="fontsz10 gw_clean_tbl" cellspacing="" cellpadding="1">
	<tr>
		<form method="get" action="{$smarty.server.REQUEST_URI}" style="display:inline">
		<td class="fontsz5">{$lang.PAGE}:</td>
		
		{if $paging.length <= 10}
		<td nowrap>
			{for $i=1;$i<=$paging.length;$i++}
				{if $i==$paging.current}
					<b>{$i}</b>
				{else}
					<a href="#{$i}" onclick="return gw_adm_sys.change_page({$i})">{$i}</a>
				{/if}
				
			{/for}
		</td>
		{else}
		<td nowrap>
			{if $paging.first}<a href="#{$paging.first}" onclick="return gw_adm_sys.change_page(1)">1</a>{/if}
			{if $paging.prev}<a href="#{$paging.prev}" onclick="return gw_adm_sys.change_page({$paging.prev})">{$paging.prev}</a>{/if}
		</td>
		
		<td nowrap>
			{if $paging.length > 50}
				<input name="list_params[page]" value="{$paging.current}" style="width:40px" />
			{else}
			<select id="list_paging" name="list_params[page]" onchange="this.form.submit()"></select> 
			<script>
				gw_adm_sys.paging_select_box($('#list_paging'),1,{$paging.length});
				$('#list_paging').val({$paging.current|default:1});
			</script>
			{/if}
		</td>
		</form>
		<td nowrap>
			{if $paging.next}<a href="#{$paging.next}" onclick="return gw_adm_sys.change_page({$paging.next})">{$paging.next}</a>{/if}
			{if $paging.last}<a href="#{$paging.last}" onclick="return gw_adm_sys.change_page({$paging.last})">{$paging.last}</a>{/if}
		</td>
		
		{/if}
	</tr>
</table>
{/if}
{/if}