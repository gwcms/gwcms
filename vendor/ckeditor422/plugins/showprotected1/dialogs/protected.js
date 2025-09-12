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
                        inputStyle: 'height:150px;width:100%;'
                    }
                ]
            }
        ],
        onShow: function () {
            var selection = editor.getSelection();
            console.log("Selection object:", selection);

            var selectedElement = selection ? selection.getSelectedElement() : null;
            console.log("Selected element:", selectedElement);

            // 🛠️ If no direct selection, try to find a protected span inside the selection
            if (!selectedElement) {
                var ranges = selection.getRanges();
                console.log("Selection ranges:", ranges);

                if (ranges.length > 0) {
                    var startNode = ranges[0].startContainer;
                    console.log("Start node:", startNode);

                    selectedElement = startNode.getAscendant('span', true);
                }
            }

            console.log("Final selected element:", selectedElement);

            // ✅ Ensure we have the protected element
            if (selectedElement && selectedElement.hasClass('cke_protected')) {
                this._.selectedElement = selectedElement;

                var protectedData = selectedElement.getAttribute('data-protected') || '';
		//alert(protectedData);
                //var decodedData = CKEDITOR.plugins.showprotected1.decodeProtectedSource(protectedData);
		decodedData = protectedData;
		
                console.log("Decoded protected data:", decodedData);

                this.setValueOf('main', 'protectedCode', decodedData);
            } else {
                console.warn("⚠️ No protected element selected.");
                alert("Please select a protected element before opening the dialog.");
                this._.selectedElement = null;
            }
	    
	    
        },
        onOk: function () {
            var newCode = this.getValueOf('main', 'protectedCode');
            var element = this._.selectedElement;

            if (element && element.hasClass('cke_protected')) {
                var encodedCode = CKEDITOR.plugins.showprotected1.encodeProtectedSource(newCode);

                element.setAttribute('data-protected', encodedCode);
                element.setAttribute('title', newCode);
                element.setAttribute('alt', newCode);
                element.setText(newCode); // Update the visible text inside the span

                // 🔄 Force CKEditor to recognize the update
                editor.updateElement();
            } else {
                alert("⚠️ Could not update the protected element. Make sure it's selected.");
            }
        }
    };
});
