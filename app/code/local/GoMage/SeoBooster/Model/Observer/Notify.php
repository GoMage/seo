<?php
/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2014 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.0.0
 * @since        Available since Release 1.0.0
 */

class GoMage_SeoBooster_Model_Observer_Notify
{
    public function notify($event)
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()
            && Mage::getStoreConfig('gomage_notification/notification/enable')
        ) {
            Mage::helper('gomage_seobooster')->notify();
        }
    }

}