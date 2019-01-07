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
```
