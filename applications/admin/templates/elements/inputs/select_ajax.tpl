{if $readonly}
	{foreach $value as $selected}
		{$options[$selected]}{if !$selected@last},{/if}
	{/foreach}
{else}
	
{if $preload && $value}
	{if is_array($value)}
		{foreach $value as $valitm}
			{$options[$valitm]="`$valitm` {GW::l('/g/LOADING')}..."}
		{/foreach}
	{else}
		{$options[$value]="`$value` {GW::l('/g/LOADING')}..."}
	{/if}
{/if}
	


<select  id="{$id}" {if $maximumSelectionLength>1}multiple="multiple"{/if} class="form-control " name="{$input_name}{if $maximumSelectionLength>1 && substr($input_name,-2)!='[]'}[]{/if}" 
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
require(['vendor/select2/js'], function () {
	//$('.gwselect2').select2(); 


{if $datasource}
		
	function formatSelect2Result(item) {
		if (item.loading)
			return item.text;

		if(item.html)
			return item.html;
			
		var markup = "<div class='select2-result-repository clearfix'>" +
			"<div class='select2-result-repository__meta'>" +
			"<div class='select2-result-repository__title'>" + item.title + "</div>";
		
			if(item.hasOwnProperty('footer')){
				markup += "<small class='text-muted'>"+item.footer+"<small>";
			}
				

		"</div>" +
			"</div></div>";
		
		return markup;
	}	

	function formatSelect2Selection(item) {
		return item.title || item.text;
	}
	
			
	$('#{$id}').data('source', "{$datasource}");
	$("#{$id}").select2({
		ajax: {
	{if $maximumSelectionLength>0}maximumSelectionLength: {$maximumSelectionLength}, {/if}
			url: $('#{$id}').data('source'),
			dataType: 'json',
			delay: 250,
			data: function (params) {

				var tmp = {
					q: params.term, // search term
					page: params.page
				};
			{if $urlArgsAddFunc}
				$.extend(tmp, {$urlArgsAddFunc});
			{/if}
				return tmp;

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
		escapeMarkup: function (markup) {
			return markup;
		}, // let our custom formatter work
		minimumInputLength: 1,
		templateResult: formatSelect2Result, // omitted for brevity, see the source of this page
		templateSelection: formatSelect2Selection // omitted for brevity, see the source of this page
		{if $dontCloseOnSelect},closeOnSelect: false{/if}
	}).on('fillitems', function(event, items, append){

		if(append){
			var current = $(this).val();
			
			for(var index in items){
				if(current.indexOf(items[index].id)!=-1){
					console.log('select_ajax: fillitems append. Remove duplicate id:'+id+', title: '+items[index].title);
					
					delete index[id];
				}
			}			
			
		}else{
			$(this).empty();
		}
		
		var that = this;
		
		//console.log(items);

		$.each(items, function(index, item){
			//console.log(item.title+':'+item.id)
			$(that).append(new Option(item.title, item.id, true, true));
		} );

		$(this).trigger('change');		
	}).on('inittitles', function(event, ids, append){
		
		if(!ids)
			ids = $(this).val()
		
		var that = this;
		
		$.get($(this).data('source'), { ids: JSON.stringify(ids) }, function(data){
			
			if(data.hasOwnProperty('items'))
			{
				$(that).trigger('fillitems', [data.items, append]);
			}else{
				console.log("select_ajax: Items not received. Received content: "+data);
			}
			
		}, 'json')
	});
	
	{if $preload && $value}
		$('#{$id}').trigger('inittitles');
	{/if}	
	
	
	{if $onchangeFunc}
			$('#{$id}').change(function(){
				
				if(!$(this).data('init-done')){					
					$(this).data('init-done', 1)
					$(this).data('prev-val', $(this).val())
				}else{
					if($(this).data('prev-val') != $(this).val()){
						{$onchangeFunc}(true);
						$(this).data('prev-val', $(this).val());
					}else{
						{$onchangeFunc}(false);
					}
				}
			}
			).change();
	{/if}


{/if}

});
	</script>
{/capture}





{/if}