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
