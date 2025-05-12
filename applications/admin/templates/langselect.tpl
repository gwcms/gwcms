<!--Language selector-->
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

{if count(GW::s('ADMIN/LANGS')) > 1}
<li class="dropdown langselector" style="">
	<a class="lang-selector dropdown-toggle" href="#" data-toggle="dropdown">
		<span class="lang-selected">
			
			<img class="lang-flag" src="{GW::s("STATIC_EXTERNAL_ASSETS")}flags/png/{$ln}.png" alt="{GW::s("LANG_NAMES/{$ln}")}" title="{GW::s("LANG_NAMES/{$ln}")}" style="max-height:24px;max-width:24px">
			<span class="lang-id">{strtoupper($ln)}</span>
			<span class="lang-name">{GW::s("LANG_NAMES/{$ln}")}</span>
		</span>
	</a>


	<!--Language selector menu-->
	<ul class="head-list dropdown-menu">
		{$xpld=explode('/',$smarty.server.REQUEST_URI,4)}
		{if $xpld[1]==admin}
			{$x=array_shift($xpld)}
		{/if}
		{$x=array_shift($xpld)}{$x=array_shift($xpld)}
		{$path=implode('/', $xpld)}
		
		{foreach GW::s('ADMIN/LANGS') as $ln_code}
			

			<li>
				{*https://www.iconfinder.com/iconsets/195-flat-flag-psd-icons*}
				<a href="{$app_base}{$ln_code}/{$path}" class="{if $ln_code == $ln}active{/if}">
					<img class="lang-flag" src="{GW::s("STATIC_EXTERNAL_ASSETS")}flags/png/{$ln_code}.png" alt="{GW::s("LANG_NAMES/{$ln_code}")}" style="max-height:24px;max-width:24px">
					<span class="lang-id">{strtoupper($ln_code)}</span>
					<span class="lang-name">{GW::s("LANG_NAMES/{$ln_code}")}</span>
				</a>
			</li>
		{/foreach}
			
		{if GW::s('i18nExt')}
			<li><center><small class='text-muted' style='font-size:9px'>&#8212;&#8212;&#8212;&nbsp;{GW::ln('/g/I18N_EXTEND_LANGS')}&nbsp;&#8212;&#8212;&#8212;</small></center></li>
			
			{foreach GW::s('i18nExt') as $ln_code}

				<li>
						{if $app->user->i18next_lns[$ln_code]}
							{$ico="fa-check-square-o"}
							{$swstate=0}
						{else}
							{$ico="fa-square-o"}
							{$swstate=1}							
						{/if}					
						
					<a href="{$app->buildUri("users/profile",[act=>doSetI18nExtState,ln=>$ln_code,state=>$swstate])}">
						
						
						<img class="lang-flag" src="{GW::s("STATIC_EXTERNAL_ASSETS")}flags/png/{$ln_code}.png" alt="{GW::s("LANG_NAMES/{$ln_code}")}" style="max-height:16px;max-width:16px">
						<span class="lang-id">{strtoupper($ln_code)}</span>
						<span class="lang-name"><i class="fa {$ico}" aria-hidden="true"></i> {GW::s("LANG_NAMES/{$ln_code}")}</span>
					</a>
				</li>
			{/foreach}
		{/if}
							
			
	</ul>
	
	

</li>
{/if}
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<!--End language selector-->
