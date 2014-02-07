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
    $this->updateAttribute(Mage_Catalog_Model_Product::ENTITY,
        'exclude_from_sitemap',
        'label',
        'Exclude from XML Sitemap'
    );
    $this->updateAttribute(Mage_Catalog_Model_Category::ENTITY,
        'exclude_from_sitemap',
        'label',
        'Exclude from XML Sitemap'
    );
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
