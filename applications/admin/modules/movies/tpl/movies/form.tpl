{include file="default_form_open.tpl"}


{include file="elements/input.tpl" name=title}
{include file="elements/input.tpl" name=image1 type=image}
{include file="elements/input.tpl" name=name_orig}
{include file="elements/input.tpl" type=textarea name=description}
{include file="elements/input.tpl" name=rate type=select options=[0,1,2,3,4,5,6,7,8,9,10]}
{include file="elements/input.tpl" name=recommend}


<tr>
<td>
Imdb info
</td>
<td>

<div id="imdb_loading" style="display:none">Loading...</div>



<div id="imdb_info">
	{if $item && $item->image}<img src="{$app->sys_base}repository/{$item->image}" />{/if}
</div>



<script type="text/javascript">
$(function(){
	$("[name='item[name_orig]']").change(do_imdb());

	//imdb info field
	if(!$('#imdb_ta').val()){
		do_imdb();
	}else{
		load_imdb();
	}
})

function do_imdb()
{
	var title = $("[name='item[name_orig]']").val()
	
	if(!title)
		return false;
	
	$('#imdb_loading').show();
	
	$.get(GW.ln+'/'+GW.path+'/imdb',
			{
				'title': title,
				'act':'do:get_imdb'
			}, 
			function(data) {
				$('#imdb_loading').hide();
								
				$('#imdb_ta').val(data);

				load_imdb();
			});	 
}

function load_imdb()
{
	eval('var info='+$('#imdb_ta').val());
	
	var container = $('#imdb_info');

	var html = "<img style='float: left; margin-right: 10px;' src='repository/"+info.local_images.poster+"' >";

	html+='<a href="http://www.imdb.com/title/'+info.title_id+'/">'+info.title+' ('+info.rating+')'+'</a><br />';
	html+='<p>'+info.plot+'</p>';
	html+='<p>'+info.storyline+'</p>';
	
	container.html(html);
	$('#imdb_img').val(info.local_images.poster);
}




</script>

<textarea id="imdb_ta" style="width:100%;height:50px" name="item[imdb]">{if $item}{$item->imdb|escape}{/if}</textarea>
<input type="hidden" id="imdb_img"  name="item[image]" value="{$item->image|escape:'html'}">



</td>
</tr>



{include file="default_form_close.tpl"}