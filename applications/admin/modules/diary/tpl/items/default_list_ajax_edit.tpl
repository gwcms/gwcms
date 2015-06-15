
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>

<script type="text/javascript">

ckeditor_config = { 
	width:700, 
	extraPlugins : 'autogrow',
	autoGrow_maxHeight : 1000,	
	toolbar:
	[
		['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', 'Save'],
		['UIColor']
	]
}


{*save mygtuka aprasyti*}
CKEDITOR.plugins.registered['save']=
{
     init : function( editor )
     {
        var command = editor.addCommand( 'save', 
           {
              modes : { wysiwyg:1, source:1 },
              exec : function( editor ) {
            	  replaceDiv(1,1);
              }
           }
        );
        editor.ui.addButton( 'Save',{ label : 'Ajax Save',command : 'save' });
     }
  }

{*doubleclickas ijungs editoriu*}
window.onload = function()
{
	// Listen to the double click event.
	if ( window.addEventListener )
		document.body.addEventListener( 'dblclick', onDoubleClick, false );
	else if ( window.attachEvent )
		document.body.attachEvent( 'ondblclick', onDoubleClick );

};

function onDoubleClick( ev )
{
	// Get the element which fired the event. This is not necessarily the
	// element to which the event has been attached.
	var element = ev.target || ev.srcElement;

	// Find out the div that holds this element.
	var name;
	do
	{
		element = element.parentNode;
	}
	while ( element && ( name = element.nodeName.toLowerCase() ) && ( name != 'div' || element.className.indexOf( 'editable' ) == -1 ) && name != 'body' )


	if ( name == 'div' && element.className.indexOf( 'editable' ) != -1 )
		replaceDiv( element );
}

var editor;
var ajaxsaveargs;

function replaceDiv(div, destroy_only)
{
	if ( editor )
	{
		ajaxsave(ajaxsaveargs, editor.getData());
		editor.destroy();

		editor=false;

		if(destroy_only)
			return false;
	}

		
	ajaxsaveargs = $(div).attr('ajaxsaveargs');
		
	editor = CKEDITOR.replace( div, ckeditor_config);
	
}


	var editigid = 0;

	function ajaxsave(ajaxsaveargs, data)
	{
		ajaxsaveargs = eval('('+ajaxsaveargs+')');

		var path = GW.ln+'/'+GW.path;

		var params = { 'act': "do:ajax_save" }

		params['item['+ajaxsaveargs.name+']']=data;

		for (x in ajaxsaveargs.vals)
			params['item['+x+']']=ajaxsaveargs.vals[x];
		
		$.post(path, params);		
	}


</script>