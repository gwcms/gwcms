CKEDITOR.plugins.add('showprotected1', {
    requires: 'dialog,fakeobjects',

    onLoad: function () {
        var baseStyle = 'background: #f4f4f4; color: #d9534f; font-family: monospace; white-space: pre;';
        var template = '.%2 span.cke_protected {' + baseStyle + '}';

        function cssWithDir(dir) {
            return template.replace(/%2/g, 'cke_contents_' + dir);
        }

        CKEDITOR.addCss(cssWithDir('ltr') + cssWithDir('rtl'));
    },

    init: function (editor) {
        CKEDITOR.dialog.add('showProtectedDialog', this.path + 'dialogs/protected.js');

        editor.on('doubleclick', function (evt) {
            var element = evt.data.element;

            if (element.is('span') && element.hasClass('cke_protected')) {
                evt.data.dialog = 'showProtectedDialog';

                if (element.hasAttribute('data-protected')) {
                    editor._protectedCode = element.getAttribute('data-protected');
                }
            }
        });

        // Convert raw {SMARTY} to protected span when loading content
        editor.dataProcessor.dataFilter.addRules({
            text: function (text) {
                return text.replace(/\{[\s\S]+?\}/g, function (match) {
                    return '<span class="cke_protected" contenteditable="false" data-protected="' + CKEDITOR.tools.htmlEncode(match) + '" title="' + CKEDITOR.tools.htmlEncode(match) + '">' + CKEDITOR.tools.htmlEncode(match) + '</span>';
                });
            }
        });

        // Ensure spans remain visible in WYSIWYG mode
        editor.dataProcessor.htmlFilter.addRules({
            elements: {
                span: function (element) {
                    if (element.attributes && element.attributes['data-protected']) {
                        element.children = [
                            new CKEDITOR.htmlParser.text(element.attributes['data-protected'])
                        ];
                    }
                }
            }
        });
    },

    afterInit: function (editor) {
        var dataProcessor = editor.dataProcessor;
        var dataFilter = dataProcessor && dataProcessor.dataFilter;

        if (dataFilter) {
            dataFilter.addRules({
                comment: function (commentText) {
                    if (commentText.indexOf(CKEDITOR.plugins.showprotected1.protectedSourceMarker) === 0) {
                        var cleanedCommentText = CKEDITOR.plugins.showprotected1.decodeProtectedSource(commentText);

                        var spanElement = new CKEDITOR.htmlParser.element('span', {
                            'class': 'cke_protected',
                            'contenteditable': 'false',
                            'data-protected': cleanedCommentText,
                            'title': cleanedCommentText
                        });

                        spanElement.add(new CKEDITOR.htmlParser.text(cleanedCommentText));

                        return spanElement;
                    }
                    return null;
                }
            });
        }

        var protectedRegex = /\{[\s\S]*?\}/g;

	editor.dataProcessor.dataFilter.addRules({
	    text: function (text) {
		return text.replace(/\{[\s\S]+?\}/g, function (match) {
		    return '<span class="cke_protected" contenteditable="false" data-protected="' + match + '" title="' + match + '">' + match + '</span>';
		});
	    }
	});

        editor.dataProcessor.htmlFilter.addRules({
            elements: {
                span: function (element) {
                    if (element.attributes && element.attributes['data-protected']) {
                        return new CKEDITOR.htmlParser.text(element.attributes['data-protected']);
                    }
                }
            }
        });

	editor.on('mode', function () {
	    if (editor.mode === 'source') {
		setTimeout(function () { // Ensure Source mode is fully loaded
		    var sourceData = editor.getData();

		    // Remove <span> but keep its raw content intact
		    var cleanedSource = sourceData.replace(/<span[^>]*data-protected="([^"]+)"[^>]*>(.*?)<\/span>/g, function (match, code, innerContent) {
			return CKEDITOR.tools.htmlDecode(code); // Ensure unescaped content is restored
		    });

		    if (sourceData !== cleanedSource) {
			editor.setData(cleanedSource); // Apply only if changes detected
		    }
		}, 100); // Small delay to avoid CKEditor conflicts
	    }
	});
	
	

        // Clean <span> elements before saving
        editor.on('save', function (evt) {
            var cleanData = editor.getData().replace(/<span[^>]*data-protected="([^"]+)"[^>]*>(.*?)<\/span>/g, function (match, code, innerContent) {
                return innerContent; // Keep content, remove <span>
            });

            evt.cancel(); // Stop default save
            editor.fire('saveSnapshot'); // Ensure CKEditor saves the cleaned version

  
           
        });	
	
    }
});

CKEDITOR.plugins.showprotected1 = {
    protectedSourceMarker: '{cke_protected}',

    decodeProtectedSource: function (protectedSource) {
        if (protectedSource.indexOf('%3C!--') === 0) {
            return decodeURIComponent(protectedSource).replace(/<!--\{cke_protected\}([\s\S]+?)-->/g, function (match, data) {
                return decodeURIComponent(data);
            });
        } else {
            return decodeURIComponent(protectedSource.substr(CKEDITOR.plugins.showprotected1.protectedSourceMarker.length));
        }
    },

    encodeProtectedSource: function (protectedSource) {
        return '<!--' + CKEDITOR.plugins.showprotected1.protectedSourceMarker +
            encodeURIComponent(protectedSource).replace(/--/g, '%2D%2D') +
            '-->';
    }
};
