{if $readonly}
	{foreach $value as $selected}
		{$options[$selected]}{if !$selected@last},{/if}
	{/foreach}
{else}
	

<select  id="{$id}" {if $maximumSelectionLength>1}multiple="multiple"{/if} class="form-control " name="{$input_name}" 
		style="width: {$width|default:"100%"}; {if $height}height:{$height};{/if}"
		>
	{html_options options=$options selected=$value}
</select>

	
{if !$gwcms_input_select2_loaded}
	{$m->addIncludes("bs/select2css", 'css', "`$app_root`static/vendor/select2/css.css")}
	{assign var=gwcms_input_select2_loaded value=1 scope=global}		
{/if}	

	{capture append=footer_hidden}
	<script type="text/javascript">
		require(['vendor/select2/js'], function(){ 
			//$('.gwselect2').select2(); 
			
			
		{if $datasource}
				
    function formatRepo (item) {
      if (item.loading) return item.text;

      var markup = "<div class='select2-result-repository clearfix'>" +
        "<div class='select2-result-repository__meta'>" +
          "<div class='select2-result-repository__title'>" + item.title + "</div>";

      "</div>" +
      "</div></div>";

      return markup;
    }
	
    function formatRepoSelection (item) {
      return item.title || item.text;
    }				
				
$("#{$id}").select2({
  ajax: {
	{if $maximumSelectionLength>0}maximumSelectionLength: {$maximumSelectionLength},  {/if}	  
    url: "{$datasource}",
    dataType: 'json',
    delay: 250,
    data: function (params) {
      return {
        q: params.term, // search term
        page: params.page
      };
    },
    processResults: function (data, params) {
      // parse the results into the format expected by Select2
      // since we are using custom formatting functions we do not need to
      // alter the remote JSON data, except to indicate that infinite
      // scrolling can be used
      params.page = params.page || 1;

      return {
        results: data.items,
        pagination: {
          more: (params.page * 30) < data.total_count
        }
      };
    },
    cache: true
  },
  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
  minimumInputLength: 1,
  templateResult: formatRepo, // omitted for brevity, see the source of this page
  templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
  
});				
				
				
		{/if}
			
		});
	</script>
	{/capture}
	

	


{/if}