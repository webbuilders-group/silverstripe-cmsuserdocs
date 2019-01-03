<?php
namespace WebbuildersGroup\CMSUserDocs\Search;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\DocsViewer\DocumentationSearch;


class LuceneCMSUserDocsSearch implements ICMSUserDocsSearchEngine {
    use Injectable;
    use Configurable;
    
    /**
     * Location for the search index, defaults to the TEMP_FOLDER/RebuildLuceneDocsIndex
     * @var {string}
     * @config LuceneCMSUserDocsSearch.index_location
     */
    private static $index_location;
    
    /**
     * Performs the search against the documentation in the system
     * @param {string} $keywords Keywords to search for
     * @param {int} $startIndex Start index for pagination
     * @return {ArrayData} Array Data of information about the results
     * 
     * @see ICMSUserDocsSearchEngine::getSearchResults()
     */
    public function getSearchResults($keywords, $startIndex=0, HTTPRequest $request=null) {
        //Workaround to set the search index location
        Config::inst()->update(DocumentationSearch::class, 'index_location', $this->config()->index_location);
        
        
        //Search the index
        $search=new DocumentationSearch();
        $search->setQuery($keywords);
        $search->setVersions(array());
        $search->setModules(array());
        $search->performSearch();
        
        return $search->getSearchResults($request);
    }
}
?>