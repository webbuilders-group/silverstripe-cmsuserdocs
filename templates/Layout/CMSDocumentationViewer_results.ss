<div class="cms-content center cmsdocviewer-content cmsdocviewer-results-content" data-layout-type="border" data-pjax-fragment="CurrentForm">
    <div class="cms-content-fields center cms-panel-padded ui-widget" data-layout-type="border">
        <% if $Results %>
            <p class="intro"><%t WebbuildersGroup\\CMSUserDocs\\Control\\CMSDocumentationViewer.SEARCH_RESULTS "_Your search for <strong>&quot;{query}&quot;</strong> found {total} result(s). Showing page {current_page} of {total_pages}." query=$Query.XML total=$TotalResults current_page=$ThisPage total_pages=$TotalPages %></p>
            
            <% loop $Results %>
                <div class="result">
                    <h2><a href="$Link" class="cms-panel-link" data-pjax-target="Content">$Title.XML</a></h2>
                    <p class="path">$BreadcrumbTitle.XML</p>
                    
                    <p>$Content.LimitCharacters(200).XML</p>
                </div>
            <% end_loop %>
            
            <% if $SearchPages %>
                <ul class="pagination">
                    <% if $PrevUrl %>
                        <li class="prev"><a href="$PrevUrl" class="cms-panel-link" data-pjax-target="CurrentForm"><%t WebbuildersGroup\\CMSUserDocs\\Control\\CMSDocumentationViewer.PREV "_Prev" %></a></li>
                    <% end_if %>
                    
                    <% loop $SearchPages %>
                        <% if $IsEllipsis %>
                            <li class="ellipsis">...</li>
                        <% else %>
                            <% if $Current %>
                                <li class="active"><strong>$PageNumber</strong></li>
                            <% else %>
                                <li><a href="$Link" class="cms-panel-link" data-pjax-target="CurrentForm">$PageNumber</a></li>
                            <% end_if %>
                        <% end_if %>
                    <% end_loop %>
                    
                    <% if $NextUrl %>
                        <li class="next"><a href="$NextUrl" class="cms-panel-link" data-pjax-target="CurrentForm"><%t WebbuildersGroup\\CMSUserDocs\\Control\\CMSDocumentationViewer.NEXT "_Next" %></a></li>
                    <% end_if %>
                </ul>
            <% end_if %>
        <% else %>
            <p>No Results</p>
        <% end_if %>
    </div>
</div>