{$no_standart_cms_frame=1}
{include "default_open.tpl"}


<form id="listConfig"  action="{$app->buildURI($app->path_arr_parent.path)}" method="post" >
<input type="hidden" name="act" value="doDialogConfigSave" />
<input type="hidden" id="defaults" name="defaults" value="0" />
<input type="hidden" name="dialog_iframe" value="1">
<input type="hidden" id="remove_saved_filters" name="remove_saved_filters" value="[]" />
<input type="hidden" id="default_filter" name="default_filter" value="{$default_filter}" />

<div class="dialogcontainer">
<style>

.dialogcontainer{ padding: 10px}
.cols_deactivated, .sortable { list-style-type: none; margin: 0; padding: 0; }
.cols_deactivated input, .sortable input{ margin:0; margin-right: 5px}
.cols_deactivated li, .sortable li { 
	font-size: 100%; 
	line-height: 100%; 
	margin-bottom: 2px;
	padding: 4px; border: 0; 
	text-align: left;
	background-color: #eee;
}
.cols_deactivated li span, .sortable li span {
	vertical-align: middle;
}




.sortable-state-disabled{ 
	border:1px solid silver;
	background-color: #fff;
	cursor: default !important;	
	color: silver;
}
.sortable-state-disabled:hover{ 
	color: silver;
}
.ui-state-highlight { 
	background-color: yellow !important;
	height: 25px;
}
.form-field * { vertical-align: middle; }



.sortable li { cursor: row-resize }

.bootstrap-select .btn { padding: 6px 12px 6px 12px }

.cols_deactivated{ max-height:150px; overflow-y: auto; }

</style>




<table style="" class="gwTable">


<tr><th>{GW::l('/g/LDS_FIELD_PRIORITY_VISIBILITY')}</th><th>{GW::l('/g/ORDERS_LABEL')}</th></tr>

<tr>
<td valign="top" class="columns">

<ul class="cols_activated sortable form-field" style="width:200px;margin-top:5px">
	{foreach $fields as $id => $enabled}
		<li>
			<input type="checkbox" {if $enabled}checked{/if} />
			<input type="hidden" name="fields[{$id}]" value="{intval($enabled)}">
			<span>{$m->fieldTitle($id)}</span>
		</li>
	{/foreach}
</ul>


<i id="cols_deactivated_label" style="color:brown">Deaktyvuoti:</i>
<ul class="cols_deactivated">
</ul>

</td>

<td valign="top" class="columns">

<ul  class="cols_activated sortable form-field" style="width:200px;margin-top:5px">
	{foreach $order_fields as $id => $info}
		<li class="orderrow">
			<input class="orderCheckbox" type="checkbox" {if $info.enabled}checked{/if} />
			<input type="hidden" name="order_fields[{$id}][enabled]" value="{intval($info.enabled)}">
			<input class="dirdrop" type="hidden" name="order_fields[{$id}][dir]" value="{$info.dir|default:'ASC'}">
			<span>
				<a href="#" class="dirchange">
					<i class="fa fa-sort-amount-{strtolower($info.dir)}"></i>
					
				</a>
			</span>
			<span>{$m->fieldTitle($id)}</span>
			
		</li>
	{/foreach}
</ul>

<i id="cols_deactivated_label" style="color:brown">{GW::l('/g/DEACTIVATED_FIELDS')}:</i>
<ul class="cols_deactivated">
</ul>





</td>

</tr>

</table>
	


</div>

{*
Leisti pasirinkti kur saugoti ar taikyti:
į page view, current(dabar parinktas), default(parenkamas jei neparinktas joks kitas) ir regural(sukuriamas po defaultu jei nera)

*}

