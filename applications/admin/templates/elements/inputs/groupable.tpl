<style>
  .sortablelist {
    border: 1px solid #eee;
    width: 142px;
    min-height: 20px;
    list-style-type: none;
    margin: 0;
    padding: 5px 0 0 0;
    float: left;
    margin-right: 10px;
  }
  #sortable1 li, #sortable2 li,  #sortable3 li {
    margin: 0 5px 5px 5px;
    padding: 5px;
    font-size: 1.2em;
    width: 120px;
  }
  </style>

{$unusedoptions = $options}
{$value=json_decode($value, true)}

	
<table class='inpGroupableTable'>	
	<tr>
		{foreach $groups as $groupkey => $grouptitle}
		<th>
			{$grouptitle}
		</th>
		{/foreach}
		<th>
			Unused
		</th>		
	</tr>	
	<tr>
		{foreach $groups as $groupkey => $grouptitle}
		<td valign='top'>
			<ul  class="sortablelist connectedSortable gr{$groupkey}" data-key='{$groupkey}'>
				{foreach $value[$groupkey] as $idx => $id}
					<li class="ui-state-default" data-id='{$id}'>{$options[$id]}</li>
					{gw_unassign var=$unusedoptions[$id]}
				{/foreach}
			</ul>			
		</td>
		{/foreach}
		
		<td valign='top'>
			<ul  class="sortablelist connectedSortable grUnused" >
				{foreach $unusedoptions as $id => $title}
					<li class="ui-state-default" data-id='{$id}'>{$title}</li>
				{/foreach}
			</ul>			
		</td>		
	</tr>
</table>	

<input id="{$id}" name="{$input_name}"  value="{json_encode($value)|escape}" type="hidden" />
{*
<textarea id="{$id}" name="{$input_name}" style="width:800px;height:500px">{json_encode($value)}</textarea>
*}




<script>
	  

			
	var transfer_url = "{$m->buildUri($m->view_name)}";
	
	require(['gwcms'], function(){
		  $( function() {
    $( ".sortablelist" ).sortable({
      connectWith: ".connectedSortable",
      placeholder: "ui-state-highlight",
	stop: function( event, ui ) {
		calculateItems();
	}      
    }).disableSelection();
  } );
 
		
	
	})	
	
function calculateItems(){
	var value = { };
	$('.inpGroupableTable').find('.sortablelist').each(function(){
		
		var groupkey = $(this).data('key');
		console.log(groupkey);
		
		if(!groupkey)
			return false;
		
		value[groupkey] = []
		
		$(this).find('.ui-state-default').each(function(){
			value[groupkey].push($(this).data('id'));
		})
		
		$('#{$id}').val( JSON.stringify(value) );
		
	})
}		
	
</script>


<style>
  .sortablelist {
    border: 1px solid #eee;
    width: 142px;
    min-height: 20px;
    list-style-type: none;
    margin: 0;
    padding: 5px 0 0 0;
    float: left;
    margin-right: 10px;
  }
  .sortablelist li {

    padding: 2px 5px;
    cursor: move; /* fallback if grab cursor is unsupported */
    cursor: grab;
    cursor: -moz-grab;
    cursor: -webkit-grab;    
  }
</style>
