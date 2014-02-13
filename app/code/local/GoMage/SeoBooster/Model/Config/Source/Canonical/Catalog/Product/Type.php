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
 * Canonical product url type source model
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Model
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Model_Config_Source_Canonical_Catalog_Product_Type
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('gomage_seobooster')->__('Use Global'),
                    'value' => 0
                ),
                array(
                    'label' => Mage::helper('gomage_seobooster')->__('Use Longest'),
                    'value' => GoMage_SeoBooster_Helper_Product::CANONICAL_PRODUCT_URL_LONGEST
                ),
                array(
                    'label' => Mage::helper('gomage_seobooster')->__('Use Shortest'),
                    'value' => GoMage_SeoBooster_Helper_Product::CANONICAL_PRODUCT_URL_SHORTEST
                )
            );
        }

        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
}
