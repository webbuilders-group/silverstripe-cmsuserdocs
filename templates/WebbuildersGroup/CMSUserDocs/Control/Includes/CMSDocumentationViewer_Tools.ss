<div class="cms-content-tools fill-height cms-panel cms-panel-layout cmsdocviewer-menu" id="cmsdocviewer-menu" data-expandOnClick="true" data-layout-type="border">
    <div class="cms-content-header north vertical-align-items">
        <div class="cms-content-header-info vertical-align-items fill-width">
            <div class="section-heading flexbox-area-grow">
                <span class="section-label"><%t WebbuildersGroup\\CMSUserDocs\\Control\\CMSDocumentationViewer.MENU "_Menu" %></span>
            </div>
            
            <div class="view-controls">
                <button id="filters-button" class="btn btn-secondary icon-button font-icon-search btn--icon-large no-text" title="Filter"></button>               
            </div>
        </div>
    </div>
    
    <div class="flexbox-area-grow fill-height panel--scrollable cms-panel-content cms-helper-hide-actions">
        <div class="cms-content-filters cms-content-filters--hidden">
            <div class="search-holder">
                $SearchForm
            </div>
        </div>
        
        <div class="panel panel--padded panel--scrollable flexbox-area-grow fill-height flexbox-display cms-content-view">
            <% include WebbuildersGroup\CMSUserDocs\Control\CMSDocumentationViewer_DocsMenu %>
            
            <ul class="minor-nav">
                <li><a href="{$Link}$Language/all" class="cms-panel-link" data-pjax-target="Content"><%t WebbuildersGroup\\CMSUserDocs\\Control\\CMSDocumentationViewer.DOC_INDEX "Documentation Index" %></a></li>
            </ul>
        </div>
    </div>
    
    <div class="cms-panel-content-collapsed">
        <h3 class="cms-panel-header"><%t WebbuildersGroup\\CMSUserDocs\\Control\\CMSDocumentationViewer.MENU "_Menu" %></h3>
    </div>
</div>