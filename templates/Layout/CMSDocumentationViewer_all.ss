<div class="cms-content center cmsdocviewer-content cmsdocviewer-all-content" data-layout-type="border" data-pjax-fragment="CurrentForm">
    <div class="cms-content-fields center cms-panel-padded ui-widget" data-layout-type="border">
        <div id="page-numbers">
            <span>
                <% loop $AllPages.GroupedBy('FirstLetter') %>
                    <a href="#$FirstLetter">$FirstLetter.XML</a>
                <% end_loop %>
            </span>
        </div>
        
        <% loop $AllPages.GroupedBy('FirstLetter') %>
            <h2 id="$FirstLetter">$FirstLetter.XML</h2>
        
            <ul class="third semantic">
                <% loop $Children %>
                    <li>
                        <a href="$Link" class="cms-panel-link" data-pjax-target="Content">$Title.XML</a>
                    </li>
                <% end_loop %>
            </ul>
        <% end_loop %>
    </div>
</div>