CMS User Docs Viewer
=================
A wrapper for silverstripe/docsviewer that pulls the documentation into the CMS adding a Site Help option to the help menu.

## Maintainer Contact
* Ed Chipman ([UndefinedOffset](https://github.com/UndefinedOffset))

## Requirements
* SilverStripe CMS 4.3+
* [SilverStripe Documentation Viewer Module](https://github.com/silverstripe/silverstripe-docsviewer) 2.0+


## Installation
```
composer require webbuilders-group/silverstripe-cmsuserdocs
```


## Usage
Out of the box the module will display the documentation files that have been bundled into any of your installed modules. To configure what is shown in the list of available documentation and other information on how to write documentation see the detailed documentation for the [SilverStripe Documentation Viewer Module](https://github.com/silverstripe/silverstripe-docsviewer/blob/master/docs/en/).


## Configuration
There are a number of configuration options available see below for a full list:
```yml
CMSDocumentationViewer:
    append_current_doc_title: true #Whether to append the current documentation path titles to the cms title or not
    search_engine: false #Search engine to use for searching documentation this must be an implementor of ICMSUserDocsSearchEngine, if left as false then the search form is not shown. See below for more information
    skip_default_entity: false #Skip the default entity in the page title and the breadcrumbs

LuceneCMSUserDocsSearch:
    index_location: null #Location for the search index, defaults to the TEMP_FOLDER/RebuildLuceneDocsIndex
```

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
