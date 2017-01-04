{include file="default_open.tpl"}

{*if ID is set then it will be UPDATE action. else - INSERT*}

{$default_form_before_form}


<form id="itemform" class="itemform" action="{$formendpoint|default:$smarty.server.REQUEST_URI}" method="post"  enctype="multipart/form-data" onsubmit="gwcms.beforeFormSubmit(this)"  >

<table style="width:{if $form_width}{$form_width}{else}600px{/if}" >
<tr>
<td>

{assign var="width_title" value="30%" scope="root"}


<input class="gwSysFields" type="hidden" name="act" value="do:{$action|default:"save"}" />

{if !$nohiddenitemid}
<input class="gwSysFields" type="hidden" name="item[id]" value="{$item->id}" />
{/if}


{*if $item->id}
	<input class="gwSysFields" type="hidden" name="item[update_time_check]" value="{if $item->update_time_check}{$item->update_time_check}{else}{$item->update_time}{/if}" />
{/if*}

{capture append=footer_hidden}
		<script src="{$app_root}static/js/forms.js"></script>
{/capture}

<script>
	var changes_track={if $changes_track}1{else}0{/if};
	
	
	require(['gwcms'], function(){
	
		$(function(){
			$('#itemform').attr('rel', $('#itemform').serialize());	

			if(changes_track){
					gw_changetrack.init('.itemform');
			}
		})

		$('#itemform').submit(function() {
			window.onbeforeunload = null;
		});	


		window.onbeforeunload = function() {
			if($('#itemform').attr('rel') != $('#itemform').serialize())
				return "You have made changes on this page that you have not yet confirmed. If you navigate away from this page you will lose your unsaved changes";
		}	
	
	})
	
</script>




	<div class="row panel gwlistpanel">
		<div class="panel-body">
			
			<table class="gwTable gwcmsTableForm">
				<tbody>