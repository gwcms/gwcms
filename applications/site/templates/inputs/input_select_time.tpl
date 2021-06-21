{$tag_params['data-minuteseconval']=$value}
{include file="inputs/input_select.tpl" options=[] addclass="`$addclass` minutesecondopts"}
<script>
	$(function(){
		$(".minutesecondopts:not([data-initdone='1'])").each(function(){
			var opts="";
			for(var min=0;min<60;min++){
				for(var sec=0;sec<6;sec++){
					var opt=GW.zero(min,2)+':'+GW.zero(sec*10,2);
					opts+="<option value='"+opt+"'>"+opt+"</option>";					
				}
			}
			$(this).append(opts);
			$(this).val($(this).data('minuteseconval'));
		}).attr('data-initdone',1);		
	})
</script>