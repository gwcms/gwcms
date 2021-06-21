{* bootrap-multiselect http://www.jqueryrain.com/?FusAX1FA *}
<script type="text/javascript" src="{$app->sys_base}vendor/bootstrap-multiselect/dist/js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="{$app->sys_base}vendor/bootstrap-multiselect/dist/css/bootstrap-multiselect.csss" type="text/css"/>


<div>

<script type="text/javascript">
    $(document).ready(function() {
	$('#{$id}').multiselect({
		enableCaseInsensitiveFiltering: true,
	    enableFiltering: true,
	//includeSelectAllOption: true,
	    maxHeight: 400,
	    numberDisplayed: 8
	});
    });
</script>


{include file="inputs/input_select.tpl" multiple=1}
</div>
