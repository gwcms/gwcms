{include "default_open.tpl"}

{*
<center>
<table class="did">

<tr>
<td valign="top">

<fieldset>
<legend>Registruotiems vartotojams</legend>

	<form action="{$smarty.server.REQUEST_URI}" method="post">
		<input type="hidden" name="act" value="do:login" />
		
		{if $errors}
			<p>{$errors}</p>
		{/if}

		<table >
		<tr>
			<td class="txt">Prisijungimo vardas:</td>
			<td class="fld"><input name="login[0]" value="{$login->name}" /></td>
		</tr>
		
		<tr>
			<td class="txt">Prisijungimo slaptaÅ¾odis:</td>
			<td class="fld"><input name="login[1]" value="{$login->pass}" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input id="buylink"  type="submit" value="Prisijungti"></td>
		</tr>
		</table>

	</form>

</fieldset>

</td>
<td valign="top">


<fieldset>
<legend>Naujiems vartotojams</legend>

<form action="{$ln}/{$page->path}/register">
	<input id="buylink"  type="submit" value="Registruotis">
</form>

</fieldset>



</fieldset>

<!-----------------------------PASSCHANGE-------------------------------------->
<fieldset>
<legend>PamirÅ¡ote slaptaÅ¾odÄ¯</legend>

	{if !$smarty.get.passchange}
		<input id="buylink" type="submit" value="SlaptaÅ¾odÅ¾io priminimas" onclick="$(this).hide();$('#passchange').fadeIn();" />
	{/if}
	
<form 
	action="{$ln}/{$page->path}?passchange=1" 
	method="post" 
	id="passchange" 
	{if !$smarty.get.passchange}style="display:none"{/if}>
	<input type="hidden" name="act" value="do:passchange" />
	<table>
		<tr>
			<td>El. paÅ¡to adresas</td>
			<td>
				<input 
					{if $smarty.get.passchange}class="error_field"{/if} 
					name="email" 
					value="{$smarty.post.email|escape}" 
					placeholder="@" />
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="SiÅ³sti"></td>		
		</tr>
	</table>
</form>

</fieldset>
<!-----------------------------PASSCHANGE-------------------------------------->


</td>

</tr>
</table>
</center>

*}

<div class="container">
	
	{include "messages.tpl"}
	
    <div class="center-block logig-form">
        <div class="panel panel-primary">
            <div class="panel-heading">{GW::ln('/m/LOGIN_FORM')}</div>
            <div class="panel-body">
                <form role="form" action="{$smarty.server.REQUEST_URI}" method="post">
			<input type="hidden" name="act" value="do:login" />
			
                    <div class="form-group">
                        <div class="input-group login-input {if $smarty.get.error}has-error has-feedback{/if}">
                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                            <input  name="login[0]"  value="{$login->username|escape}" type="text" class="form-control" placeholder="{GW::ln('/m/USERNAME_EMAIL')}" required="1">
                        </div>
                        <br>
                        <div class="input-group login-input {if $smarty.get.error}has-error has-feedback{/if}">
                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                            <input  name="login[1]" type="password" class="form-control" placeholder="{GW::ln('/m/PASSWORD')}" required="1">
                        </div>
                        <div class="checkbox">
                            <input type="checkbox" id="checkbox_remember" name="login_auto" {if $login->auto}checked="checked"{/if}>
                            <label for="checkbox_remember">{GW::ln('/m/REMEMBER_ME')}</label>
                        </div>
                        <button type="submit" class="btn btn-ar btn-primary pull-right">{GW::ln('/m/LOGIN')}</button>
                        {*<a href="#" class="social-icon-ar sm twitter animated fadeInDown animation-delay-2"><i class="fa fa-twitter"></i></a>*}
                        <a href="#" class="social-icon-ar sm google-plus animated fadeInDown animation-delay-3"><i class="fa fa-google-plus"></i></a>
                        <a href="#" class="social-icon-ar sm facebook animated fadeInDown animation-delay-4"><i class="fa fa-facebook"></i></a>
                        <hr class="dotted margin-10">
                        <a href="#" class="btn btn-ar btn-success pull-right">{GW::ln('/m/CREATE_ACCOUNT')}</a>
                        <a href="#" class="btn btn-ar btn-warning">{GW::ln('/m/PASSWORD_RECOVERY')}</a>
                        <div class="clearfix"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> <!-- container  -->

{include "default_close.tpl"}