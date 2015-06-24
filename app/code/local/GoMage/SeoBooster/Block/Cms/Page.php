<?php
/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2015 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.2.0
 * @since        Available since Release 1.0.0
 */

class GoMage_SeoBooster_Block_Cms_Page extends Mage_Cms_Block_Page
{
    /**
     * Prepare global layout
     *
     * @return Mage_Cms_Block_Page
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $page = $this->getPage();
        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->setRobots(Mage::helper('gomage_seobooster')->getRobots($page));
            if (Mage::helper('gomage_seobooster/canonical_cms')->canUseCanonicalUrl()) {
                $head->addLinkRel('canonical', Mage::helper('gomage_seobooster/canonical_cms')->getCanonicalUrl($page));
            }
        }

        return $this;
    }
}