<div style='border-top: 1px solid rgba(0, 0, 0, 0.07);padding: 10px;'>
	
	<select id="saveto" name="pageviewid" class="selectpicker">
		<option value="">Taikyti neišsaugant</option>
		
		{if !$m->readonly && $m->write_permission}
		{foreach $page_views as $pview}
			<option value="{$pview->id}" {if $current_page_view_id == $pview->id}selected="selected"{/if}>{GW::l('/g/PAGE_VIEWS')}: {$pview->title}</option>
		{/foreach}
		{/if}
		
	</select>
	<button id="submitbtn" class="btn btn-primary" data-save="{GW::l('/g/SAVE')}" data-apply="{GW::l('/g/APPLY_1')}"></button>
</div>


</form>



{if !$gwcms_input_select_loaded}
	{$m->addIncludes("bs/selectcss", 'css', "`$app_root`static/vendor/bootstrap-select/css.css")}
	{assign var=gwcms_input_select_loaded value=1 scope=global}	
{/if}

<script type="text/javascript">require(['vendor/bootstrap-select/js'], function(){ $('.selectpicker').selectpicker(); });</script>

<script>
require(["gwcms"], function(){	
	$(function() {
		
		$( ".sortable" ).sortable({
			placeholder: "ui-state-highlight"
		});
		$( ".sortable" ).disableSelection();
	});

	$(document).ready(function() {

		$('.sortable li').addClass('sortable-state-disabled');
		$('.sortable li input:checked').parent().toggleClass('sortable-state-disabled');

		$('.sortable li input[type=checkbox]').change(function() {
			var $this = $(this)
			
			var $li = $(this).parent();
			
			var attachto=$li.parents('.columns').find($this.is(':checked') ? '.cols_activated' : '.cols_deactivated').get(0);
			
			if($this.is(':checked')){
				$li.detach().appendTo( attachto );
			}else{
				$li.detach().prependTo( attachto );
			}
			
			if ( $this.is(':checked') ) {
				$( $li ).animate({
					backgroundColor: "#eee",
					color: "#000"
				}, 1000 );
			} else {
				$( $li ).animate({
					backgroundColor: "#fff",
					color: "#999"
				}, 1000 );
			}
			
			
			
			
			$this.parent().toggleClass('sortable-state-disabled', $this.is(':checked'))

			$this.next().val( $this.is(':checked') ? 1 : 0 );
			
			$('.columns').each(function(){
				var max_height = Math.max(500-$(this).find('.cols_activated').height(), 150)
				
				$(this).find('.cols_deactivated').css('max-height', max_height+'px')
				
				
			})
		});
		
		$('.columns .sortable li input:not(:checked)').each(function(){ 
			$(this).change();
		})
		
		

		$('.sortable li').hover(
			function () {
				$(this).addClass("ui-state-hover");
			},
			function () {
				$(this).removeClass("ui-state-hover");
			}
		);
	});


	

	$('.dirchange').click(function(event){
		event.stopPropagation();

		var i=$(this).find('.fa');
		var inp = $(this).parents('.orderrow').find('.dirdrop');

		if(i.hasClass('fa-sort-amount-asc'))
		{
			i.removeClass('fa-sort-amount-asc')
			i.addClass('fa-sort-amount-desc');
			inp.val('DESC');
		}else{
			i.addClass('fa-sort-amount-asc')
			i.removeClass('fa-sort-amount-desc');	
			inp.val('ASC');
		}



		return false
	});
	


	$('.orderrow input[type=checkbox]').on('change', function() {
	   $('#newOrder').attr("required", $('.orderrow input[type=checkbox]').length > 0 ? 'true' : false)
	});	




	$('.switchDefault').attr('title', "{GW::l('/g/SET_AS_DEFAULT')}")
	
	$('#saveto').change(function(){
		$('#submitbtn').html(this.value ? $('#submitbtn').data('save') : $('#submitbtn').data('apply'))
		$('#submitbtn').removeClass('btn-primary').removeClass('btn-warning').addClass(this.value ? 'btn-primary' : 'btn-warning')
	}).change();

});
</script>


{include "default_close.tpl"}