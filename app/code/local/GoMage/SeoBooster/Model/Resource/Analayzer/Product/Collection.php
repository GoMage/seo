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
 * Short description of the class
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Model
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Model_Resource_Analayzer_Product_Collection
    extends GoMage_SeoBooster_Model_Resource_Analayzer_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('gomage_seobooster/analayzer_product');
    }

    public function prepareCollectionForReport()
    {
        $nameAttribute = $attribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'name');

        $this->getSelect()->joinInner(
            array('catalog_produt' => $this->getTable('catalog/product')),
            'main_table.product_id = catalog_produt.entity_id',
            array('sku')
        )->joinInner(
            array('catalog_produt_name' => $attribute->getBackend()->getTable()),
            "main_table.product_id = catalog_produt_name.entity_id AND catalog_produt_name.attribute_id = {$nameAttribute->getId()}",
            array('name' => 'catalog_produt_name.value')
        )->joinLeft(
            array('duplicate_table' => $this->getResource()->getDuplicateTable()),
            'main_table.product_id = duplicate_table.product_id',
            array(
                'duplicate_entity_id' => 'duplicate_table.entity_id',
                'duplicate_name' => 'duplicate_table.name',
                'duplicate_description' => 'duplicate_table.description',
                'duplicate_meta_title' => 'duplicate_table.meta_title',
                'duplicate_meta_description' => 'duplicate_table.meta_description',
                'duplicate_meta_keyword' => 'duplicate_table.meta_keyword'
            )
        );

        return $this;
    }
}
