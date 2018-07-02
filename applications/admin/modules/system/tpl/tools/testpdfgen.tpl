{include "default_open.tpl"}	


<form action="{$smarty.server.REQUEST_URI}" method="POST" id="htmlcontentsform">
<table style="width:100%">
	<tr>
		<td style="width:50%">
			{*{include file="elements/input0.tpl" type=htmlarea value=$filecontents name=htmlcontents ck_options=[height=>"600px"]}*}
			{include file="elements/input0.tpl" type=code_smarty value=$filecontents name=htmlcontents height="700px" width="100%"}
		</td>
		<td style="width:50%" id="pdfzona"><object data="{$m->buildUri(false,[act=>doGenPdf])}" style="width: 100%;height: 730px"></object></td>
	</tr>
</table>
	
</form>
	
<button id="submit">Click me or Hit F9 on keyboard</button>

<script>
	require(['gwcms'], function(){
		
		$('#submit').click(function(){
			$.ajax({
				type: "POST",
				url: $('#htmlcontentsform').attr('action'),
				success: function (data) {
					$('#pdfzona').html($('#pdfzona').html());
				},
				error: function (error) {
					alert('error')
				},
				async: true,
				data: { item: { htmlcontents: $('#item__htmlcontents__').val() }  },
				cache: false,
				timeout: 60000				
			});			
			
			
			
		})
		
		
		$("body").keydown(function (event) {


			
			if (event.which == 120) {
				
				$('#submit').click();
				event.preventDefault();
			}

		});		
		
	})
</script>
	

{include "default_close.tpl"}