{include file="common.tpl"}


	{$do_toolbar_buttons_actions[]=transform}	
	{function name=do_toolbar_buttons_transform} 		
				
			{toolbar_button 
				title="Units convert pt to px"
				href=$m->buildUri(testpdfgen, [act=>doProcess,fname=>pt2px,ratio=>1.5]+$smarty.get) confirm=1}
				
			{toolbar_button 
				title="Units convert cm to px"
				href=$m->buildUri(testpdfgen, [act=>doProcess,fname=>cm2px,ratio=>15]+$smarty.get) confirm=1}
				
			{toolbar_button 
				title="Bigger font sizes by 10%"
				href=$m->buildUri(testpdfgen, [act=>doAdjustFontsize,ratio=>1.1]+$smarty.get) confirm=1}		
				
			{toolbar_button 
				title="Smaller font sizes by 10%"
				href=$m->buildUri(testpdfgen, [act=>doAdjustFontsize,ratio=>0.9]+$smarty.get) confirm=1}				
				
						
	
	{/function}		
		{$do_toolbar_buttons[]=actions}
	{function name=do_toolbar_buttons_actions}
		{call name="do_toolbar_buttons_dropdown" do_toolbar_buttons_drop=$do_toolbar_buttons_actions groupiconclass="fa fa-angle-down" grouptitle="Veiksmai"}
	{/function}	

{include "default_open.tpl"}	



{include "tools/lang_select.tpl"}
<form action="{$smarty.server.REQUEST_URI}" method="POST" id="htmlcontentsform"></form>
<table style="width:100%">
	<tr>
		<td style="width:50%">
			{*{include file="elements/input0.tpl" type=htmlarea value=$filecontents name=htmlcontents ck_options=[height=>"600px"]}*}
			{include file="elements/input0.tpl" type=code_smarty value=$filecontents name=htmlcontents height="700px" width="100%"}
		</td>
		<td style="width:50%" id="pdfzona"><object data="{$m->buildUri(false,[act=>doGenPdf]+$smarty.get)}" style="width: 100%;height: 730px"></object></td>
	</tr>
</table>


	
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
	

<br ><br ><br ><br >


{include file="tools/default_form_open.tpl"  action=savePdfParams}	
{call e type="number" field="config/dpi" default=150}
{include file="tools/default_form_close.tpl" submit_buttons=[apply]}


{include "default_close.tpl"}