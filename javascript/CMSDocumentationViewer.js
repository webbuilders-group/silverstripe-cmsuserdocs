(function($) {
    $.entwine('ss', function($) {
        /**
         * TABLE OF CONTENTS
         * 
         * Transform a #table-of-contents div to a nested list
         */
        $('.CMSDocumentationViewer .cmsdocviewer-page-content #table-contents-holder').entwine({
            onadd: function(e) {
                var toc = '<div id="table-of-contents" class="open">' +
                      '<h4>'+ss.i18n._t('CMSDocumentationViewer.TABLE_OF_CONTENTS', '_Table of contents')+'<span class="updown">&#9660;</span></h4><ul style="display: none;">';
                
                // Remove existing anchor redirection in the url
                var pageURL = window.location.href.replace(/#[a-zA-Z0-9\-\_]*/g, '');
                
                var itemCount = 0;
                $('.CMSDocumentationViewer .cmsdocviewer-page-content h1[id], .CMSDocumentationViewer .cmsdocviewer-page-content h2[id], .CMSDocumentationViewer .cmsdocviewer-page-content h3[id], .CMSDocumentationViewer .cmsdocviewer-page-content h4[id]').each(function(i) {
                    var current=$(this);
                    
                    var tagName=current.prop("tagName");
                    if(typeof tagName == "String") {
                        tagName = tagName.toLowerCase();
                    }
                    
                    itemCount++;
                    
                    toc += '<li class="'+tagName+'"><a id="link'+i+'" href="'+pageURL+'#'+$(this).attr('id')+'" title="'+current.text()+'">'+current.text()+'</a></li>';
                });
                
                // if no items in the table of contents, don't show anything
                if(itemCount == 0) {
                    return false;
                }
                
                toc += '</ul></div>';
                
                $('.CMSDocumentationViewer .cmsdocviewer-page-content #table-contents-holder').prepend(toc);
            }
        });
        
        /**
         * Table of contents toggling
         */
        $('.CMSDocumentationViewer .cmsdocviewer-page-content #table-of-contents').entwine({
            onadd: function() {
                // Make sure clicking a link won't toggle the TOC
                $('.CMSDocumentationViewer .cmsdocviewer-page-content #table-of-contents li a').click(function (e) {
                    e.stopPropagation();
                });
            },
            onclick: function(e) {
                var list=$(this).children('ul');
                var iconSpan=$(this).find('h4 span');
                
                if(list.is(':visible')==false) {
                    console.log('show');
                    list.animate({'height':'show'}, 200, function() {
                        iconSpan.html('&#9650;');
                    });
                }else {
                    console.log('hide');
                    list.animate({'height':'hide'}, 200, function() {
                        iconSpan.html('&#9660;');
                    });
                }
            }
        });
        
        
        /**
         * HEADING ANCHOR LINKS
         * 
         * Automatically adds anchor links to headings that have IDs
         */
        $('.CMSDocumentationViewer .cmsdocviewer-page-content h1[id], .CMSDocumentationViewer .cmsdocviewer-page-content h2[id], .CMSDocumentationViewer .cmsdocviewer-page-content h3[id], .CMSDocumentationViewer .cmsdocviewer-page-content h4[id], .CMSDocumentationViewer .cmsdocviewer-page-content h5[id], .CMSDocumentationViewer .cmsdocviewer-page-content h6[id]').entwine({
            onadd: function() {
                var url=window.location.href;
                var link='<a class="heading-anchor-link" title="'+ss.i18n._t('CMSDocumentationViewer.LINK_TO_SECTION', '_Link to this section')+'" href="'+url+'#'+$(this).attr('id')+'"></a>';
                
                $(this).append(link);
            },
            
            onmouseenter: function() {
                $(this).addClass('hover');
            },
            
            onmouseleave: function() {
                $(this).removeClass('hover');
            }
        });
        
        /**
         * SYNTAX HIGHLIGHTER 
         * 
         * As the Markdown parser now uses the GFM structure (```yml) this does not work with prettyprint. The below translates the GFM output to one prettyprint can use
         */
        $('.CMSDocumentationViewer .cmsdocviewer-page-content pre').entwine({
            onadd: function() {
                var code=$(this).find('code[class^=language]');
                
                if(code.length>0) {
                    var brush=code.attr('class').replace('language-', '');
                    $(this).attr('class', 'prettyprint lang-' + brush);
                    
                    if(PR && PR.prettyPrint) {
                        PR.prettyPrint();
                    }
                }
            }
        });
        
        /**
         * Search Integration
         */
        $('.CMSDocumentationViewer .cmsdocviewer-menu form.search-form').entwine({
            onsubmit: function(e, button) {
                var url=$.path.addSearchParams(this.attr('action'), $(this).serialize());
                var data={pjax: 'Content'};
                
                $('.cms-container').loadPanel(url, null, data);
                
                return false;
            }
        });
    });
})(jQuery);