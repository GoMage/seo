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

class GoMage_SeoBooster_Model_Config_Source_Canonical_Type
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
                'label' => Mage::helper('gomage_seobooster')->__('Use Longest'),
                'value' => GoMage_SeoBooster_Helper_Product::CANONICAL_PRODUCT_URL_LONGEST
            ),
            array(
                'label' => Mage::helper('gomage_seobooster')->__('Use Shortest'),
                'value' => GoMage_SeoBooster_Helper_Product::CANONICAL_PRODUCT_URL_SHORTEST
            )
        );

        return $options;
    }
}
