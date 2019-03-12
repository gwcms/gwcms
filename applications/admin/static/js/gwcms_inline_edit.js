/*
 * Redaguoti iš sąrašo eilute,
 * list.tpl faile prideti {$dl_inline_edit=1}
 * tada visos saraso eilutes reaguoja i i double click
 * padoubleclickinta eilute pasislepia per ajax uzloadinus forma
 * i jos vieta pridedama formos eilute kuri pilnai atkartoja rodomus laukelius pakeisdama redaguojamais laukeliais
 * paspaudus enter be shift mygtuko ivykdomas issaugojimas, paspaudus escape iseinama is redagavimo, 
 * padoubleclickinus kitą eilutę buvusiosios redagavimas uždaromas neišsaugant
 * 
 */



function initActiveList()
{
	
	$(document).keyup(function(e) {
		 if (e.keyCode == 27) { // escape key maps to keycode `27`
			$('.inlineFormRow').remove();
			
			gw_adm_sys.resetInitState($('.inlineFormRowHidd'));
			$('.inlineFormRowHidd').show().removeClass('inlineFormRowHidd');
			
			gw_adm_sys.initObjects();
			
		}
		
		
		//$('.inlineFormRow').length > 0;  - siuo metu yra redaguojama
		
		if(e.keyCode == 13  && !e.shiftKey && $('.inlineFormRow').length > 0)
		{
			submitInlineForm();
		}
		
	});	
	
	initActiveListRows();
}


function fireInlineEdit(trigger)
{
	
	
	var id = trigger.data('id');
	var url = trigger.data('url') ? trigger.data('url') : gw_navigator.url(inline_edit_form_url, {id:id,form_ajax:1})

	var name = 'list_row_' + id;
	var trobject = $('#'+name)

	$('.inlineFormRow').remove();
	$('.inlineFormRowHidd').show().removeClass('inlineFormRowHidd');

	triggerLoading(trigger,1);

	$.get(url, function (data) {
			loadRowAfterAjx(trobject, data);
			$('#' + name).hide().addClass('inlineFormRowHidd');	
			triggerLoading(trigger, 0);
			
			$('#inlineForm').submit(function( event ) {
				submitInlineForm();
				event.preventDefault();
			})
	});	
}

function initActiveListRows()
{
	//po to kai perkraus eilute	
	$( ".list_row:not([data-initdone='1'])" ).dblclick(function() {
		
		fireInlineEdit($(this));
		
	}).attr('data-initdone',1);	
	
	$('.inline_edit_trigger').unbind('click').click(function () {
		fireInlineEdit($(this));
	}
	);	
	
	
	gw_adm_sys.initObjects();
}


function loadRowAfterAjx(trobject, data)
{
		if (data.indexOf('<!--AJAX-NOERR-DONT-REMOVE-->') == -1)
				data = "<td colspan=100>" + data + "</td>";		
		
		return loadRowAfter(trobject, data);
}

function animateErrorInput(field)
{
		var ob =  $('.gw_input_item__'+field+'__');
		var curr_bgcolor = ob.css("background-color");
		var curr_color = ob.css("color");
		
		ob.animate({backgroundColor: "#FF0000",color: "#fff"}, 500 );
		
		setTimeout(function(){
				ob.animate({backgroundColor: curr_bgcolor, color: curr_color}, 10000 );
		},10000 )
}

function submitInlineForm()
{
		$( "#inlineForm").trigger( "beforesubmitevents", [ "Custom", "Event" ] );
		
		var inlineformrow = $('.inlineFormRow');
		inlineformrow.find(':input').attr('form', 'inlineForm');
		
		var id = inlineformrow.attr('data-id');
		var rowname = 'list_row_' + id; //read only row
		var rowobj = $('#' + rowname);
		
		//triggerLoading(trigg, 1);

		$.post($("#inlineForm").attr('action')+'&time='+new Date().getTime(), $("#inlineForm").serialize()+'&inlistform=1&ajax=1',
				function (data, status, request) {
						
						var messages = request.getResponseHeader('GW_AJAX_MESSAGES');
						
						if (request.getResponseHeader('GW_AJAX_FORM') == 'OK')
						{
							console.log(data);
							
								rowobj.after(data);
								
								if(inlineformrow.attr('data-id')!='0')
									rowobj.remove();
								
								inlineformrow.remove();
								$('.activeList').trigger( "updated");//call init list
								
								var id = request.getResponseHeader('GW_AJAX_FORM_ITEM_ID');
								var title = request.getResponseHeader('GW_AJAX_FORM_ITEM_TITLE');							
								
								//console.log(messages);
								
								animateChangedRow(id);
								initActiveListRows();
								
								//gw_navigator.jump(location.href, {id:id})
						} else {
								//inlineformrow.remove();
								//loadRowAfterAjx(rowobj, data)
								var err_fields = JSON.parse(request.getResponseHeader('GW_ERROR_FIELDS'));
								
	
								for(var field in err_fields)
									animateErrorInput(field);
						}
						
						gwcms.showMessages(JSON.parse(messages), title);

				}
		)
}

