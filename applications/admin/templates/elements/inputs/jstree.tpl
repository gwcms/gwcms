
{$GLOBALS.arrayObjectContainer=$GLOBALS.jstreeContainer+1}
{$idx=$GLOBALS.jstreeContainer}

<div class="jstreeContainer">
	
	

  <style>
    
    #jstree{$idx} { border: 1px solid #ccc; padding: 10px; border-radius: 6px; }
  </style>
</head>

{$m->addIncludes("upload_input/css", 'css', "`$app_root`static/vendor/jstree/dist/themes/default/style.min.css")}
	
	{capture append=footer_hidden}
	<style>
		.jstree{ background-color: white; padding: 10px; border: 1px solid silver; border-radius: 10px; display:inline-block }
		.gwTreeViewTbl td{ vertical-align: top;}
		
		
		    .jstreeContainer{ position: relative; height: auto; }
			.controls{ margin-bottom:10px; float:right; position:absolute; top:0;right:0; }
	</style>
	{/capture}


  <div id="jstree{$idx}"></div>
  <textarea 	name="{$input_name}"  name="json" id="jsonArea{$idx}" style="display:none;width:100%;height:300px">{json_encode((array)json_decode($value))}</textarea>



<script>



require(['gwcms'], function(){  require(['vendor/jstree/dist/jstree.min'], function(){ 


  const data = JSON.parse($('#jsonArea{$idx}').val());

  // Convert JSON → jsTree data
function jsonToJsTree(obj, key) {
  if (typeof obj === 'object' && obj !== null) {
    const children = [];
    if (Array.isArray(obj)) {
      obj.forEach((v,i) => children.push(jsonToJsTree(v, i)));
    } else {
      Object.keys(obj).forEach(k => children.push(jsonToJsTree(obj[k], k)));
    }
    return { text: key !== undefined ? key : '(root)', children: children };
  } else {
    return { 
      text: (key !== undefined ? key + ': ' + String(obj) : String(obj)),
      icon: 'jstree-file',
      data: { key: key, value: obj }   // store raw value
    };
  }
}

  // Initialize jsTree
$('#jstree{$idx}').jstree({
  core: {
    check_callback: true,
    data: (function(){
      if (Array.isArray(data)) {
        return data.map((v,i) => jsonToJsTree(v, i));
      } else {
        return Object.keys(data).map(k => jsonToJsTree(data[k], k));
      }
    })()
  },
  plugins: ["dnd", "contextmenu", "types", "wholerow"],
  contextmenu: {
    items: function(node) {
      // return only the actions you want
      return {
        renameItem: {
          label: "Rename",
          action: function(obj) {
            $('#jstree{$idx}').jstree(true).edit(node);
          }
        },
        deleteItem: {
          label: "Delete",
          action: function(obj) {
            $('#jstree{$idx}').jstree(true).delete_node(node);
          }
        }
        // no "create" here → so user can’t create via context menu
      };
      
	}
	}
      
});


function treeToJson(node) {
  if (!node.children.length) {
    // if raw value exists, return it instead of parsing text
    if (node.data && node.data.value !== undefined) {
      return node.data.value;
    }
    return node.text;
  }
  const result = {};
  node.children.forEach(id => {
    const child = $('#jstree{$idx}').jstree(true).get_node(id);
    const key = (child.data && child.data.key) ? child.data.key : child.text;
    result[key] = treeToJson(child);
  });
  return result;
}

function buildJson() {
  const tree = $('#jstree').jstree(true);
  const roots = tree.get_node('#').children;
  const result = { };
  roots.forEach(id => {
    const node = tree.get_node(id);
    const parts = node.text.split(':');
    const key = parts[0].trim();
    result[key] = treeToJson(node);
  });
  return result;
}

  $('#jstree{$idx}').on("changed.jstree move_node.jstree create_node.jstree delete_node.jstree rename_node.jstree", function () {
	  
	$('#jsonArea{$idx}').val(JSON.stringify(buildJson(), null, 2));
   
  });
  
  $('#jstrc_showRaw{$idx}').click(function(){
	$('#jsonArea{$idx}').fadeIn();
  })

  $('#jstrc_expandAll{$idx}').click(function(){
	$('#jstree{$idx}').jstree('open_all');
  })

  $('#jstrc_collapseAll{$idx}').click(function(){
	 
	$('#jstree{$idx}').jstree('close_all');
  })

  $('#jstrc_add{$idx}').click(function(){
	 
	var nodekey = prompt('Please provide node key');
	var nodeval = prompt('Please provide node value');
	
	var tree = $('#jstree').jstree(true);
		tree.create_node('#', { 
		  "text": nodekey+': '+nodeval, 
		  data: { key: nodekey, value: nodeval },
		  "icon": "jstree-file" 
		}, "last");

  })



});


})
</script>


 <div class="controls">
	 <span id="jstrc_add{$idx}" class="btn-input material-symbols-outlined">add_circle</span>
 <span id="jstrc_expandAll{$idx}" class="btn-input material-symbols-outlined">expand_content</span>
<span id="jstrc_collapseAll{$idx}" class="btn-input material-symbols-outlined">collapse_content</span>
    <span id="jstrc_showRaw{$idx}" class="btn-input material-symbols-outlined" style="font-size:24px !important;line-height:16px">raw_on</span>

  </div>  
    
</div>