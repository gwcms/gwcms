{include file="default_open.tpl"}

{*if ID is set then it will be UPDATE action. else - INSERT*}

{$default_form_before_form}


<form id="itemform" action="{$smarty.server.REQUEST_URI}" method="post"  enctype="multipart/form-data" >

<table style="width:{if $form_width}{$form_width}{else}600{/if}px">
<tr>
<td>

{assign var="width_title" value="30%" scope="root"}


<input type="hidden" name="act" value="do:{$action|default:"save"}" />
<input type="hidden" name="item[id]" value="{$item->id}" />


<script>
	
	$(function(){
		$('#itemform').attr('rel', $('#itemform').serialize());	
	})
	
	$('#itemform').submit(function() {
		window.onbeforeunload = null;
	});	

	
	window.onbeforeunload = function() {
		if($('#itemform').attr('rel') != $('#itemform').serialize())
			return "You have made changes on this page that you have not yet confirmed. If you navigate away from this page you will lose your unsaved changes";
	}	
	
</script>

<table class="gwTable" style="width:100%">