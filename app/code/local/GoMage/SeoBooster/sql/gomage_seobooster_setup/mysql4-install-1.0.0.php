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
 * @var $this Mage_Core_Model_Resource_Setup
 */

$installer = $this;
$installer->startSetup();

try {
    /**
     * Add canonical product attributes
     */
    $attributesOptions = array(
        'canonical_url_type' => array(
            'label' => 'Canonical URL',
            'source' => 'gomage_seobooster/config_source_canonical_catalog_product_type',
        ),
        'canonical_url_store' => array(
            'label' => 'Cross Domain Canonical URL',
            'source' => 'gomage_seobooster/config_source_canonical_catalog_storeurl',
        )
    );

    foreach ($attributesOptions as $_code => $_options) {
        $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, $_code, array_merge($_options, array(
            'type'              => 'int',
            'input'             => 'select',
            'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            'visible'           => false,
            'required'          => false,
            'user_defined'      => true,
            'default'           => 0,
            'searchable'        => false,
            'filterable'        => false,
            'comparable'        => false,
            'visible_on_front'  => false,
            'unique'            => false,
            'group'             => 'Meta Information',
            'is_configurable'   => 0
        )));

        $this->updateAttribute(Mage_Catalog_Model_Product::ENTITY,
            $_code,
            'is_configurable',
            0
        );
    }
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
