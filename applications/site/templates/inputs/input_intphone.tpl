        

<div class="iti">
           
        

<input 
	name="{$input_name}" 
	type="tel"
	class="form-control has-danger {if $required} required{/if} {if $addclass} {$addclass}{/if}"
	id="{$id}" 
	value="{if $value!=0}{if $value && strpos($value,'+')===false}+{/if}{$value|escape}{/if}" 
	{if $required}required="1"{/if} 
	{if $placeholder}placeholder="{$placeholder|escape}"{/if}

	{if $readonly}disabled='disabled'{/if}
	{foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach}
>

</div>

{if $onchange}<script> $(function(){ $('#{$id}').change(); }) </script>{/if}

{if $m->error_fields.$field}<span class="glyphicon glyphicon-remove form-control-feedback"></span>{/if}
			
{if !$input_international_phone_loaded}
	
	{$asssets=GW::s("STATIC_EXTERNAL_ASSETS")} {*{$app->sys_base}vendor*}
		<link href="{$asssets}international-telephone-input-master/international-telephone-input.css" media="all" rel="stylesheet" type="text/css" />

		<script src="{$asssets}international-telephone-input-master/international-telephone-input.js" type="text/javascript"></script>
		<script>
				
	{if $prefered_country}	
		intlTelInput.preferredCountries= ["{strtolower($prefered_country)}"]
	{else}
		{if $app->ln == 'lt'}
			intlTelInput.preferredCountries= ["lt"]
		{else}
			intlTelInput.preferredCountries= []
		{/if}
	{/if}
			
	{if $limit_country}
			allowed_countries = {json_encode($limit_country)};
			
			allowed_countries = GW.array_flip(allowed_countries)
			console.log(allowed_countries)
			var tmp =intlTelInput.countries;
			intlTelInput.countries = [];
			
			for(var i in tmp){
				
				if(allowed_countries.hasOwnProperty(tmp[i].cca2))
					intlTelInput.countries.push(tmp[i])
			}
	{/if}
						
			intlTelInputInit(document.querySelectorAll('input[type=tel]'));
			
			function intPhoneValid(valid)
			{
				{$validactions}
			}
			
			$(function(){
				$('input[type=tel]').each(function(){					
					this.dispatchEvent(new CustomEvent("keyup", {  }));
				})
				
				setTimeout(function(){  $('input[type=tel]').each(function(){					
					this.dispatchEvent(new CustomEvent("keyup", {  }));
				})
				  }, 1000)
			})
		</script>



		{assign scope=global var=input_international_phone_loaded value=1}
{/if}



    <style>
	    
       .iti ul {
            top: 100% !important;
            transform: translateY(0%) !important;
        }

        .iti {
            position: relative;
            display: inline-block;
        }

        .iti input[type=tel] {
            padding-right: 6px;
            padding-left: 52px;
            margin-left: 0;

            position: relative;
            z-index: 0;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            margin-right: 0;
            border: 1px solid #CCC;
            width: 100%;
            height: 35px;
            /* padding: 6px 12px; */
            border-radius: 2px;
            font-family: inherit;
            font-size: 100%;
            color: inherit;
        }

	.has-danger{ border-color: #d9534f !important }

</style>	