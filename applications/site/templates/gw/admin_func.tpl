{if ($app->user && $app->user->is_admin) || GW::$devel_debug || GW::s('DEVELOPER_PRESENT')}	
        <style>
                .lnresulthighl{
			background-color: brown !important;
			color: white !important;
		}
                .transover{
			background-color: blue !important;
		}
        </style>
        <script>
		var gw_lang_results_active = {intval($app->sess['lang-results-active'])};
		var gw_ln = "{$app->ln}";
        </script>
        <script src="{$app_root}assets/js/admin.js?v={$GLOBALS.version_short}"></script>


	<script src="/vendor/ckeditor422/ckeditor.js"></script>
	{*TODO:::   perkelt js i ckedit_inline.js, padaryt redirecta i login jei is admin gaunamas need autj - igyvendint  *}
	{literal}

		
		
		<script>
			function starInlineEditing(el){
				console.log(el);
					const dataId = el.dataset.pageid || '';
					const dataKey = el.dataset.contentkey || '';

					fetch('/admin/' + GW.ln + '/sitemap/pages/' + dataId + '/form?json=1&inpname=' + dataKey)
						.then(res => res.text())
						.then(response => {
							try{
								var pagedata = JSON.parse(response)

								console.log(pagedata);
								


								var inpdata = pagedata['input_data'][dataKey];

								el.data_parent_id = pagedata.parent_id;

								startEdit(el, inpdata['value'], inpdata['multilang']);
							}catch(err){
								console.log(err)
								
								
								if (confirm("Admin authorisation required. Do you want to open the admin window?")) {
								    // Open a new window with specific size
								    window.open(
									"/admin",           // URL to open
									"AdminWindow",      // Window name
									"width=500,height=500" // Window features
								    );
								}								
							
								//GW.open_dialog2({ url: '/admin', iframe:1, title:"Login is required" })
							}
							//inlineEditNotification(el, ' response received! ');
						})
				}
			
			
				function initProtected(id)	
				{
					
					
				}			
			
			
				function findContainer(el) {
					while(el) {
					  if(el.classList && el.classList.contains('ckedit-container')) return el;
					  el = el.parentNode;
					}
					return null;
				}

				function inlineEditNotification(el, str)
				{
					const container =findContainer(el) 
					if(!container)
						alert(str)

					const msg = document.createElement('div');
					msg.className = 'save-message';
					msg.textContent = str;
					container.appendChild(msg);

					// Remove after animation
					msg.addEventListener('animationend', () => msg.remove());
				}
				
				function cancelInlineEditing(el){
					
					
					
					var editor = CKEDITOR.instances[el.id];
					editor.destroy();
					el.innerHTML = el.originalHTML; // restore old content
					inlineEditNotification(el, 'Editing canceled');	
					el.setAttribute('contenteditable', 'false');
					changeEditBtnText(el,"Edit")
				}
				
				function changeEditBtnText(el, str)
				{
					var c = findContainer(el)
					
					const editbtn = c.querySelector('.edit-inline-btn');
					console.log(editbtn)
					editbtn.textContent = str;
				}


				const restoreSmarty = (html) => {
					html = html.replace(/<span[^>]*data-protected="([^"]+)"[^>]*>(.*?)<\/span>/g,
						function (match, code, innerContent) {
						return innerContent; // Remove span but keep content
						}
					);
					html = html.replace(/&gt;/g, '>').replace(/&lt;/g, '<');
					
					return html
				};

				function startEdit(el, content, multilang, index) {

					
					changeEditBtnText(el,"Escape")
					
					const dataId = el.dataset.pageid || '';
					const dataKey = el.dataset.contentkey || '';
					// Assign a unique ID
					
										
					el.setAttribute('contenteditable', 'true');


					// Save original HTML to restore on cancel
					el.originalHTML = el.innerHTML;

		//content is replaced with unprocessed one
					if (content)
						el.innerHTML = content;

					var config = {
						allowedContent: true,
						extraAllowedContent: '*(*);*{*}',
						contentsCss: [],
						removePlugins: 'maximize,resize',

						toolbar: [
							{name: 'document', items: ['CustomSave', 'EditInAdmin', '-', 'Source']},
							{name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat']},
							{name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent']},
							{name: 'links', items: ['Link', 'Unlink']},
							{name: 'insert', items: ['Image', 'Table']}
						]
					};
					config.protectedSource = [];
					config.protectedSource.push( /\{[\s\S]*?\}/g );
					
					config.extraPlugins = 'dialog,codemirror,filebrowser,showprotected1'
	
					config.entities = false;
					config.basicEntities = false;
					config.entities_greek = false;
					config.entities_latin = false;	
					config.filebrowserBrowseUrl = '/admin/'+GW.ln+'/sitemap/repository/fileselect';
					config.filebrowserImageBrowseUrl = config.filebrowserBrowseUrl
					

					// Create CKEditor inline instance
					const editor = CKEDITOR.inline(el.id, config);
					
					initProtected(el.id);
					
					
					//kad parodyt toolbar?
					el.focus();

					inlineEditNotification(el, 'Editing start');
					// Register custom Save command
					editor.addCommand('customSaveCmd', {
						exec: function (editor) {

							//
							var html = editor.getData();
							html = restoreSmarty(html);

							// Prepare URL-encoded POST data
							const formData = new URLSearchParams();
							formData.append('act', 'do:save');
							formData.append('item[id]', dataId);

							//
							formData.append('item[input_data][' + dataKey + (multilang ? '_' + GW.ln : '') + ']', html);
							formData.append('item[parent_id]', el.data_parent_id);


							fetch('/admin/' + GW.ln + '/sitemap/pages/' + dataId + '/form?dialog=1', {// ← your PHP endpoint
								method: 'POST',
								headers: {'Content-Type': 'application/x-www-form-urlencoded'},
								body: formData.toString()
							})
								.then(res => res.text())
								.then(response => {
									console.log('Server response:', response);
									editor.destroy();

									inlineEditNotification(el, ' Content saved! ');
									
									console.log('TODO: reiktu patikrint ar tikrai pavyko savinimas tik tada perkrauti');
									location.reload();
								})
								.catch(err => {
									console.error('Save error:', err);
									alert('Failed to save content!');
								});


							// Temporary visual feedback
							//inlineEditNotification(' Content saved! ');
						}
					});
					
					editor.addCommand('EditInAdminCmd', {
						exec: function (editor) {
							var dataId = el.dataset.pageid || '';
							location.href = '/admin/' + GW.ln + '/sitemap/pages/' + dataId + '/form';
						}
					});					
					
					

					// Add custom Save button with icon
					editor.ui.addButton('CustomSave', {
						label: 'Save',
						command: 'customSaveCmd',
						toolbar: 'document',
						icon: '   https://cdn-icons-png.flaticon.com/512/489/489707.png' // floppy disk icon
					});

					editor.ui.addButton('EditInAdmin', {
						label: 'Edit in admin',
						command: 'EditInAdminCmd',
						toolbar: 'document',
						icon: 'https://cdn-icons-png.flaticon.com/512/1548/1548672.png' // floppy disk icon
					});
					// Listen for ESC key to cancel editing
					editor.on('contentDom', function () {
						editor.document.on('keydown', function (event) {
							if (event.data.getKey() === 27) { // ESC
								event.data.preventDefault();
								cancelInlineEditing(el);

							}
						});
					});
				}



				document.querySelectorAll('.ckedit').forEach((el, index) => {
				
					  const container = document.createElement('div');
						container.classList.add('ckedit-container');
						el.parentNode.insertBefore(container, el);
						container.appendChild(el);
						
						  const btn = document.createElement('button');
						btn.textContent = 'Edit';
						btn.className = 'edit-inline-btn';
						container.appendChild(btn);
						
						el.id = 'inlineeditor_' + index;
						
						
						btn.addEventListener('click', (e) => {
							if (el.getAttribute('contenteditable') === 'true') {
								cancelInlineEditing(el);
							}else{
								starInlineEditing(el);
							}
						})

					el.addEventListener('click', (e) => {						
						
						const dataId = el.dataset.pageid || '';
						const dataKey = el.dataset.contentkey || '';

						if(e.ctrlKey){
							location.href = '/admin/' + GW.ln + '/sitemap/pages/' + dataId + '/form';
							return;
						}

						if (!e.shiftKey)
							return;

						e.preventDefault();
						
						starInlineEditing(el);
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
  position: absolute;
  top: 5px;
  right: 5px;
  background: #4caf50;
  color: #fff;
  padding: 5px 10px;
  border-radius: 4px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  z-index: 999;
  animation: fadeOut 2s ease 1s forwards;
  font-size: 12px;
  
}

.edit-inline-btn {
  position: absolute;
  top: 2px;
  right: 2px;
  color: orange;
  border: none;
  padding: 1px 1px;
  font-size: 11px;
  border-radius: 0px;
  cursor: pointer;
  background:transparent;
  border: 1px solid orange;
  transition: border 0.2s;
  z-index: 10;
  line-height: 10px;
}
.edit-inline-btn:hover {
  
}
.ckedit-container {
  position: relative; /* for button positioning */
}			

			@keyframes fadeOut {
				to {
					opacity: 0;
					transform: translateX(-50%) translateY(10px);
				}
			}
		</style>
	{/literal}


{/if}
