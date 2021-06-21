{$dfield="`$field`_data"}
{$dinput_name=$input_name_pattern|sprintf:$dfield}
{$did=str_replace(["[","]"],'_',$dinput_name)}
	
{include file="inputs/input_text.tpl" type=text other_tags=["data-store-details"=>'']} 
<span id="location_err"><i class="icon-warning-sign"></i></span>


<input type="hidden" name="{$dinput_name}" id="{$did}" value="{$item->$dfield|escape}" />


{if !$input_lautocomplete_loaded}
	{*
	<link href="{$app->sys_base}/vendor/bootstrap-ajax-typeahead/demo/assets/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" />
	*}
	
	<script src="https://maps.googleapis.com/maps/api/js?sensor=true&amp;libraries=places&key=AIzaSyCfGLFtH9BUdS5l1lTebTa26iT8pYL5Wck&address=Dallas" type="text/javascript"></script>
	<script src="{$app_root}assets/js/location_autocomplete.js"></script> 


	{assign scope=global var=input_lautocomplete_loaded value=1}
	

{/if}		
<script type="text/javascript">

$(document).ready(function() {
	initLocationInput('#{$id}','#{$did}')
});

</script>