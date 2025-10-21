{include file="default_form_open.tpl" form_width="100%"}

	<tr>
		<td  class="input_label_td">
			Laiško turinys
		</td>
		<td>
			Tema: {$item->subject}<br />
			Gavimo laikas: {$item->orig_time}<br />
			{$headers=json_decode($item->headers)}
			Nuo: {$headers->fromaddress}<br />
			
			
			<hr>
			
			<iframe id="foo" style="width:100%;max-width:800px;height:500px;"></iframe>
			<textarea id="bar" style="display:none">{$item->decompressBody()|escape}</textarea>
			
			<script>
				var iframe = document.getElementById('foo'),
				iframedoc = iframe.contentDocument || iframe.contentWindow.document;

				iframedoc.body.innerHTML = document.getElementById('bar').value;
			</script>
		</td>
	</tr>	


{call e field="ruleid" type=text}


{call e field=attachments 
	type=attachments 
	valid=[image=>[storewh=>'2000x1500',minwh=>'1x1',maxwh=>'6000x6000'],limit=>99]
	preview=[thumb=>'50x50']
	readonly=$tmpreadonly
}

{call e field=data type=jstree height="100px"}
{call e field=processed type=bool}

{capture append="footer_hidden"}
<style>
	.input_label_td{ width: 150px; }
</style>
{/capture}

{assign var="comments" value=1 scope=global}
{include file="default_form_close.tpl" comments=1}