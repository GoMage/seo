<?php

/**
 * GoMage SeoBooster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2014 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.0.0
 * @since        Available since Release 1.0.0
 */
class GoMage_SeoBooster_SitemapController extends Mage_Core_Controller_Front_Action
{
    /**
     * Dispatch action method
     *
     * @param $action
     */
    public function dispatch($action)
    {
        if (!Mage::helper('gomage_seobooster')->isEnabled()) {
            $action = 'noRoute';
        }
        parent::dispatch($action);
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
