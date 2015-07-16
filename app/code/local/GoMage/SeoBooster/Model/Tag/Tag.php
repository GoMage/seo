<?php
/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2015 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.3.0
 * @since        Available since Release 1.0.0
 */

class GoMage_SeoBooster_Model_Tag_Tag extends Mage_Tag_Model_Tag
{
    public function getTaggedProductsUrl()
    {
        if (Mage::helper('gomage_seobooster')->canUseTagUrlRewrite()) {
            return Mage::getModel('gomage_seobooster/tag_url')->getUrl($this);
        }

        return Mage::getUrl('tag/product/list', array('tagId' => $this->getTagId()));
    }
}
