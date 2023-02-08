{* bootrap-multiselect https://select2.github.io/examples.html *}
{*if !$gwcmssite_input_select2_loaded2*}	
	{*kazkas buvo isjunges*}
	<script type="text/javascript" src="{$app->sys_base}vendor/select2/full.js?v={GW::$globals.version_short}"></script>
	<link rel="stylesheet" href="{$app->sys_base}vendor/select2/css.css?v={GW::$globals.version_short}" type="text/css"/>
	{assign var=$gwcmssite_input_select2_loaded2 value=1 scope=global}	
{*/if*}	
	{if $unify2}
	{*notcompatible with select_ajax*}
	  <link rel="stylesheet" href="{$assets}css/unify-components2_select2.css">
	  {/if}

<div>
	
{if $placeholder}	
	<script type="text/javascript">
		$(document).ready(function() {	
			$('#{$id}').one('select2:open', function(e) {
				
			    $('input.select2-search__field').prop('placeholder', "{$placeholder}");
			    
			});
		
		});
	</script>	
	
{/if}
	


	{*
	moved to head section (jquery_ui_widgets)
	<sscript src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
	*}

	
{if !$gwcmssite_input_select2_loaded1}	
	<script type="text/javascript">
		{*-1 to select other*}
		function matchCustom(params, data) {
		    // If there are no search terms, return all of the data
		    if ($.trim(params.term) === '') {
		      return data;
		    }

		    // Do not display the item if there is no 'text' property
		    if (typeof data.text === 'undefined') {
		      return null;
		    }

		    // `params.term` should be the term that is used for searching
		    // `data.text` is the text that is displayed for the data object
		    if (data.text.toUpperCase().indexOf(params.term.toUpperCase()) > -1) {
		      //var modifiedData = $.extend({}, data, true);
		      //modifiedData.text += ' (matched)';

		      // You can return modified objects from here
		      // This includes matching the `children` how you want in nested data sets
		      //return modifiedData;
		      return data;
		    }
		    if(data.id==-1)
			   return data;

		    // Return `null` if the term should not be displayed
		    return null;
		}
		
		$(document).ready(function() {	
			
		
			$(".gwselect2").select2({
				matcher: matchCustom				
			 });
		
		});
	</script>
	
	{assign var=gwcmssite_input_select2_loaded1 value=1 scope=global}	
{/if}

	

{include file="inputs/input_select.tpl" addclass="`$addclass` gwselect2"}
</div>


<style>
	
	.select2-container{ z-index: 999; width: 100% !important }
	.select2-selection__arrow{ top: 20px !important; }
	.select2-container--default .select2-selection--single .select2-selection__arrow b::before {
		content: "" !important;
	}
	
	.select2-selection__rendered {
		line-height: 100% !important;
	}
	
</style>