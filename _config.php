<?php
use SilverStripe\Admin\CMSMenu;
use WebbuildersGroup\CMSUserDocs\Control\CMSDocumentationViewer;


CMSMenu::remove_menu_item(str_replace('\\', '-', CMSDocumentationViewer::class));
