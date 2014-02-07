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
    $attributesOptions = array(
        'robots' => array(
            'label'  => 'Robots',
            'source' => 'gomage_seobooster/config_source_robots',
            'type'   => 'varchar'
        ),
        'exclude_from_sitemap' => array(
            'label'  => 'Exclude from XML Sitemap',
            'source' => 'eav/entity_attribute_source_boolean',
            'type'   => 'int'
        )
    );

    foreach ($attributesOptions as $_code => $_options) {
        $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, $_code, array_merge($_options, array(
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

        $installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, $_code, array_merge($_options, array(
            'input' => 'select',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'default' => 0,
            'group' => 'Meta Information',
        )));
    }
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
