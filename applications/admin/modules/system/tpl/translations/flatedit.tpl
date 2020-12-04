{include "default_open.tpl"}

{if $smarty.get.addln}
	{$langsfound[strtoupper($smarty.get.addln)]=1}
{/if}
{$fields=array_merge(['ANY'],array_keys($langsfound))}


{foreach GW::s('LANGS') as $sysln}
	{$sysln=strtoupper($sysln)}
	{if !isset($langsfound[$sysln])}
		<a href="{Navigator::buildURI(false, [addln=>$sysln])}">+{$sysln}</a>
	{/if}
{/foreach}


<button class="btn btn-default" id="openclosebnt" onclick='$(".cell").trigger("flip",[true])'>Open / close</button>
<button class="btn btn-default" id="savebtn" onclick='trsave()'><i class="fa fa-floppy-o"></i> Save</button>

<span class="text-muted"><i class="fa fa-info-circle"></i> Write to column "ANY" if all langs value same</span>
<br /><br />

	<table id="trTable" class="table-condensed table-hover table-vcenter table-bordered gwTable gwActiveTable gwListTable">
		
		<tr>
			<th>ID</th>
			{foreach $fields as $field}
				<th>{$field}</th>
			{/foreach}
		</tr>
		
		{foreach $list as $item}
			
			<tr data-id='{$item->id}'>
				<td>{$item->id}</td>
				{foreach $fields as $field}
					<td 
						class="cell {if $orig[$item->id][$field]!=$item->$field}waitpush{/if}" 
						data-initial="{$item->$field|escape}"
						data-field='{$field}'>{$item->$field}</td>
				{/foreach}
			</tr>
				
		{/foreach}
		
		<tr id="addnew"><td><button onclick="addnew()">Add new</button></td></tr>
	</table>
	
	<br /><br />
	<a href="{$m->buildUri(autotranslate,[id=>$smarty.get.id])}" class="btn btn-primary"><i class="fa fa-google"></i> Auto translate empty cells</a>
	

<textarea id="rowtpl" style="display:none">	
	<tr data-id='idplace'>
		<td>idplace</td>
		{foreach $fields as $field}
			<td 
				class="cell modified" 
				data-initial=""
				data-field='{$field}'></td>
		{/foreach}
	</tr>	
</textarea>
	
<script>
	require(['gwcms'], function(){
		initCells('.cell')
	})
	
	
	function trsave(){
		var data = [];
		
		$(".open").trigger("flip");
		
		$('.modified').each(function(){
			var e = $(this);
			var id = e.parent().data('id');
			;
			data.push({ "id":id, "field": e.data('field'), 'value':e.html() })
		})
		
		console.log(data);
		
		gw_navigator.post('{$m->buildUri(flatedit,[act=>doSaveLines,id=>$smarty.get.id])}', { rows: JSON.stringify(data) }, 'post' )
	}
	
	function focus(e)
	{
		var fieldInput = e;
		var fldLength= fieldInput.val().length;
		fieldInput.focus();
		fieldInput[0].setSelectionRange(fldLength, fldLength);		
	}	
	
	function testBtnStates()
	{
		if($('.modified').length){
			$('#savebtn').fadeIn();
		}else{
			$('#savebtn').fadeOut();
		}
	}
	
	function addnew()
	{
		var key = window.prompt('Key ex: "OPTIONS/STATUS/0" or "NAUJAS_VERTIMO_KODAS" please make key human readable use caps lock and "_" symbol');
		console.log(key)
		var html = $('#rowtpl').val();
		
		html = html.replaceAll('idplace', key)
		console.log(html)
	
		$('#trTable').find('tr:last').prev().after(html);
		initCells($('#trTable').find('tr:last').prev().find('.cell'))
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
	.modified{ background-color: #f6dc00 !important }
	.tredit{ width:100%;height:50px;min-width: 400px; }
	.waitpush{ background-color: #f7fdca }
	.cell{ max-width: {if !$langsfound}800{else}{round(800/count($langsfound))}{/if}px }
</style>


	
{include "default_close.tpl"}