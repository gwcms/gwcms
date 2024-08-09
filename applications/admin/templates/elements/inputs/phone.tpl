
        

<div class="iti">
           
{*text line or password*}
<input id="{$id}"
	class="form-control{if $class} {$class}{/if} inp-{$type|default:text}"
	{if $required}required="required"{/if} 
	name="{$input_name}" 
	type="tel" 
	value="{$value|escape}" 
	onchange="this.value=$.trim(this.value);" 
	{if $readonly}readonly{/if}
	{if $maxlength}maxlength="{$maxlength}"{/if} 
	style="width: {$width|default:"100%"}; {if $height}height:{$height};{/if}" 
	{if $hidden_note}title="{$hidden_note}"{/if} 
	{if $placeholder}placeholder="{$placeholder}"{/if} 
	 {foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach}
	{$input_extra_params}
/>
        



</div>

			
{if !$input_international_phone_loaded}

		<link href="{$app->sys_base}vendor/international-telephone-input-master/international-telephone-input.css" media="all" rel="stylesheet" type="text/css" />

		<script src="{$app->sys_base}vendor/international-telephone-input-master/international-telephone-input.js" type="text/javascript"></script>
		<script>
			
		intlTelInput.preferredCountries= ["lt"]
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
						
			
			
			function intPhoneValid(valid)
			{
				{$validactions}
			}
			

			
			
			require(['gwcms'], function(){
				intlTelInputInit(document.querySelectorAll('input[type=tel]'));
				$('.input[type=tel]').keyup();
			})
		</script>



		<style>

			ul {
			    top: 100% !important;
			    transform: translateY(0%) !important;
			}

			.iti {
			    position: relative;
			    display: inline-block;
			}

			input[type=tel] {
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
		
		{assign scope=global var=input_international_phone_loaded value=1}
{/if}



