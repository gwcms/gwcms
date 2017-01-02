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
			$('.inlineFormRowHidd').show().removeClass('inlineFormRowHidd');
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
	var url = trigger.data('url') ? trigger.data('url') : gw_navigator.url(inline_edit_form_url, {id:id,ajax:1})

	var name = 'list_row_' + id;
	var trobject = $('#'+name)

	$('.inlineFormRow').remove();
	$('.inlineFormRowHidd').show().removeClass('inlineFormRowHidd');

	triggerLoading(trigger,1);

	$.get(url, function (data) {
			loadRowAfterAjx(trobject, data);
			$('#' + name).hide().addClass('inlineFormRowHidd');	
			triggerLoading(trigger, 0);
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
	
}


function loadRowAfterAjx(trobject, data)
{
		if (data.indexOf('<!--AJAX-NOERR-DONT-REMOVE-->') == -1)
				data = "<td colspan=100>" + data + "</td>";		
		
		return loadRowAfter(trobject, data);
}

function loadRowAfter(trobject, data, classn)
{		
		var id = trobject.attr('data-id');
		var name = 'list_row_' + id;
		
		classn = classn ? classn : 'inlineFormRow';
		
		trobject.after('<tr id="' + name + '_after" class="' + classn + '" data-id="' + id + '">' + data + '</tr>');
}

function animateChangedRow(id)
{
		var curr_bgcolor = $('#list_row_'+id).css("background-color");
		var curr_color = $('#list_row_'+id).css("color");
		
        $('#list_row_'+id).animate({backgroundColor: "#003311",color: "#fff"}, 300 );
		
		setTimeout(function(){
				$('#list_row_'+id).animate({backgroundColor: curr_bgcolor, color: curr_color}, 300 );
		},300 )
	
}

function submitInlineForm()
{
	
		
		var inlineformrow = $('.inlineFormRow');
		inlineformrow.find(':input').attr('form', 'inlineForm');
		
		var id = inlineformrow.attr('data-id');
		var rowname = 'list_row_' + id; //read only row
		var rowobj = $('#' + rowname);
		
		//triggerLoading(trigg, 1);

		$.post($("#inlineForm").attr('action'), $("#inlineForm").serialize()+'&inlistform=1&ajax=1',
				function (data, status, request) {
						
						if (request.getResponseHeader('GW_AJAX_FORM') == 'OK')
						{
							
								rowobj.after(data);
								
								
								if(inlineformrow.attr('data-id')!='0')
									rowobj.remove();
								
								inlineformrow.remove();
								$('.activeList').trigger( "updated");//call init list
								
								var id = request.getResponseHeader('GW_AJAX_FORM_ITEM_ID');
								var title = request.getResponseHeader('GW_AJAX_FORM_ITEM_TITLE');
								var messages = request.getResponseHeader('GW_AJAX_MESSAGES');
								
																
								gwcms.showMessages(JSON.parse(messages), title);
								
								
								//console.log(messages);
								
								animateChangedRow(id);
								initActiveListRows();
								
								
								//gw_navigator.jump(location.href, {id:id})
						} else {
								inlineformrow.remove();
								loadRowAfterAjx(rowobj, data)
								
						}

				}
		)
}

function triggerLoading(obj, state)
{
		if(state==1)
		{
				$(obj).attr('data-loading-restore-html', $(obj).html());
				$(obj).html('<i class="fa fa-spinner fa-pulse"></i>');
		}else{
				$(obj).html($(obj).attr('data-loading-restore-html'));
		}
}

function triggerExpanded(obj, state)
{
		if(state==1)
		{
				$(obj).addClass('expanded')
				$(obj).attr('data-expanded-restore-html', $(obj).html());
				$(obj).html('<i class="fa fa-caret-square-o-down mouseout"></i><i class="fa fa-caret-square-o-up mouseover" style="display:none"></i>'+$(obj).html());
				$(obj).hover(
						function(){$(this).find('.mouseover').show();$(this).find('.mouseout').hide();},
						function(){$(this).find('.mouseover').hide();$(this).find('.mouseout').show();}
				)
		}else{
				$(obj).removeClass('expanded fa fa-arrow-circle-down');
				$(obj).html($(obj).attr('data-expanded-restore-html'));
		}
}



function openIframeUnderThisTr(trig, url, afterclose){
		
		var rowobj = $(trig).closest('tr');
		var id = $(rowobj).attr('data-id');
		var rowaftername = 'list_row_'+id+'_after';
		
		if($(trig).hasClass('expanded'))
		{
			triggerExpanded(trig, 0);
			$('#'+rowaftername).remove();
			
			if(afterclose)
					eval(afterclose);
			
			return false;
		}
				
		triggerExpanded(trig, 1);
		triggerLoading(trig, 1);
		
		loadRowAfter(rowobj, "<td colspan='100'><iframe class='iframeunderrow iframe_auto_sz' src='"+url+"' style='width:100%'></td></iframe>", 'iframeunderrowcont');
		
		$('#'+rowaftername+' .iframeunderrow').load(function(){
				triggerLoading(trig, 0);
				$(trig).addClass('expanded');
		})
		
		$('.iframe_auto_sz').load(function(){
				//if($('.iframe_auto_sz').attr('data-ifrm_auto_sz_init'))
				//		return false;
					
					var ifrm = this
					var ifrmcont = $(this).contents();
					
					
					var iframe_content = $(this).contents().find('body');
					var src =  this.contentWindow.location.href
					
					if(src.indexOf('iframeclose=1')!=-1)
					{
												
							//this will close iframe
							openIframeUnderThisTr(trig, url, afterclose);
					}
							
					
					iframe_content.resize(function(){ 
						$(ifrm).height(ifrmcont.height()+20);
					});
					
					iframe_content.resize();
					setTimeout(function(){
							iframe_content.resize()
					},1000);
					
				//$('.iframe_auto_sz').attr('data-ifrm_auto_sz_init'))
		})		
				
}
