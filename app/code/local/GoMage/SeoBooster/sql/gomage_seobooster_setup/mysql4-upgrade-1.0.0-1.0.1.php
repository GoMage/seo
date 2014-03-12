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
 * @var $this Mage_Eav_Model_Entity_Setup
 */

$installer = $this;
$installer->startSetup();

try {
    /**
     * Add canonical store attribute for category
     */
    $installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'canonical_url_store', array(
        'type' => 'int',
        'label' => "Cross-Domain Canonical URL",
        'input' => 'select',
        'source' => 'gomage_seobooster/config_source_canonical_catalog_storeurl',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible' => true,
        'required' => false,
        'default' => 0,
        'group' => 'Meta Information',
    ));
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
