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

class GoMage_SeoBooster_Model_Config_Source_Canonical_Catalog_Storeurl
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
            $stores = Mage::getResourceModel('core/store_collection');
            $this->_options = array(array(
                'label' => Mage::helper('gomage_seobooster')->__("Use Global"),
                'value' => GoMage_SeoBooster_Helper_Data::CANONICAL_URL_DEFAULT_DOMAIN_VALUE
            ));

            foreach ($stores as $_store) {
                if (!$_store->getIsActive()) {
                    continue;
                }
                $storeUrl = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_SECURE_IN_FRONTEND, $_store->getStoreId())
                    ? Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL, $_store->getStoreId())
                    : Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL, $_store->getStoreId());

                $this->_options[] = array(
                    'label' => $_store->getName() . ' - ' . $storeUrl,
                    'value' => $_store->getStoreId()
                );
            }
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
