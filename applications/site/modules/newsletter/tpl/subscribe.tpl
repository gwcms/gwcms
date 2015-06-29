{include "default_open_clean.tpl"}


<form action="{$smarty.server.REQUEST_URI}" method="post">
	<input type="hidden" name="act" value="do:subscribe" />
	<input type="hidden" name="id" value="{$subscriber->id}" />

<fieldset  id="csubscribed">
	<legend><input type="radio" class="susbcribe_inp" id="subscribed" name="unsubscribed" value="0" {if !$subscriber->unsubscribed}checked="checked"{/if}/> 
		{if $subscriber->unsubscribed}Užsisakyti{else}Pasirinkti naujienų grupes{/if}
	</legend>

	Naujienų grupės:
	
	<ul style="list-style-type: none;">
	{foreach $options.groups as $id => $item}
		<li><input type="checkbox" name="groups[]" value="{$id}" {if isset($selected_groups.$id)}checked="checked"{/if} /> {$item} </li>
	{/foreach}
	</ul>
	
	<div class="expandifactive" style="display:none">
	<input type="submit" value="Saugoti" />
	</div>
</fieldset>


<fieldset id="cunsubscribed">
	<legend><input type="radio" class="susbcribe_inp"  name="unsubscribed" value="1" {if $subscriber->unsubscribed}checked="checked"{/if}/> Atsisakyti</legend>
	
	{if $subscriber->unsubscribed}
		Jūs esate atsisakę, pasirinkite užsisakyti jei norite gauti jum aktualias naujienas
	{else}
		Jūs esate užsisakę
		
		<div class="expandifactive" style="display:none">
		<br />
			<input type="checkbox" name="unsubscribe_confirm" required="required" />
			Nebenoriu gauti daugiau naujienlaiškių į <b>{$subscriber->email}</b>
			
			
			<br /><br />
			<input type="submit" value="Išsaugoti pasirinkimą" />
		</div>
	{/if}
</fieldset>
	

</form>
	
<style>
	.subscr_block_passive{ color: silver }
</style>
<script>
	$('.susbcribe_inp').change(function(){
		var state = $('#subscribed').prop('checked');
		$('#csubscribed').toggleClass('subscr_block_passive',!state)
		$('#cunsubscribed').toggleClass('subscr_block_passive', state)
		$('#csubscribed input[type=checkbox]').prop('disabled', !state);
		$('#csubscribed .expandifactive').toggle(state)
		$('#cunsubscribed .expandifactive').toggle(!state)
		
		$('#cunsubscribed input[name="unsubscribe_confirm"]').prop('required', !state);
		
		
	}).click(function(){ $(this).click() }).change();
</script>

{include "default_close.tpl"}