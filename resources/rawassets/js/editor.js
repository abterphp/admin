$(document).ready(function () {
    $.trumbowyg.svgPath = '/admin-assets/vendor/trumbowyg/ui/icons.svg';
    $('.wysiwyg').trumbowyg({
        autogrow: true,
        btnsDef: {
            // Create a new dropdown
            image: {
                dropdown: ['insertImage', 'upload'],
                ico: 'insertImage'
            }
        },
        // Redefine the button pane
        btns: [
            ['viewHTML'],
            ['formatting'],
            ['strong', 'em', 'del'],
            ['superscript', 'subscript'],
            ['link'],
            ['image'], // Our fresh created dropdown
            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
            ['unorderedList', 'orderedList'],
            ['horizontalRule'],
            ['removeformat'],
            ['fullscreen'],
            ['table']
        ],
        plugins: {
            // Add imagur parameters to upload plugin for demo purposes
            upload: {
                serverPath: editorFileUploadPath,
                fileFieldName: 'image',
                headers: {
                    'Authorization': 'Client-ID ' + clientId
                },
                urlPropertyName: 'data.url'
            }
        }
    });
});
