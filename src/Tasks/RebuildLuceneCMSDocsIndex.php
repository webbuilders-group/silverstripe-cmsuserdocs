<?php
class RebuildLuceneCMSDocsIndex extends RebuildLuceneDocsIndex {
    protected $title='Rebuild CMS Documentation Search Indexes';
    protected $description='Rebuilds the indexes used for the search engine in the CMS Documentation.';
    
    public function run($request) {
        //Workaround to point the documentation entities to the cms instead of to the DocumentationViewer path
        $baseLink=Controller::join_links(Config::inst()->get('LeftAndMain', 'url_base', Config::FIRST_SET), CMSDocumentationViewer::config()->get('url_segment', Config::FIRST_SET), '/');
        Config::inst()->update('DocumentationViewer', 'link_base', $baseLink);
        
        //Workaround to change the index location before rebuilding the index
        Config::inst()->update('DocumentationSearch', 'index_location', LuceneCMSUserDocsSearch::config()->index_location);
        
        $this->rebuildIndexes();
    }
    
    /**
     * @return string
     */
    public function getTitle() {
        return _t('RebuildLuceneCMSDocsIndex.TITLE', parent::getTitle());
    }
    
    /**
     * @return string HTML formatted description
     */
    public function getDescription() {
        return _t('RebuildLuceneCMSDocsIndex.DESCRIPTION', parent::getDescription());
    }
}
?>