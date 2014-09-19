<?php

/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2014 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.1.0
 * @since        Available since Release 1.0.0
 */
class GoMage_SeoBoosterBridge_Model_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     */
    public function prepareNavigationResult(Varien_Event_Observer $observer)
    {
        $event       = $observer->getEvent();
        $gomage_ajax = $event->getGomageAjax();
        $gomage_ajax->setData('current_url', Mage::helper('gomage_seoboosterbridge')->getCurrentUrl());
    }

}
