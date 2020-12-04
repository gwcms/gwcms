{$fields=array_merge(['ANY'], GW::s(LANGS))}
{include "list/actions.tpl"}
	
	{foreach $groupedlist as $group => $keys}
		

	
	<table data-group="{$group}" class="trTable table-condensed table-hover table-vcenter table-bordered gwTable gwActiveTable gwListTable">
		
		<tr>
			<th>{$group}</th>
			{foreach $fields as $field}
				<th>{$field}</th>
			{/foreach}
			<th>{call dl_actions_ext_actions item=$langfiles[$group]}</th>
		</tr>
		
		{foreach $keys as $id => $item}
			
			<tr data-id='{$id}'>
				<td>{$id}</td>
				{foreach $fields as $field}
					{$field=strtoupper($field)}
					<td 
						class="cell {if $changedlines[$group][$id][$field]}waitpush{/if}"
						data-initial="{$item[$field]|escape}"
						data-field='{$field}'>{$item[$field]}</td>
				{/foreach}
			</tr>
				
		{/foreach}
		
	</table>
	<br />

	{/foreach}
<script>
	require(['gwcms'], function(){
		initCells('.cell')
	})
	var savelines_url = '{$m->buildUri(flatedit,[act=>doSaveLines,ajax=>1,id=>''])}';
	
	function trsaveone(e){
		var data = [];
		
		var group= e.parents('.trTable').data('group')
		var id = e.parents('tr').data('id')
		var url = savelines_url+group;

		data.push({ "id":id, "field": e.data('field'), 'value':e.html() })
		
		$.post(url, { rows: JSON.stringify(data) }, function(data){
			
			
			if(data=='SAVEOK'){
				e.removeClass('modified');
				e.addClass('waitpush');
			}else{
				alert("Unexpected reply: \n\n"+data)
			}
		})
	}
	
	function testBtnStates()
	{
		if($('.modified').length){
			$('#savebtn').fadeIn();
		}else{
			$('#savebtn').fadeOut();
		}
	}
	
	function focus(e)
	{
		var fieldInput = e;
		var fldLength= fieldInput.val().length;
		fieldInput.focus();
		fieldInput[0].setSelectionRange(fldLength, fldLength);		
	}
	
	function initCells(querysel){
		$(querysel).on('flip',function(x,dontclose){
			
			var e = $(this)
			
			if(e.data('isedit')){
				e.html(e.find('textarea').val());
				e.data('isedit', false)
				e.removeClass('open');
			}else{
				if(!dontclose)
					$(".open").trigger("flip")
				
				e.html("<textarea class='tredit'>"+e.html()+"</textarea>");
				focus(e.find('textarea'))
				
				e.data('isedit', true)
				e.addClass('open');
			}
			
			if(e.html()!=e.data('initial')){
				e.addClass('modified');
				
				if(!e.hasClass('open'))
					trsaveone(e)
			}else{
				e.removeClass('modified');
			}
			
			
			testBtnStates();
		}).dblclick(function(){
			$(this).trigger("flip");
		}).keydown(function(e){
			if(e.which == 27){
			   $(this).trigger("flip");
			}
		});	
	}
</script>

<style>
	.trTable{ background-color: transparent; float:left; margin-top: 10px; margin-left: 10px;}
	.trTable td{ background-color: white; }
	.waitpush{ background-color: #f7fdca !important; }
	.modified{ background-color: #f6dc00 !important }
	.tredit{ width:100%;height:50px;min-width: 400px; }
	
	.cell{ max-width: {if !$langsfound}800{else}{round(800/count($langsfound))}{/if}px }
	
</style>


