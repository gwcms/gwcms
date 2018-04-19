

	{capture append="footer_hidden"}
		<style>
			.input_label_td{ width: 10% }
			.input_td{ width:auto }
			.i18nhidden{ width:1px }
			.gw_switch_ln{ cursor: pointer; }
		</style>

		<link type="text/css" href="{$app_root}static/css/flags.css" rel="stylesheet" />
		
		<script>
				function tooglei18nCol(ln_code)
				{

					$('.col_i18n_'+ln_code).children().toggle();
					$('.col_i18n_'+ln_code).toggleClass('i18nhidden');
					$('.toggle_i18n_'+ln_code).toggle();


				}
				
			require(['gwcms'],function(){
				{if $hide_non_primary}
				{foreach $langs as $ln_code}
					{if $idx!=0}
						tooglei18nCol('{$ln_code}');
					{/if}
				{/foreach}
				{/if}
			})
		</script>
	{/capture}

<tr>
	<td>
		
	</td>
	{foreach $langs as $ln_code}
		{if $ln_code=='en'}{$flag_code='gb'}{else}{$flag_code=$ln_code}{/if}
		<td>
			<span class="gw_switch_ln" href="#" onclick="tooglei18nCol('{$ln_code}');return false">
				<img src="{$app_root}static/img/blank.gif" class="flag flag-{$flag_code}" alt="{$ln_code}" /> <span class="toggle_i18n_{$ln_code}">{GW::l("/g/LANG/`$ln_code`")}</span>
			</span>
		</td>	
	{/foreach}
</tr>