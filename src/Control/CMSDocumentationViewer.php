<?php
namespace WebbuildersGroup\CMSUserDocs\Control;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Assets\Folder;
use SilverStripe\CMS\Search\SearchForm;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\PjaxResponseNegotiator;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\DocsViewer\DocumentationHelper;
use SilverStripe\DocsViewer\DocumentationManifest;
use SilverStripe\DocsViewer\DocumentationPermalinks;
use SilverStripe\DocsViewer\Controllers\DocumentationViewer;
use SilverStripe\DocsViewer\Models\DocumentationFolder;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\GroupedList;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use SilverStripe\View\Parsers\ShortcodeParser;
use SilverStripe\i18n\i18n;
use WebbuildersGroup\CMSUserDocs\Search\ICMSUserDocsSearchEngine;


class CMSDocumentationViewer extends LeftAndMain {
    private static $menu_priority=-2;
    private static $url_segment='help';
    
    private static $allowed_actions=array(
                                        'all',
                                        'handleAction',
                                        'results'
                                    );
    
    
    /**
     * Whether to append the current documentation path titles to the cms title or not
     * @var {bool}
     * @default true
     * @config CMSDocumentationViewer.append_current_doc_title
     */
    private static $append_current_doc_title=true;
    
    
    /**
     * Search engine to use for searching documentation this must be an implementor of ICMSUserDocsSearchEngine, if left as false then the search form is not shown
     * @var {string}
     * @config CMSDocumentationViewer.search_engine
     */
    private static $search_engine=false;
    
    /**
     * Skip the default entity in the title and breadcrumbs
     * @var {bool}
     * @default true
     * @config CMSDocumentationViewer.skip_default_entity
     */
    private static $skip_default_entity=false;
    
    
    /**
     * The string name of the currently accessed {@link DocumentationEntity}
     * object. To access the entire object use {@link getEntity()}
     *
     * @var string
     */
    protected $entity='';
    
    /**
     * @var DocumentationPage
     */
    protected $record;
    
    /**
     * @var DocumentationManifest
     */
    protected $manifest;
    
    
    public function init() {
        parent::init();
        
        //Workaround to point the documentation entities here instead of to the DocumentationViewer path
        $baseLink=Controller::join_links($this->stat('url_base', true), $this->config()->get('url_segment'), '/');
        Config::inst()->update(DocumentationViewer::class, 'link_base', $baseLink);
        
        
        //Requirements
        Requirements::css('webbuilders-group/silverstripe-cmsuserdocs: thirdparty/google/code-prettify/prettify.css');
        Requirements::css('webbuilders-group/silverstripe-cmsuserdocs: css/CMSDocumentationViewer.css');
        
        Requirements::add_i18n_javascript('webbuilders-group/silverstripe-cmsuserdocs: javascript/lang/');
        Requirements::javascript('webbuilders-group/silverstripe-cmsuserdocs: thirdparty/google/code-prettify/run_prettify.js?autorun=false');
        Requirements::javascript('webbuilders-group/silverstripe-cmsuserdocs: javascript/CMSDocumentationViewer.js');
    }
    
    /**
     * Gets the title for this section of the cms
	 * @return {string}
	 */
	public function Title() {
	    $title=parent::Title();
	    
	    //If we have a record and we're not on the base url add the current page's title
	    if($this->config()->append_current_doc_title==true && $this->getPage()) {
	        $baseLink=Controller::join_links($this->stat('url_base', true), $this->config()->url_segment, $this->getLanguage());
	        
	        if(rtrim($this->request->getURL(), '/')!=$baseLink) {
	            $pageTitle=$this->getPagePathTitle();
	            if(!empty($pageTitle)) {
    	           $title.=' - '.$this->getPagePathTitle();
	            }
	        }
	    }else if($this->action=='all') {
	        $title.=' - '._t('WebbuildersGroup\\CMSUserDocs\\Control\\CMSDocumentationViewer.DOC_INDEX', '_Documentation Index');
	    }
	    
	    return $title;
	}
    
    /**
     * Handles requests for the documentation index
     * @param {SS_HTTPRequest}
     * @return {SS_HTTPResponse}
     */
    public function all($request) {
        return $this->getResponseNegotiator()->respond($request);
    }
    
    /**
     * Handles requests for the documentation search results
     * @param {SS_HTTPRequest}
     * @return {SS_HTTPResponse}
     */
    public function results($request) {
        return $this->getResponseNegotiator()->respond($request);
    }
    
    /**
     * Overloaded, it's handled differently
     * @param {string} $action
     * @return {bool}
     */
    public function hasAction($action) {
        return true;
    }
    
