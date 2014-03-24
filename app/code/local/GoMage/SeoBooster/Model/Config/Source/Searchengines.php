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

class GoMage_SeoBooster_Model_Config_Source_Searchengines
{
    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array(
                'label' => Mage::helper('gomage_seobooster')->__("Google"),
                'value' => GoMage_SeoBooster_Helper_Sitemap::SEARCH_ENGINE_GOOGLE
            ),
            array(
                'label' => Mage::helper('gomage_seobooster')->__("Yahoo"),
                'value' => GoMage_SeoBooster_Helper_Sitemap::SEARCH_ENGINE_YAHOO
            ),
            array(
                'label' => Mage::helper('gomage_seobooster')->__("Bing"),
                'value' => GoMage_SeoBooster_Helper_Sitemap::SEARCH_ENGINE_BING
            ),
            array(
                'label' => Mage::helper('gomage_seobooster')->__("Ask"),
                'value' => GoMage_SeoBooster_Helper_Sitemap::SEARCH_ENGINE_ASK
            ),
        );

        return $options;
    }
}
