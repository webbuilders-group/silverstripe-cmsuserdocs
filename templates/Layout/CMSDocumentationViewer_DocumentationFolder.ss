<div class="cms-content center cmsdocviewer-content cmsdocviewer-folder-content" data-layout-type="border" data-pjax-fragment="CurrentForm">
    <div class="cms-content-fields center cms-panel-padded ui-widget" data-layout-type="border">
        <% if $Introduction %>
            <div class="introduction">
                <p>$Introduction</p>
            </div>
        <% end_if %>
        
        <% if $Children %>
            <div class="documentation_children">
                <ul>
                    <% loop $Children %>
                        <li>
                            <h3><a href="$Link" class="cms-panel-link" data-pjax-target="Content">$Title.XML</a></h3>
                            <% if $Summary %><p>$Summary.XML</p><% end_if %>
                        </li>
                    <% end_loop %>
                </ul>
            </div>
        <% else %>
            <div class="documentation_children">
                <ul>
                    <% loop $DocsMenu %>
                        <li>
                            <h3><a href="$Link" class="cms-panel-link" data-pjax-target="Content">$Title.XML</a></h3>
                            <% if $Summary %><p>$Summary.XML</p><% end_if %>
                        </li>
                    <% end_loop %>
                </ul>
            </div>
        <% end_if %>
    </div>
    
    <% include CMSDocumentationViewer_NextPrevious %>
</div>