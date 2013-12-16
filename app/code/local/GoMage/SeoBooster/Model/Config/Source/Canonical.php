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
 * Canonical url source model
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Model
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Model_Config_Source_Canonical
{
    public function toOptionArray()
    {
        $options = array(
            array(
                'label' => Mage::helper('gomage_seobooster')->__('No'),
                'value' => GoMage_SeoBooster_Helper_Data::CANONICAL_URL_DISABLED
            ),
            array(
                'label' => Mage::helper('gomage_seobooster')->__('Products'),
                'value' => GoMage_SeoBooster_Helper_Data::CANONICAL_URL_PRODUCTS
            ),
            array(
                'label' => Mage::helper('gomage_seobooster')->__('Categories'),
                'value' => GoMage_SeoBooster_Helper_Data::CANONICAL_URL_CATEGORIES
            ),
            array(
                'label' => Mage::helper('gomage_seobooster')->__('Products and Categories'),
                'value' => GoMage_SeoBooster_Helper_Data::CANONICAL_URL_PRODUCTS_CATEGORIES
            )
        );

        return $options;
    }
}
