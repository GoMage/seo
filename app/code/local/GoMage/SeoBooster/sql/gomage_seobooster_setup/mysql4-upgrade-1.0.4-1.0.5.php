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
    /* Add url rewrite id to tag */
    $installer->getConnection()->addColumn(
        $installer->getTable('cms/page'),
        'robots',
        "VARCHAR(255) NOT NULL"
    );
    $installer->getConnection()->addColumn(
        $installer->getTable('cms/page'),
        'exclude_from_sitemap',
        "SMALLINT(1) UNSIGNED NOT NULL DEFAULT 0"
    );
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