    /**
     * Overloaded, it's handled differently
     * @param {string} $action
     * @return {bool}
     */
    public function checkAccessAction($action) {
        return true;
    }
    
    /**
     * Overloaded to avoid "action doesn't exist" errors - all URL parts in this controller are virtual and handled through handleRequest(), not controller methods.
     * @param {SS_HTTPRequest} $request
     * @param {string} $action
     * @return {SS_HTTPResponse}
     */
    public function handleAction($request, $action) {
        // if we submitted a form, let that pass
        if(!$request->isGET()) {
            return parent::handleAction($request, $action);
        }
        
        $url=$request->getURL();
        
        //If the current request has an extension attached to it, strip that off and redirect the user to the page without an extension.
        if(DocumentationHelper::get_extension($url)) {
            $request->shift();
            $request->shift();
            
            return $this->redirect(Director::absoluteURL(DocumentationHelper::trim_extension_off($url)).'/', 301);
        }
        
        //Strip off the base url
        $base=ltrim(Config::inst()->get(DocumentationViewer::class, 'link_base'), '/');
        
        if($base && strpos($url, $base)!==false) {
            $url=substr(ltrim($url, '/'), strlen($base));
        }
        
        
        //Handle any permanent redirections that the developer has defined.
        if($link=DocumentationPermalinks::map($url)) {
            $request->shift();
            $request->shift();
            
            //the first param is a shortcode for a page so redirect the user to the short code.
            return $this->redirect($link, 301);
        }
        
        
        //Validate the language provided. Language is a required URL parameter. as we use it for generic interfaces and language selection.
        //If language is not set, redirects to 'en'
        $languages=i18n::getData()->getLanguages();
        if(!$lang=$request->param('Lang')) {
            $lang=$request->param('Action');
            $action=$request->param('ID');
        }else {
            $action=$request->param('Action');
        }
        
        if(!$lang) {
            return $this->redirect($this->Link('en'));
        }else if(!isset($languages[$lang])) {
            return $this->httpError(404);
        }
        
        
        $request->shift(10);
        $allowed=$this->config()->allowed_actions;
        
        if(in_array($action, $allowed)) {
            //if it's one of the allowed actions such as search or all then the URL must be prefixed with one of the allowed languages.
            return parent::handleAction($request, $action);
        }else {
            //look up the manifest to see find the nearest match against the list of the URL. If the URL exists then set that as the current page to match against. strip off any extensions.
            if ($record=$this->getManifest()->getPage($url)) {
                $this->record=$record;
                
                return $this->getResponseNegotiator()->respond($request);
            }else if($redirect=$this->getManifest()->getRedirect($url)) {
                $to=Controller::join_links(Director::baseURL(), $base, $redirect);
                
                return $this->redirect($to, 301);
            }else if(!$url || $url==$lang) {
                return $this->getResponseNegotiator()->respond($request);
            }else {
                $url=explode('/', $url);
                $url=implode('/', array_map(function ($a) {
                                                return DocumentationHelper::clean_page_url($a);
                                            }, $url));
                
                if($record=$this->getManifest()->getPage($url)) {
                    return $this->redirect($record->Link(), 301);
                }
            }
        }
        
        return $this->httpError(404);
    }
    
    /**
	 * Caution: Volatile API.
	 * @return PjaxResponseNegotiator
	 */
	public function getResponseNegotiator() {
		if(!$this->responseNegotiator) {
			$controller=$this;
			$this->responseNegotiator=new PjaxResponseNegotiator(
				array(
					'CurrentForm'=>function() use(&$controller) {
						return $controller->DocContent();
					},
					'Content'=>function() use(&$controller) {
						return $controller->renderWith($controller->getTemplatesWithSuffix('_Content'));
					},
					'Breadcrumbs'=>function() use (&$controller) {
						return $controller->renderWith('CMSBreadcrumbs');
					},
					'DocsMenu'=>function() use (&$controller) {
						return $controller->renderWith($controller->getTemplatesWithSuffix('_DocsMenu'));
					},
					'default'=>function() use(&$controller) {
						return $controller->renderWith($controller->getViewer('show'));
					}
				),
				$this->response
			);
		}
		
		return $this->responseNegotiator;
	}
	
	/**
	 * Handles rendering the content panel
	 * @return {HTMLText}
	 */
	public function DocContent() {
	    if($this->record) {
	        return $this->renderWith($this->getTemplatesWithSuffix('_'.get_class($this->record)));
	    }
	    
	    if($this->action=='all') {
	        return $this->renderWith($this->getTemplatesWithSuffix('_all'));
	    }else if($this->action=='results') {
	        return $this->getSearchResults();
	    }
	    
	    return $this->renderWith($this->getTemplatesWithSuffix('_DocumentationFolder'));
	}
    
