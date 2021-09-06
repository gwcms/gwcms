<div class="change_lang" style="margin-bottom:20px;text-align:right">
{GW::ln('/g/LANGUAGE')}: 

			{$curr_lang=$smarty.get.lang|default:GW::$settings.LANGS.0}
			
			{foreach GW::$settings.LANGS as $ln_code}
				
				<a href="{$app->buildUri(false, [lang=>$ln_code]+$smarty.get)}"
				   {if $ln_code==$curr_lang}class="selected"{/if}
				   >{GW::ln("/g/LANG/`$ln_code`")}</a>
				
			{/foreach}
</div>

