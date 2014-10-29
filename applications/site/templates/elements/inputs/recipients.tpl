


{$options=["--- RECENT ---"=>$numbers_recent, "--- CONTACTS ---"=>$numbers_contacts]}


{$options=[''=>'-- Pasirinkite --'] + $options}




<select onchange="$(this).next().val(this.value);">
{html_options options=$options}
</select>


{include "elements/inputs/text.tpl"}
    			