    /**
     * @return {DocumentationManifest}
     */
    public function getManifest() {
        if(!$this->manifest) {
            $flush=Director::isTest() || (isset($_GET['flush']));
            
            $this->manifest=new DocumentationManifest($flush);
        }
        
        return $this->manifest;
    }
    
    /**
     * @return {string}
     */
    public function getLanguage() {
        if (!$lang=$this->request->param('Lang')) {
            $lang=$this->request->param('Action');
        }
        
        return $lang;
    }
    
    /**
     * Generate a list of {@link Documentation } which have been registered and which can be documented.
     * @return {DataObject}
     */
    public function getDocsMenu() {
        $entities=$this->getManifest()->getEntities();
        $output=new ArrayList();
        $record=$this->getPage();
        $current=$this->getEntity();
        
        foreach($entities as $entity) {
            $checkLang=$entity->getLanguage();
            $checkVers=$entity->getVersion();
            
            // only show entities with the same language or any entity that
            // isn't registered under any particular language (auto detected)
            if($checkLang && $checkLang!==$this->getLanguage()) {
                continue;
            }
            
            if($current && $checkVers) {
                if ($entity->getVersion()!==$current->getVersion()) {
                    continue;
                }
            }
            
            $mode='link';
            $children=new ArrayList();
            
            if($entity->hasRecord($record) || $entity->getIsDefaultEntity()) {
                $mode='current';
                
                // add children
                $children = $this->getManifest()->getChildrenFor(
                                                                $entity->getPath(), ($record) ? $record->getPath() : $entity->getPath()
                                                            );
            }else {
                if ($current && $current->getKey()==$entity->getKey()) {
                    continue;
                }
            }
            
            $link=$entity->Link();
            
            $output->push(new ArrayData(array(
                                            'Title'=>$entity->getTitle(),
                                            'Link'=>$link,
                                            'LinkingMode'=>$mode,
                                            'DefaultEntity'=>$entity->getIsDefaultEntity(),
                                            'Children'=>$children
                                        )));
        }
        
        return $output;
    }

    /**
     * Return the content for the page. If its an actual documentation page then display the content from the page, otherwise display the contents from the index.md file if its a folder
     * @return {HTMLText}
     */
    public function getPageContent() {
        $page=$this->getPage();
        $html=$page->getHTML();
        $html=$this->replaceChildrenCalls($html);
        $html=$this->parseLinksForCMS($html);
        
        return $html;
    }
    
    /**
     * Parses the html to replace the children shortcode with the documentation index
     * @param {string} $html
     * @return {string}
     */
    public function replaceChildrenCalls($html) {
        $codes=new ShortcodeParser();
        $codes->register('CHILDREN',  array($this, 'includeChildren'));
        
        return $codes->parse($html);
    }
    
