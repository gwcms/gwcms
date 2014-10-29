{include file="default_open.tpl"}

{*if ID is set then it will be UPDATE action. else - INSERT*}



<table style="width:{if $form_width}{$form_width}{else}600{/if}px">
<tr>
<td>

{assign var="width_title" value="30%" scope="root"}


<form id="itemform" action="{$smarty.server.REQUEST_URI}" method="post"  enctype="multipart/form-data" >

<input type="hidden" name="act" value="do:{$action|default:"save"}" />
<input type="hidden" name="item[id]" value="{$item->id}" />

<table class="gwTable" style="width:100%">