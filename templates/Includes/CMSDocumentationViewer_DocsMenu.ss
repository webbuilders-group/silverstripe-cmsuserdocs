<ul class="menuList" data-pjax-fragment="DocsMenu">
    <% if not $HasDefaultEntity %>
        <li><a href="$Link" class="cms-panel-link" data-pjax-target="Content"><%t CMSDocumentationViewer.HOME "_Home" %></a></li>
    <% end_if %>
    
    <% loop $DocsMenu %>
        <% if $DefaultEntity %>
            <li><a href="$Link" class="cms-panel-link top" data-pjax-target="Content"><%t CMSDocumentationViewer.HOME "_Home" %></a></li>
            
            <% loop $Children %>
                <li class="$LinkingMode">
                    <a href="$Link" class="cms-panel-link top" data-pjax-target="Content">$Title.XML</a>

                    <% if $LinkingMode=="section" || $LinkingMode=="current" %>
                        <% if $Children %>
                            <ul class="$FirstLast">
                                <% loop $Children %>
                                    <li><a href="$Link" class="cms-panel-link $LinkingMode" data-pjax-target="Content">$Title.XML</a>
                                        <% if $LinkingMode=="section" || $LinkingMode=="current" %>
                                            <% if $Children %>
                                                <ul class="$FirstLast">
                                                    <% loop $Children %>
                                                        <li><a href="$Link" class="cms-panel-link $LinkingMode" data-pjax-target="Content">$Title.XML</a></li>
                                                    <% end_loop %>
                                                </ul>
                                            <% end_if %>
                                        <% end_if %>
                                    </li>
                                <% end_loop %>
                            </ul>
                        <% end_if %>
                    <% end_if %>
                </li>
            <% end_loop %>
        <% else %>
            <li class="$LinkingMode"><a href="$Link" class="cms-panel-link top" data-pjax-target="Content">$Title.XML</a>
                <% if $LinkingMode=="section" || $LinkingMode=="current" %>
                    <% if $Children %>
                        <ul class="$FirstLast">
                            <% loop $Children %>
                                <li><a href="$Link" class="cms-panel-link $LinkingMode" data-pjax-target="Content">$Title.XML</a>
                                    <% if $LinkingMode=="section" || $LinkingMode=="current" %>
                                        <% if $Children %>
                                            <ul class="$FirstLast">
                                                <% loop $Children %>
                                                    <li><a href="$Link" class="cms-panel-link $LinkingMode" data-pjax-target="Content">$Title.XML</a></li>
                                                <% end_loop %>
                                            </ul>
                                        <% end_if %>
                                    <% end_if %>
                                </li>
                            <% end_loop %>
                        </ul>
                    <% end_if %>
                <% end_if %>
            </li>
        <% end_if %>
    <% end_loop %>
    
    <% if $SilverStripeDocLink %>
        <li class="last"><a href="$SilverStripeDocLink.ATT" target="_blank"><%t CMSDocumentationViewer.GENERAL_SILVERSTRIPE_HELP "_SilverStripe CMS user help guide" %></a></li>
    <% end_if %>
</ul>