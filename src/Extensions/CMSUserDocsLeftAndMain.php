<?php
class CMSUserDocsLeftAndMain extends Extension {
    /**
     * Remove the help menu item, we're replacing this
     */
    public function onAfterInit() {
        CMSMenu::remove_menu_item('Help');
    }
}
?>