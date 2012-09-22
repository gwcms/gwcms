{include file="header.tpl"}
<body>
{include file="top_meny.tpl"}
{include file="categori_meny.tpl"}

<div class="warp">
	<div class="contentbg_top"></div>
	
	<div class="contentbg_mid">
        <div class="overskrift">Kontakt oss</div>
        {include file="messages.tpl"}<br />
        
        <div class="content_contact">
        <form name="form" id="form">
        <fieldset>
        <legend>Send oss en mail</legend>
        <input type="hidden" name="act" value="do:send" />
        <p>
        	<label>Velg en kategori:</label>
        	<select name="subject">
    			<option {if $subject|lower == $m->lang.tech|lower}selected="selected"{/if}>{$m->lang.tech}</option>
    			<option {if $subject|lower == $m->lang.order|lower}selected="selected"{/if}>{$m->lang.order}</option>
    			<option {if $subject|lower == $m->lang.invoice|lower}selected="selected"{/if}>{$m->lang.invoice}</option>
                <option {if $subject|lower == $m->lang.other|lower}selected="selected"{/if}>{$m->lang.other}</option>
                <option {if $subject|lower == $m->lang.bug|lower}selected="selected"{/if}>{$m->lang.bug}</option>
   			</select>
        </p>
        
        <p>
        	<label>Navn:</label>
        	<input class="registrering_tekstfelt" name="name" type="text" {if $user} value="{$user->first_name} {$user->second_name}"{/if} />
        </p>
        <p>
        	<label>Epost:</label>
        	<input class="registrering_tekstfelt" name="email" type="text" {if $user} value="{$user->email}"{/if} />
        </p> 
        <p>
        	<label>Emne:</label>
        	<input class="registrering_tekstfelt" name="new_subject" type="text" maxlength="40" />
        </p> 
        <p>
        	<label>Melding:</label>
        	<textarea name="message" cols="50" rows="10"></textarea>
        </p>
        <p><label></label><input id="signin_submit" name="" type="submit" value="Send" /></p>

        </fieldset>
		</form>
    
    </div>
	</div>
	<div class="contentbg_bot"></div>
</div>

{include file="footer.tpl"}