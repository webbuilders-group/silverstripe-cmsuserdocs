CMS User Docs Viewer
=================
A wrapper for silverstripe/docsviewer that pulls the documentation into the cms replacing the Help menu item.

## Maintainer Contact
* Ed Chipman ([UndefinedOffset](https://github.com/UndefinedOffset))

## Requirements
* SilverStripe CMS 3.1+
* [SilverStripe Documentation Viewer Module](https://github.com/silverstripe/silverstripe-docsviewer) 1.4+


## Installation
__Composer (recommended):__
```
composer require webbuilders-group/silverstripe-cmsuserdocs
```


## Usage
Documentation will now be visible in the CMS replacing the Help menu item that normally links over to the SilverStripe userdocs for your version. That link is still maintained at the bottom of the list of documentation sections in the cms.

Out of the box the module will display the documentation files that have been bundled into any of your installed modules.  To configure what is shown in the list of available documentation and other information on how to write doocumentation see the detailed documentation for the [SilverStripe Documentation Viewer Module](https://github.com/silverstripe/silverstripe-docsviewer/blob/master/docs/en/).


### Enabling search support
You can enable search support by setting the configuration option ``CMSDocumentationViewer.search_engine`` to the built in ``LuceneCMSUserDocsSearch`` engine. If you are using the default docs search engine each time you update your documentation you must rebuild the index using ``dev/tasks/RebuildLuceneCMSDocsIndex``. To change the location of the index you need to set the ``LuceneCMSUserDocsSearch.index_location`` configuration option to the full path to the index, by default will be in the temp folder for the site under the RebuildLuceneDocsIndex folder).

You can also build your own search engine must implement the ICMSUserDocsSearchEngine, your ``getSearchResults`` must return an array data containing the following:

* __Results:__ The results from the search each item must be an instance of DocumentationPage
* __Query:__ Term searched for
* __TotalResults:__ Total Number of Results
* __TotalPages:__ Total Number of Pages
* __ThisPage:__ Current Page Number
* __PrevUrl:__ URL for the previous page
* __NextUrl:__ URL for the next page
* __SearchPages:__ Array List of Array Data objects containing Link, Current, and PageNumber
