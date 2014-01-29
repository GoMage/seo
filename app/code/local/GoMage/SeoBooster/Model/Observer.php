<?php
/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.0.0
 * @since        Available since Release 1.0.0
 */

/**
 * Observer model
 *
 * @category   GoMage
 * @package    GoMage_Seobooster
 * @subpackage Model
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Model_Observer
{
    /**
     * Refresh tag url rewrite if tag is new or name updated
     *
     * @param Varien_Event_Observer $observer Observer
     * @return $this
     */
    public function refreshTagUrlRewrite(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $tag = $event->getDataObject();

        if (!Mage::helper('gomage_seobooster')->canUseTagUrlRewrite()){
            return $this;
        }

        if (!$tag || !$tag->getId()) {
            return $this;
        }

        if ($tag->isObjectNew() || ($tag->getOrigData('name') != $tag->getName())) {
            Mage::getModel('gomage_seobooster/tag_url')->refreshTagRewrite($tag);
        }
    }

    /**
     * Remove tag url rewrite if tag deleted
     *
     * @param Varien_Event_Observer $observer Observer
     * @return $this
     */
    public function removeTagUrlRewrite(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $tag = $event->getDataObject();

        if (!Mage::helper('gomage_seobooster')->canUseTagUrlRewrite()){
            return $this;
        }

        $tag->isDeleted(true);
        Mage::getModel('gomage_seobooster/tag_url')->refreshTagRewrite($tag);
    }
}