    /**
     * Parses the links for use in the cms, routes all absolute links to _blank and uses cms-panel-link for the relative links
     * @param {string} $html HTML to be processed
     * @return {string} Processed html
     */
    public function parseLinksForCMS($html) {
        //Make absolute uri links open in a new window
        $html=preg_replace('/<a href="([A-Za-z][A-Za-z0-9+\-.]*:(?:\/\/(?:(?:[A-Za-z0-9\-._~!$&\'()*+,;=:]|%[0-9A-Fa-f]{2})*@)?(?:\[(?:(?:(?:(?:[0-9A-Fa-f]{1,4}:){6}|::(?:[0-9A-Fa-f]{1,4}:){5}|(?:[0-9A-Fa-f]{1,4})?::(?:[0-9A-Fa-f]{1,4}:){4}|(?:(?:[0-9A-Fa-f]{1,4}:){0,1}[0-9A-Fa-f]{1,4})?::(?:[0-9A-Fa-f]{1,4}:){3}|(?:(?:[0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})?::(?:[0-9A-Fa-f]{1,4}:){2}|(?:(?:[0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})?::[0-9A-Fa-f]{1,4}:|(?:(?:[0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})?::)(?:[0-9A-Fa-f]{1,4}:[0-9A-Fa-f]{1,4}|(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))|(?:(?:[0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})?::[0-9A-Fa-f]{1,4}|(?:(?:[0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})?::)|[Vv][0-9A-Fa-f]+\.[A-Za-z0-9\-._~!$&\'()*+,;=:]+)\]|(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)|(?:[A-Za-z0-9\-._~!$&\'()*+,;=]|%[0-9A-Fa-f]{2})*)(?::[0-9]*)?(?:\/(?:[A-Za-z0-9\-._~!$&\'()*+,;=:@]|%[0-9A-Fa-f]{2})*)*|\/(?:(?:[A-Za-z0-9\-._~!$&\'()*+,;=:@]|%[0-9A-Fa-f]{2})+(?:\/(?:[A-Za-z0-9\-._~!$&\'()*+,;=:@]|%[0-9A-Fa-f]{2})*)*)?|(?:[A-Za-z0-9\-._~!$&\'()*+,;=:@]|%[0-9A-Fa-f]{2})+(?:\/(?:[A-Za-z0-9\-._~!$&\'()*+,;=:@]|%[0-9A-Fa-f]{2})*)*|)(?:\?(?:[A-Za-z0-9\-._~!$&\'()*+,;=:@\/?]|%[0-9A-Fa-f]{2})*)?)">/', '<a href="$1" target="_blank">', $html);
        
        //Make absolute path links open in a new window
        $html=preg_replace('/<a href="(\/(?:(?:[A-Za-z0-9\-._~!$&\'()*+,;=:@]|%[0-9A-Fa-f]{2})+(?:\/(?:[A-Za-z0-9\-._~!$&\'()*+,;=:@]|%[0-9A-Fa-f]{2})*)*)?)">/', '<a href="'.Director::baseURL().'$1" target="_blank">', $html);
        
        //Make relative links load the cms panel
        $html=preg_replace('/<a href="((?:[A-Za-z0-9\-._~!$&\'()*+,;=:@]|%[0-9A-Fa-f]{2})+(?:\/(?:[A-Za-z0-9\-._~!$&\'()*+,;=:@]|%[0-9A-Fa-f]{2})*)*)">/', '<a href="$1" class="cms-panel-link" data-pjax-target="Content">', $html);
        
        return $html;
    }

    /**
     * Short code parser
     */
    public function includeChildren($args) {
        if(isset($args[Folder::class])) {
            $children=$this->getManifest()->getChildrenFor(
                                                        Controller::join_links(dirname($this->record->getPath()), $args[Folder::class])
                                                    );
        }else {
            $children=$this->getManifest()->getChildrenFor(
                                                        dirname($this->record->getPath())
                                                    );
        }
        
        if(isset($args['Exclude'])) {
            $exclude=explode(',', $args['Exclude']);
            
            foreach($children as $k=>$child) {
                foreach($exclude as $e) {
                    if($child->Link==Controller::join_links($this->record->Link(), strtolower($e), '/')) {
                        unset($children[$k]);
                    }
                }
            }
        }
        
        return $this->customise(new ArrayData(array(
                                                    'Children'=>$children
                                                )))->renderWith('Includes/DocumentationPages');
    }

    /**
     * @return {ArrayList}
     */
    public function getChildren() {
        if ($this->record instanceof DocumentationFolder) {
            return $this->getManifest()->getChildrenFor(
                $this->record->getPath()
            );
        } elseif ($this->record) {
            return $this->getManifest()->getChildrenFor(
                dirname($this->record->getPath())
            );
        }

        return new ArrayList();
    }

    /**
     * Generate a list of breadcrumbs for the user.
     * @return {ArrayList}
     */
    public function Breadcrumbs($unlinked = false) {
        $breadcrumbs=new ArrayList(array(
                                        new ArrayData(array(
                                                        'Link'=>$this->Link(),
                                                        'Title'=>_t('WebbuildersGroup\\CMSUserDocs\\Control\\CMSDocumentationViewer.MENUTITLE', $this->config()->menu_title)
                                                    ))
                                    ));
        
        if($this->record) {
            $docBreadcrumbs=$this->getManifest()->generateBreadcrumbs($this->record, $this->record->getEntity());
            if($this->config()->skip_default_entity && $this->record->getEntity()->getIsDefaultEntity()) {
                $docBreadcrumbs->shift();
            }
            
            $breadcrumbs->merge($docBreadcrumbs);
        }else if($this->action=='all') {
            $breadcrumbs->push(new ArrayData(array(
                                                'Link'=>$this->Link('all'),
                                                'Title'=>_t('WebbuildersGroup\\CMSUserDocs\\Control\\CMSDocumentationViewer.DOC_INDEX', '_Documentation Index')
                                            )));
        }
        
        return $breadcrumbs;
    }

    /**
     * @return {DocumentationPage}
     */
    public function getPage() {
        return $this->record;
    }

    /**
     * @return {DocumentationEntity}
     */
    public function getEntity() {
        return ($this->record) ? $this->record->getEntity() : null;
    }

    /**
     * @return {ArrayList}
     */
    public function getVersions() {
        return $this->getManifest()->getVersions($this->getEntity());
    }
    
