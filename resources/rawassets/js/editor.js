$(document).ready(function () {
    $.trumbowyg.svgPath = '/admin-assets/vendor/trumbowyg/ui/icons.svg';
    $('.wysiwyg').trumbowyg({
        autogrow: false,
        removeformatPasted: true,
        btnsDef: {
            // Create a new dropdown
            image: {
                dropdown: ['insertImage', 'base64' /*, 'upload' */],
                ico: 'insertImage'
            },
            justify: {
                dropdown: ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ico: 'justifyLeft'
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
            ['justify'],
            ['unorderedList', 'orderedList'],
            ['horizontalRule'],
            ['table'],
            ['specialChars'],
            ['historyUndo', 'historyRedo'],
            ['removeformat'],
            ['fullscreen']
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
            },
            resizimg: {
                minSize: 64,
                step: 16,
            },
            imageWidthModalEdit: true,
            urlProtocol: true
        },
        semantic: {
            'b': 'strong',
            'i': 'em',
            's': 'del',
            'strike': 'del',
            'div': 'div'
        },
        resetCss: true,
        lang: editorLang
    });
});

jQuery.trumbowyg.langs.hu = {
    viewHTML: 'HTML nézet',

    formatting: 'Stílusok',

    p: 'Bekezdés',
    blockquote: 'Idézet',
    code: 'Kód',
    header: 'Címsor',

    bold: 'Félkövér',
    italic: 'Dőlt',
    strikethrough: 'Áthúzott',
    underline: 'Aláhúzott',

    strong: 'Vastag',
    em: 'Kiemelt',
    del: 'Törölt',

    unorderedList: 'Felsorolás',
    orderedList: 'Számozás',

    insertImage: 'Kép beszúrása',
    insertVideo: 'Video beszúrása',
    link: 'Link',
    createLink: 'Link létrehozása',
    unlink: 'Link eltávolítása',

    justifyLeft: 'Balra igazítás',
    justifyCenter: 'Középre igazítás',
    justifyRight: 'Jobbra igazítás',
    justifyFull: 'Sorkizárt',

    horizontalRule: 'Vízszintes vonal',

    fullscreen: 'Teljes képernyő',
    close: 'Bezár',

    submit: 'Beküldés',
    reset: 'Alaphelyzet',

    required: 'Kötelező',
    description: 'Leírás',
    title: 'Cím',
    text: 'Szöveg',

    removeformat: 'Formázás eltávolítása',

    base64: 'Kép beszúrás inline',
    file: 'Fájl',
    errFileReaderNotSupported: 'Ez a böngésző nem támogatja a FileReader funkciót.',
    errInvalidImage: 'Érvénytelen képfájl.',

    foreColor: 'Betű szín',
    backColor: 'Háttér szín',
    foreColorRemove: 'Betű szín eltávolítása',
    backColorRemove: 'Háttér szín eltávolítása',

    emoji: 'Emoji beszúrás',

    fontFamily: 'Betűtípus',

    fontsize: 'Betű méret',
    fontsizes: {
        'x-small': 'Extra kicsi',
        'small': 'Kicsi',
        'medium': 'Normális',
        'large': 'Nagy',
        'x-large': 'Extra nagy',
        'custom': 'Egyedi'
    },
    fontCustomSize: {
        title: 'Egyedi betű méret',
        label: 'Betű méret',
        value: '48px'
    },

    giphy: 'GIF beszúrás',

    highlight: 'Kód kiemelés',

    history: {
        redo: 'Visszállít',
        undo: 'Visszavon'
    },

    insertAudio: 'Audio beszúrás',

    lineheight: 'Line height',
    lineheights: {
        '0.9': 'Small',
        'normal': 'Regular',
        '1.5': 'Large',
        '2.0': 'Extra large'
    },

    mathml: 'Formulák beszúrás',
    formulas: 'Formulák',
    inline: 'Inline',

    mention: 'Említ',

    noembed: 'Noembed',
    noembedError: 'Hiba',

    preformatted: 'Kód minta <pre>',

    ruby: 'Ruby szöveg hozzáadás',
    rubyModal: 'Ruby modal',
    rubyText: 'Ruby szöveg',

    specialChars: 'Speciális karakterek',

    table: 'Táblázat beszúrás',
    tableAddRow: 'Sor hozzáadás',
    tableAddRowAbove: 'Sor beszúrás fönt',
    tableAddColumnLeft: 'Sor beszúrás balra',
    tableAddColumn: 'Sor beszúrás jobbra',
    tableDeleteRow: 'Sor törlés',
    tableDeleteColumn: 'Oszlop törlés',
    tableDestroy: 'Táblázat törlés',
    error: 'Hiba',

    template: 'Sablon',

    upload: 'Feltöltés',
    file: 'Fájl',
    uploadError: 'Hiba'
};
