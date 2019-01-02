<?php
namespace WebbuildersGroup\CMSUserDocs\Search;

use SilverStripe\Control\HTTPRequest;


interface ICMSUserDocsSearchEngine {
    /**
     * Performs the search against the documentation in the system
     * @param {string} $keywords Keywords to search for
     * @param {int} $startIndex Start index for pagination
     * @return {ArrayData} Array Data of information about the results this should contain the of the following:
     *      Results: The results from the search each item must be an instance of DocumentationPage
     *      Query: Term searched for
     *      TotalResults: Total Number of Results
     *      TotalPages: Total Number of Pages
     *      ThisPage: Current Page Number
     *      PrevUrl: URL for the previous page
     *      NextUrl: URL for the next page
     *      SearchPages: Array List of Array Data objects containing Link, Current, and PageNumber
     */
    public function getSearchResults($keywords, $startIndex=0, HTTPRequest $request=null);
}
?>