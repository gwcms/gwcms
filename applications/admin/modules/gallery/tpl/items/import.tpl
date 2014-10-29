{include file="default_open.tpl"}

<p>
	{gw_link levelup=1 title=$lang.BACK}
</p>
 
<br />

<form action="{$smarty.server.REQUEST_URI}" method="post"  enctype="multipart/form-data" >
<input type="hidden" name="act" value="do:uploadzip" />

<table class="gwTable" style="max-width:500px">
<tr>
	<td>{$m->lang.ZIP_FILE} <br /><small>{$app->fh()->maxUploadSize()}</small></td>
	<td><input type="file" name="zipfile" /></td>
</tr>
</table>

<div id="wait_message" style="display: none; text-align: center; font-weight: bold;">
	{$m->lang.PLEASE_WAIT_UPLOADING}
</div>

<br />

<input type="submit" value="{$lang.SAVE}"  onclick="$('zip_input, wait_message').toggle()"/>


</form>

{include file="default_close.tpl"}