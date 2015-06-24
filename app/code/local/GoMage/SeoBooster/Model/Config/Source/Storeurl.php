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

class GoMage_SeoBooster_Model_Config_Source_Storeurl
{
    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $stores = Mage::getResourceModel('core/store_collection');
        $options = array(array(
            'label' => Mage::helper('gomage_seobooster')->__("Default"),
            'value' => GoMage_SeoBooster_Helper_Data::CANONICAL_URL_DEFAULT_DOMAIN_VALUE
        ));


        foreach ($stores as $_store) {
            if (!$_store->getIsActive()) {
                continue;
            }
            $storeUrl = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_SECURE_IN_FRONTEND, $_store->getStoreId())
                ? Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL, $_store->getStoreId())
                : Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL, $_store->getStoreId());

            $options[] = array(
                'label' => $_store->getName() . ' - ' . $storeUrl,
                'value' => $_store->getStoreId()
            );
        }

        return $options;
    }
}
