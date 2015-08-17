<div class="change_lang" style="margin-bottom:20px;text-align:right">
{$lang.LANGUAGE}: 

			{$curr_lang=$smarty.get.lang|default:GW::$settings.LANGS.0}
			
			{foreach GW::$settings.LANGS as $ln_code}
				{if $ln_code==$curr_lang}{$tag_params=[class=>selected]}{else}{$tag_params=''}{/if}
				{gw_link params=[lang=>$ln_code] title=$lang.LANG.$ln_code tag_params=$tag_params}
			{/foreach}
</div>

