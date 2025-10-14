CKEDITOR.dialog.add('showProtectedDialog', function (editor) {
    return {
        title: 'Show Protected Code',
        minWidth: 400,
        minHeight: 200,
        contents: [
            {
                id: 'main',
                label: 'Protected Code',
                elements: [
                    {
                        type: 'textarea',
                        id: 'protectedCode',
                        label: 'Code',
                        inputStyle: 'height:150px;width:100%;font-family:monospace;'
                    }
                ]
            }
        ],

        onShow: function () {
            var selection = editor.getSelection();
            var selectedElement = selection ? selection.getSelectedElement() : null;

            // Try to find <span> if clicked inside text
            if (!selectedElement) {
                var ranges = selection.getRanges();
                if (ranges.length > 0) {
                    var startNode = ranges[0].startContainer;
                    selectedElement = startNode.getAscendant('span', true);
                }
            }

            if (selectedElement && selectedElement.hasClass('cke_protected')) {
                this._.selectedElement = selectedElement;

                var protectedData = selectedElement.getAttribute('data-protected') || '';

                // 🧩 Decode entities for readability inside the textarea
                var decodedData = CKEDITOR.tools.htmlDecode(protectedData)
                    .replace(/&lt;/g, '<')
                    .replace(/&gt;/g, '>')
                    .replace(/&amp;/g, '&');

                this.setValueOf('main', 'protectedCode', decodedData);
            } else {
                alert("⚠️ Please select a protected element before opening the dialog.");
                this._.selectedElement = null;
            }
        },

	onOk: function () {
	    var newCode = this.getValueOf('main', 'protectedCode');
	    var element = this._.selectedElement;

	    if (element && element.hasClass('cke_protected')) {
		// Encode HTML entities for safe display in WYSIWYG
		var encoded = CKEDITOR.tools.htmlEncode(newCode);

		// Keep encapsulation — update attributes + visible text
		element.setAttribute('data-protected', encoded);
		element.setAttribute('title', newCode);
		element.setText(newCode); // Visible text remains encoded

		// Force editor to recognize the update
		editor.fire('change');
	    } else {
		alert("⚠️ Could not update the protected element. Make sure it's selected.");
	    }
	}
    };
});