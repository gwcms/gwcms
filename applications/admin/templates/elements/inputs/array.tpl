{$GLOBALS.arrayObjectContainer=$GLOBALS.arrayObjectContainer+1}
{$idx=$GLOBALS.arrayObjectContainer}

<div class="arrayObjectContainer">
  <style>

    .tree { font-size:14px;line-height:1.4}
    .node{ margin-left:12px;position:relative}
    .node > .row{ display:flex;align-items:center;gap:8px}
    .toggler{ cursor:pointer;user-select:none;width:18px;text-align:center}
    .key{ font-weight:600}
    .meta{ color:#666;font-size:12px;margin-left:6px}
    .value{ margin-left:6px}
    .collapsed > .children{ display:none}
    .children{ margin-left:12px;border-left:1px dashed rgba(0,0,0,0.04);padding-left:8px}
    .primitive{ padding:2px 6px;border-radius:4px;background:rgba(0,0,0,0.03)}
    
    .arrayObjectContainer{ position: relative; height: auto; }
    .controls{ margin-bottom:10px; float:right; position:absolute; top:0;right:0; }
    .btn-input {  background-color: #eee;padding:1px 5px; font-size:16px !important; cursor: pointer }
  </style>



	<div style="clear:both"></div>
  <div id="tree{$idx}" class="tree"></div>

  <!-- jQuery (required) -->


  <script>
  /*
    Usage:
      $('#tree').arrayTree(data, { expand_levels: 1 });

    Options:
      expand_levels: how many levels are expanded by default (0 = everything collapsed)
      keyFormatter: function(key) -> html/text for the key (optional)
  */
 require(['gwcms'], function(){
 
 
 
 (function($){
    $.fn.arrayTree = function(data, options){
      options = $.extend({ expand_levels: 0, keyFormatter: null }, options || {});
      const container = this;

      function typeOf(v){
        if (v === null) return 'null';
        if (Array.isArray(v)) return 'array';
        return typeof v;
      }

      function renderNode(key, value, depth){
        const t = typeOf(value);
        const $node = $('<div class="node"></div>');
        const $row = $('<div class="row"></div>');
        const isCollapsible = (t === 'array' || t === 'object');

        const $toggler = $('<div class="toggler"></div>');
        if (isCollapsible){
          $toggler.text(depth < options.expand_levels ? '−' : '+');
        } else {
          $toggler.html('&nbsp;');
        }

        const $key = $('<div class="key"></div>');
        if (key !== undefined && key !== null) {
          const formattedKey = options.keyFormatter ? options.keyFormatter(key) : String(key);
          $key.html(formattedKey);
        }

        const $meta = $('<div class="meta"></div>');
        if (isCollapsible){
          $meta.text(t === 'array' ? ('array['+value.length+']') : 'object');
        } else {
          $meta.text(t);
        }

        $row.append($toggler);
        if (key !== undefined && key !== null) $row.append($key);
        $row.append($meta);

        if (!isCollapsible){
          const $val = $('<div class="value primitive"></div>');
          let display;
          if (t === 'string') display = '"' + String(value) + '"';
          else if (t === 'undefined') display = 'undefined';
          else display = String(value);
          $val.text(display);
          $row.append($val);
        }

        $node.append($row);

        if (isCollapsible){
          const $children = $('<div class="children"></div>');
          const entries = (t === 'array') ? value.map((v,i)=>[i,v]) : Object.entries(value);
          entries.forEach(([k,v])=>{
            $children.append(renderNode(k, v, depth+1));
          });
          // initial collapsed state depending on depth vs expand_levels
          if (depth < options.expand_levels) {
            // expanded
            $node.removeClass('collapsed');
            $toggler.text('−');
          } else {
            $node.addClass('collapsed');
            $toggler.text('+');
          }

          $node.append($children);

          $toggler.on('click', function(){
            if ($node.hasClass('collapsed')){
              $node.removeClass('collapsed');
              $toggler.text('−');
            } else {
              $node.addClass('collapsed');
              $toggler.text('+');
            }
          });

          // also toggle when clicking key or meta
          $row.find('.key, .meta').on('click', function(){ $toggler.trigger('click'); });
        }

        return $node;
      }

      function build(){
        container.empty();
        // top-level: if object/array with no key, render children directly
        const topType = typeOf(data);
        if (topType === 'array' || topType === 'object'){
          const wrapper = $('<div></div>');
          const entries = (topType === 'array') ? data.map((v,i)=>[i,v]) : Object.entries(data);
          entries.forEach(([k,v])=> wrapper.append(renderNode(k, v, 0)));
          container.append(wrapper);
        } else {
          container.append(renderNode(null, data, 0));
        }
      }

      build();

      // expose some controls
      container.data('arrayTree', {
        expandAll: function(){ container.find('.node.collapsed').each(function(){ $(this).removeClass('collapsed').find('> .row .toggler').text('−'); }); },
        collapseAll: function(){ container.find('.node').each(function(){ $(this).addClass('collapsed').find('> .row .toggler').text('+'); }); },
        rebuild: function(newData, newOptions){ if (newData !== undefined) data = newData; if (newOptions) options = $.extend(options, newOptions); build(); },
	showRaw: function(){ $('#rawcontent{$idx}').toggle() }
      });

      return this;
    };
  })(jQuery); 
 

  // --- Example usage ---
  $(function(){
    
    // default collapsed (expand_levels:0)
    $('#tree{$idx}').arrayTree({json_encode($value)}, { expand_levels: 1 });

    $('#expandAll{$idx}').on('click', function(){ $('#tree{$idx}').data('arrayTree').expandAll(); });
    $('#collapseAll{$idx}').on('click', function(){ $('#tree{$idx}').data('arrayTree').collapseAll(); });
    $('#showRaw{$idx}').on('click', function(){ $('#tree{$idx}').data('arrayTree').showRaw(); });
  });	 
	 
	 	
	
})

  </script>
  
  <textarea id="rawcontent{$idx}" style="display:none;width:100%;height:400px">{json_encode($value, $smarty.const.JSON_PRETTY_PRINT)}</textarea>
  <div class="controls">
 <span id="expandAll{$idx}" class="btn-input material-symbols-outlined">expand_content</span>
<span id="collapseAll{$idx}" class="btn-input material-symbols-outlined">collapse_content</span>
    <span id="showRaw{$idx}" class="btn-input material-symbols-outlined" style="font-size:24px !important;line-height:16px">raw_on</span>

  </div>  
</div>