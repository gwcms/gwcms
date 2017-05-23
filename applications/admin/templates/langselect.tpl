<!--Language selector-->
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
{if count(GW::s('ADMIN/LANGS')) > 1}
<li class="dropdown langselector" style="">
	<a class="lang-selector dropdown-toggle" href="#" data-toggle="dropdown">
		<span class="lang-selected">
			<img class="lang-flag" src="{$app_root}static/img/flags/{$ln}.png" alt="{GW::l("/g/LANG/`$ln`")}" title="{GW::l("/g/LANG/`$ln`")}" style="max-height:24px;max-width:24px">
			<span class="lang-id">{$ln|strtoupper}</span>
			<span class="lang-name">{GW::l("/g/LANG/`$ln`")}</span>
		</span>
	</a>


	<!--Language selector menu-->
	<ul class="head-list dropdown-menu">
		
			{foreach GW::s('ADMIN/LANGS') as $ln_code}
				
				<li>
					{*https://www.iconfinder.com/iconsets/195-flat-flag-psd-icons*}
					<a href="{$app_base}{$ln_code}" class="{if $ln_code == $ln}active{/if}">
						<img class="lang-flag" src="{$app_root}static/img/flags/{$ln_code}.png" alt="{GW::l("/g/LANG/`$ln_code`")}" style="max-height:24px;max-width:24px">
						<span class="lang-id">{$ln_code|strtoupper}</span>
						<span class="lang-name">{GW::l("/g/LANG/`$ln_code`")}</span>
					</a>
				</li>
			{/foreach}
	</ul>

</li>
{/if}
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<!--End language selector-->
