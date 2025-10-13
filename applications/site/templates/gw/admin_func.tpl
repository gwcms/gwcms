{if ($app->user && $app->user->is_admin) || GW::$devel_debug || GW::s('DEVELOPER_PRESENT')}	
        <style>
                .lnresulthighl{ background-color: brown !important; color: white !important; }
                .transover{ background-color: blue !important; }
        </style>
        <script>
                var gw_lang_results_active = {intval($app->sess['lang-results-active'])};
                var gw_ln = "{$app->ln}";
        </script>
        <script src="{$app_root}assets/js/admin.js?v={$GLOBALS.version_short}"></script>
		
		
<script src="/vendor/ckeditor422/ckeditor.js"></script>
{literal}

<script>
	
function inlineEditNotification(str)
{
	var msg = document.createElement('div');
        msg.textContent = '✅ '+str+' ';
        msg.style.cssText = 'position:absolute;background:#333;color:#fff;padding:6px 12px;border-radius:6px;top:10px;right:10px;z-index:9999;';
        document.body.appendChild(msg);
        setTimeout(() => msg.remove(), 2000);	
}


  const restoreSmarty = (html) => {
    const div = document.createElement('div');
    div.innerHTML = html;
    div.querySelectorAll('.smarty-placeholder').forEach(ph => {
      const code = decodeURIComponent(ph.getAttribute('data-code'));
      ph.outerHTML = code;
    });
    return div.innerHTML;
  };
	
	
	
document.querySelectorAll('.ckedit').forEach((el, index) => {
  el.addEventListener('click', (e) => {
    if (!e.shiftKey) return;
    e.preventDefault();

    // Assign a unique ID
    el.id = 'shifteditor_' + index;
    el.setAttribute('contenteditable', 'true');
     const dataId = el.dataset.pageid || '';
     const dataKey = el.dataset.contentkey || '';
     const dataLn = el.dataset.ln || '';
      const data = el.dataset.id || '';
    
        // Save original HTML to restore on cancel
     const originalHTML = el.innerHTML;

    // Create CKEditor inline instance
    const editor = CKEDITOR.inline(el.id, {
      allowedContent: true,
      extraAllowedContent: '*(*);*{*}',
      contentsCss: [],
      removePlugins: 'maximize,resize',

      toolbar: [
        { name: 'document', items: ['CustomSave', '-', 'Source'] },
        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
        { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
        { name: 'links', items: ['Link', 'Unlink'] },
        { name: 'insert', items: ['Image', 'Table'] }
      ]
    });

 inlineEditNotification('Editing start');
    // Register custom Save command
    editor.addCommand('customSaveCmd', {
      exec: function (editor) {
        
	//const html = restoreSmarty(editor.getData());
	const html = editor.getData();

        // Prepare URL-encoded POST data
        const formData = new URLSearchParams();
	formData.append('act', 'do:save');
        formData.append('item[id]', dataId);
	 
	//
       formData.append('item[input_data]['+dataKey+(dataLn?'_'+dataLn:'')+']', html);

	
        fetch('/admin/lt/sitemap/pages/'+dataId+'/form', { // ← your PHP endpoint
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: formData.toString()
        })
        .then(res => res.text())
        .then(response => {
          console.log('Server response:', response);
          editor.destroy();

          const msg = document.createElement('div');
          msg.textContent = '✅ Content saved!';
          msg.style.cssText = 'position:fixed;background:#333;color:#fff;padding:6px 12px;border-radius:6px;bottom:15px;right:15px;z-index:9999;';
          document.body.appendChild(msg);
          setTimeout(() => msg.remove(), 2000);
        })
        .catch(err => {
          console.error('Save error:', err);
          alert('Failed to save content!');
        });
      

        // Temporary visual feedback
        inlineEditNotification(' Content saved! ');
      }
    });

    // Add custom Save button with icon
    editor.ui.addButton('CustomSave', {
      label: 'Save',
      command: 'customSaveCmd',
      toolbar: 'document',
      icon: '   https://cdn-icons-png.flaticon.com/512/489/489707.png' // floppy disk icon
    });
    
    
     // Listen for ESC key to cancel editing
  editor.on('contentDom', function() {
      editor.document.on('keydown', function(event) {
        if (event.data.getKey() === 27) { // ESC
          event.data.preventDefault();
          editor.destroy();
          el.innerHTML = originalHTML; // restore old content
	   inlineEditNotification('Editing canceled');
         
        }
      });
    });   
    
    
  });
});
</script>

<style>
  .ckedit {
    border: 2px dashed transparent;
    padding: 4px;
    transition: border-color 0.2s;
  }
  .ckedit:hover {
    border-color: brown;
  }
  .ckedit:focus-within {
    border-color: green;
  }

  /* Temporary message style */
  .save-message {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #4caf50;
    color: #fff;
    padding: 10px 20px;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    z-index: 9999;
    animation: fadeOut 2s ease 1s forwards;
  }

  @keyframes fadeOut {
    to { opacity: 0; transform: translateX(-50%) translateY(10px); }
  }
</style>
{/literal}

		
{/if}
