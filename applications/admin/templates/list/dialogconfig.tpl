{$no_standart_cms_frame=1}
{include "default_open.tpl"}




<div class="dialogcontainer">
<style>

.dialogcontainer{ padding: 10px}
.sortable { list-style-type: none; margin: 0; padding: 0; }
.sortable input{ margin:0; margin-right: 5px}
.sortable li { 
	font-size: 100%; 
	line-height: 100%; 
	margin-bottom: 2px;
	padding: 4px; border: 0; 
	text-align: left;
	background-color: #eee;
}
.sortable li span {
		vertical-align: middle;
}




.sortable-state-disabled{ 
	border:1px solid silver;
	background-color: #fff !important;
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

.savedOrderRow input[type="radio"]{
	padding: 0px;
	margin:0px 0 0px 0;
	position: relative;
	top:3px;
	line-height: 15px;
}
.savedOrderRow label{
	padding:0;margin:0
}

</style>









<form id="listConfig"  action="{$app->buildURI($app->path_arr_parent.path)}" method="post" >
<input type="hidden" name="act" value="doDialogConfigSave" />
<input type="hidden" id="defaults" name="defaults" value="0" />
<input type="hidden" name="dialog_iframe" value="1">
<input type="hidden" id="remove_saved_filters" name="remove_saved_filters" value="[]" />
<input type="hidden" id="default_filter" name="default_filter" value="{$default_filter}" />

<table style="" class="gwTable gwActiveTable">


<tr><th>{$lang.LDS_FIELD_PRIORITY_VISIBILITY}</th><th>{GW::l('/g/ORDERS_LABEL')}</th></tr>

<tr>
<td valign="top">

<ul  class="sortable form-field" style="width:200px;margin-top:5px">
	{foreach $fields as $id => $enabled}
		<li>
			<input type="checkbox" {if $enabled}checked{/if} />
			<input type="hidden" name="fields[{$id}]" value="{$enabled|intval}">
			<span>{$app->fh()->fieldTitle($id)}</span>
		</li>
	{/foreach}
</ul>
</td>

<td valign="top">

<ul  class="sortable form-field" style="width:200px;margin-top:5px">
	{foreach $order_fields as $id => $info}
		<li class="orderrow">
			<input class="orderCheckbox" type="checkbox" {if $info.enabled}checked{/if} />
			<input type="hidden" name="order_fields[{$id}][enabled]" value="{$info.enabled|intval}">
			<input class="dirdrop" type="hidden" name="order_fields[{$id}][dir]" value="{$info.dir|default:'ASC'}">
			<span>
				<a href="#" class="dirchange">
					<i class="fa fa-sort-amount-{strtolower($info.dir)}"></i>
					
				</a>
			</span>
			<span>{$app->fh()->fieldTitle($id)}</span>
			
		</li>
	{/foreach}
</ul>

{function "isdefaultinput"}
	<a href="#" class="switchDefault" data-name="{$oname|escape}">
		<i class="fa {if $default}fa-check-circle-o{else}fa-circle-o{/if}" aria-hidden="true"></i>
	</a>
{/function}

<hr>
<table style="" class="gwTable gwActiveTable">
	<tr><th colspan="4">{GW::l('/g/SAVE_NAME')}</th></tr>
	
	{$i=0}
	{foreach $saved_orders as $name => $ordvals}
		{$i=$i+1}
		<tr class="savedOrderRow">
			<td><input name="existing_order_name" type="radio" value="{$name|escape}" id="savedOrder{$i}" {if $editorder==$name}checked{/if}></td>
			<td><label for="savedOrder{$i}" title="{$ordvals.order|escape}">{$ordvals.name}</label></td>
			<td>{call "isdefaultinput" default=$ordvals.default oname=$name}</td>
			<td> 
				<a href="#" class="removeSavedFilter" data-name="{$name|escape}"><i class="fa fa-times" aria-hidden="true"></i></a>
			</td>
		</tr>
		
	{/foreach}
	
	<tr  class="savedOrderRow">
		<td>
			<input  name="existing_order_name"  type="radio" value=""  id="newOrder"> 
		</td>
		<td>
			<input type="text" name="new_order_name" placeholder="{GW::l('/g/MY_ORDERING')}"  onfocus="$('#newOrder').prop('checked', true);">
		</td>
		<td>
			{call "isdefaultinput" default=false oname=""}
		</td>
	</tr>
</table>


</td>

</tr>

</table>
	
	<input id="listConfigSubmit" type="submit" style="display:none">

</form>
</div>


<div style='border-top: 1px solid rgba(0, 0, 0, 0.07);padding: 10px;'>
	<button class="btn btn-default" onclick="$('#listConfigSubmit').click();">{GW::l('/g/SAVE')}</button>
</div>


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

		$('.sortable li input').change(function() {
			var $this = $(this)
			$this.parent().toggleClass('sortable-state-disabled')

			$this.next().val( $this.is(':checked') ? 1 : 0 );
		});

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
	})

	$('.removeSavedFilter').click(function(event){
		event.stopPropagation();

		var removelist=JSON.parse($('#remove_saved_filters').val())
		removelist.push($(this).attr('data-name'));
		$('#remove_saved_filters').val(JSON.stringify(removelist));

		$(this).parents('.savedOrderRow').fadeOut(300, function(){ $(this).remove();});

		return false
	})
	
	$('.switchDefault').click(function(event){
		event.stopPropagation();
		
		$('#default_filter').val($(this).attr('data-name'))
		
		$('.switchDefault i').removeClass('fa-check-circle-o').addClass('fa-circle-o');
		$(this).find('i').removeClass('fa-circle-o').addClass('fa-check-circle-o');
		
		return false;
	})


	$('input[type=radio][name=existing_order_name]').on('change', function() {
		$('input[name="new_order_name"]').attr("required", $(this).val()=='' ? "true" : false);
	});	

	$('.orderrow input[type=checkbox]').on('change', function() {
	   $('#newOrder').attr("required", $('.orderrow input[type=checkbox]').length > 0 ? 'true' : false)
	});	




	$('.switchDefault').attr('title', "{GW::l('/g/SET_AS_DEFAULT')}")

});
</script>


{include "default_close.tpl"}