{include "default_open_clean.tpl"}

{d::ldump($subscriber)}

Noriu pakeisti naujienu grupes

{foreach $options.groups as $item}
	<li>{$item} <input type="checkbox" ></li>
{/foreach}

Noriu atsisakyti
	iveskite el pasto adresa

{include "default_close.tpl"}