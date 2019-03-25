{include file="common.tpl"}
{include file="elements/input_func.tpl"}



			{*dropdown hack*}
			{function name=do_toolbar_buttons_fillPartNr}
				<div class="btn-group">
					<a type="button" data-toggle="dropdown" class="gwtoolbarbtn btn btn-default btn-active-dark dropdown-toggle dropdown-toggle-icon">
					    <i class="fa fa-angle-down"></i> <span>Užpildyti seriją dalyvio nr</span>
					</a>
					<ul class="dropdown-menu">
					    <li>
						    <table style="margin:10px" class="gwTable">
							
							<tr>
								<td>Serijos pirmas skaič.</td>
								<td><input id="fillpartnr_start" value="1" style="width:50px"></td>
							</tr>
							<tr>
								<td></td>
								<td>
									<button class="btn btn-primary" onclick="fillParticNr(); return false"> 
										<i class="fa fa-play" aria-hidden="true"></i>  
									</button>
								</td>
							</tr>
						    </table>
					    </li>
					</ul>
				</div>
			{/function}	
			
			{$do_toolbar_buttons[]=fillPartNr}
					
			
			
			
			{function name=do_toolbar_buttons_fillseries} 

				
				<div class="btn-group">
					<a type="button" data-toggle="dropdown" class="gwtoolbarbtn btn btn-default btn-active-dark dropdown-toggle dropdown-toggle-icon">
					    <i class="fa fa-angle-down"></i> <span>Užpildyti seriją laiko langelius</span>
					</a>
					<ul class="dropdown-menu">
					    <li>
						<table style="margin:10px" class="gwTable">
							<tr>
								<td>Pradžios eilutė</td>
								<td><input id="filltime_startrow" value="1" style="width:50px"></td>
							</tr>
							<tr>
								<td>Laiko langas (min)</td>
								<td><input id="filltime_timewindow" value="15" style="width:50px"></td>
							</tr>
							<tr>
								<td>Laiko pradžia</td>
								<td><input id="filltime_timestart" value="08:00" style="width:50px"></td>
							</tr>
							<tr>
								<td>Kiek eilučių užpildyti</td>
								<td><input id="filltime_rowsNum2fill" value="100" style="width:50px"></td>
							</tr>
							<tr>
								<td></td>
								<td>
									<button class="btn btn-primary" onclick="fillTime(); return false"> 
										<i class="fa fa-play" aria-hidden="true"></i>  
									</button>
								</td>
							</tr>
						</table>
					    </li>
					</ul>
				</div>				
			{/function}	
			{$do_toolbar_buttons[]=fillseries}
	
			
			
			
		
			
			
		
		

{include file="default_form_open.tpl" form_width='1000px' action=saveSched}


</table>


<table class="gwTable">
	<tr>
		<td style="text-align: right;width: 100px;">
			Įvesties eilučių skaičius
		</td>
		<td class="selectjudges">
			{for $i=1;$i<=50;$i++}
				{$tmp[$i]=$i}
			{/for}
			{call e0 field=visible_rows type=select_plain options=$tmp selected=5} 
		</td>
		

		<td style="text-align: right;width: 100px;">
			Data
		</td>
		<td class="">
			{call e0 field=date type=date required=1} 
		</td>
		
	</tr>	
</table>

<br >

<table>
	<tr>
		<th>#</th>
		<th>Dalyvio numeris</th>
		<th>Laikas nuo</th>
		<th>Laikas iki</th>
		
	</tr>
	{for $i=1;$i<50;$i++}
	<tr class="timeslot timeslot_{$i}">
		<td>{$i}</td>
		<td>
			{call e0 field="`$i`/participant_num" type=number value="" required=1} 
		</td>			
		<td>
			{call e0 field="`$i`/start_time" type=text value="" required=1} 
		</td>	
		<td>
			{call e0 field="`$i`/end_time" type=text value="" required=1} 
		</td>	
	</tr>
	{/for}
</table>







<script>
	require(['gwcms'], function(){	
		
		//---ISJUNGTI ENTER MYGTUKA KAIP SUBMITINANTI FORMA
		$('#itemform').on('keydown', 'input[type=number], select', function(e) {
		    var self = $(this)
		      , form = self.parents('form:eq(0)')
		      , focusable
		      , next
		      ;
		    if (e.keyCode == 13) {
			focusable = form.find('input,a,select,button,textarea').filter(':visible');
			next = focusable.eq(focusable.index(this)+1);
			if (next.length) {
			    next.focus();
			} else {
			    form.submit();
			}
			return false;
		    }
		});				
	 
	 
		$('#item__visible_rows__').change(function(){
			$('.timeslot').hide();
			$(".timeslot input").prop('required',false);
			
			for(var i=1;i<=this.value;i++){
				$('.timeslot_'+i).show();
				$('.timeslot_'+i+' input').prop('required',true);
			}
		}).change();
	})
	
	function fillParticNr()
	{
		var start = $('#fillpartnr_start').val()-0;
		
		console.log("start: "+$('#fillpartnr_start').val())
		console.log("visible rows: "+$('#item__visible_rows__').val())
		
		for(var i=1;i<=$('#item__visible_rows__').val();i++)
		{
			$('#item__'+i+'__participant_num__').val(start)
			start++;
		}
		
	}
	
	function addMinutes(date, minutes) {
	    return new Date(date.getTime() + minutes*60000);
	}
	
	function formatDate(date)
	{
		return ("00" + date.getHours()).slice(-2) +':'+ ("00" + date.getMinutes()).slice(-2);
	}
	
	function fillTime()
	{
		var visiblerows = $('#item__visible_rows__').val();
		var start = $('#filltime_startrow').val()-0;
		var loop = $('#filltime_rowsNum2fill').val()-0;
		var start_time = $('#filltime_timestart').val();
		var minutes = $('#filltime_timewindow').val();
		
		
		var date = new Date('2014-11-02 '+ start_time);
		
		
		for(var i=0;i<loop;i++)
		{
			
			
			
			$('#item__'+(start+i)+'__start_time__').val(formatDate(date));
			date = addMinutes(date, minutes);
			$('#item__'+(start+i)+'__end_time__').val(formatDate(date));
		}
		
		
	}
</script>

<style>
	.timeslot { display: none; } 
</style>


<table>
{include file="default_form_close.tpl"}