    /**
     * Detection if we're in the viewer or not
     */
    public function getIsDocViewer() {
        return true;
    }

    /**
     * Generate a list of all the pages in the documentation grouped by the first letter of the page.
     * @return {GroupedList}
     */
    public function AllPages() {
        $pages=$this->getManifest()->getPages();
        $output=new ArrayList();
        $baseLink=Config::inst()->get(DocumentationViewer::class, 'link_base');
        
        foreach($pages as $url => $page) {
            $first=strtoupper(trim(substr($page['title'], 0, 1)));
            
            if ($first) {
                $output->push(new ArrayData(array(
                    'Link' => Controller::join_links($baseLink, $url),
                    'Title' => $page['title'],
                    'FirstLetter' => $first
                )));
            }
        }
        
        return GroupedList::create($output->sort('Title', 'ASC'));
    }
    
    /**
     * Returns the next page. Either retrieves the sibling of the current page or return the next sibling of the parent page.
     * @return {DocumentationPage}
     */
    public function getNextPage() {
        return ($this->record ? $this->getManifest()->getNextPage($this->record->getPath(), $this->getEntity()->getPath()):null);
    }

    /**
     * Returns the previous page. Either returns the previous sibling or the parent of this page
     * @return {DocumentationPage}
     */
    public function getPreviousPage() {
        return ($this->record ? $this->getManifest()->getPreviousPage($this->record->getPath(), $this->getEntity()->getPath()):null);
    }
    
    /**
     * Gets the SilverStripe Help Link from the LeftAndMain config
     * @return {string}
     */
    public function getSilverStripeHelpLink() {
        return LeftAndMain::config()->help_link;
    }
    
    /**
     * Gets whether there is a default entity or not
     * @return {bool}
     * @see DocumentationManifest::getHasDefaultEntity()
     */
    public function getHasDefaultEntity() {
        return $this->getManifest()->getHasDefaultEntity();
    }
    
    /**
     * Gets the full path title for the current page
     * @return {string}
     */
    public function getPagePathTitle($divider=' - ') {
        if($page=$this->getPage()) {
            $pathParts=explode('/', trim($page->getRelativePath(), '/'));
            
            // from the page from this
            array_pop($pathParts);
            
            // add the module to the breadcrumb trail.
            if($this->config()->skip_default_entity==false || $page->getEntity()->getIsDefaultEntity()==false) {
                $pathParts[]=$page->getEntity()->getTitle();
            }
            
            $titleParts=array_map(array(DocumentationHelper::class, 'clean_page_name'), $pathParts);
            
            $titleParts=array_filter($titleParts, function($val) {
                if($val) {
                    return $val;
                }
            });
            
            if($page->getTitle()) {
                array_unshift($titleParts, $page->getTitle());
            }
            
            return implode($divider, array_reverse($titleParts));
        }
    }
    
    /**
     * Gets the form used for searching
     * @return {Form} This can return nothing if there is no search engine defined
     */
    public function SearchForm() {
        if($this->config()->search_engine===false || !class_exists($this->config()->search_engine) || !ClassInfo::classImplements($this->config()->search_engine, ICMSUserDocsSearchEngine::class)) {
            return;
        }
        
        
        $fields=new FieldList(
                            TextField::create('q', '', $this->request->getVar('q'))
                        );
        
        $actions=new FieldList(
                                FormAction::create('results', _t('DocumentationViewer.SEARCH', 'Search'))->addExtraClass('ss-ui-action-constructive')
                            );
        
        
        $form=Form::create($this, SearchForm::class, $fields, $actions)
                        ->setHTMLID('Form_SearchForm')
                        ->addExtraClass('search-form clearfix fill-height')
                        ->setFormAction($this->Link($this->getLanguage().'/results'))
                        ->setFormMethod('get')
                        ->disableSecurityToken()
                        ->setTemplate($this->getTemplatesWithSuffix('_SearchForm'));
        
        
        //Allow extensions
        $this->extend('updateSearchForm', $form);
        
        
        return $form;
    }
    
    /**
     * Gets the rendered results from searching
     * @return {HTMLText}
     */
    public function getSearchResults() {
        if($this->config()->search_engine===false || !class_exists($this->config()->search_engine) || !ClassInfo::classImplements($this->config()->search_engine, ICMSUserDocsSearchEngine::class)) {
            return $this->httpError(404);
        }
        
        
        $results=Injector::inst()->get($this->config()->search_engine)->getSearchResults($this->request->getVar('q'), $this->request->getVar('start'), $this->request);
        return $results->renderWith('CMSDocumentationViewer_results');
    }
}
?>