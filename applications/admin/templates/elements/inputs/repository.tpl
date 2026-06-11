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

		// =============================
		// OPEN FILE BROWSER
		// =============================
		$('#{$id}_browse').on('click', function(e){
			e.preventDefault();

			var defaultDir = '{$defaultdir}' || '';
			var currentVal = $('#{$id}').val() || defaultDir;

			var url = '{if $testserver}https://{$smarty.server.HTTP_HOST}.1.voro.lt{/if}/admin/{$ln}/sitemap/repository/fileselect'
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

		// =============================
		// RECEIVE MESSAGE FROM POPUP
		// =============================
		window.addEventListener('message', function(event){

			// 🔐 allowed origin list (prisitaikyk jei reikia)
			var allowedOrigins = [
				window.location.origin,
				'https://{$smarty.server.HTTP_HOST}',
				'https://{$smarty.server.HTTP_HOST}.1.voro.lt'
			];

			if (allowedOrigins.indexOf(event.origin) === -1)
				return;

			var data = event.data || {};

			// tik mūsų message
			if (data.type !== 'repository_file_selected')
				return;

			// tik tam inputui
			if (data.browsereturn !== '{$id}')
				return;

			var el = document.getElementById('{$id}');
			if (!el)
				return;

			el.value = data.value ? '{if $testserver}https://{$smarty.server.HTTP_HOST}.1.voro.lt{/if}' + data.value : '';

			// trigger change
			try
			{
				el.dispatchEvent(new Event('input', { bubbles: true }));
				el.dispatchEvent(new Event('change', { bubbles: true }));
			}
			catch (e) {}

		});

	});
</script>
