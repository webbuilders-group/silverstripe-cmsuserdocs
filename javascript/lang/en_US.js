if(typeof(ss) == 'undefined' || typeof(ss.i18n) == 'undefined') {
    if(typeof(console) != 'undefined') console.error('Class ss.i18n not defined');
}else {
    ss.i18n.addDictionary('en_US', {
        'CMSDocumentationViewer.TABLE_OF_CONTENTS': 'Table of contents',
        'CMSDocumentationViewer.LINK_TO_SECTION': 'Link to this section'
    });
}