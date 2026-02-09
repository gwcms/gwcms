
<table style="width:100%">
	<tr>
		<td style="width:80%">
{include file="{$smarty.current_dir}/text.tpl"}
		</td>
		<td style="width:20%">
			<button type="button" id="{$id}_browse" style="float:right">
				<span class="material-symbols-outlined">folder_open</span>
			      </button>
		</td>
		</tr>
</table>


<script>
	require(['gwcms'], function(){
	  $('#{$id}_browse').on('click', function(e){
	    e.preventDefault();

		var currentVal = $('#{$id}').val() || '';
		var url = '/admin/{$ln}/sitemap/repository/fileselect'
					+ '?browsereturn=' + encodeURIComponent('{$id}')
				{if $filetype==image} + '&type=image'{/if}
				{if $abspath} + '&abspath=1'{/if}
					+ '&start=' + encodeURIComponent(currentVal);
				window.open(
					url,
					'gw_repo_browse',
					'width=800,height=650,left=100,top=80,resizable=yes,scrollbars=yes'
					);
			});
		});
</script>