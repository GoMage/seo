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
class GoMage_SeoBooster_Model_Resource_Analayzer_Product extends GoMage_SeoBooster_Model_Resource_Analayzer_Abstract
{
    protected $_requiredAttributes = array('name', 'meta_title', 'meta_description', 'meta_keywords');

    protected function _construct()
    {
        $this->_init('gomage_seobooster/analyzer_product', 'entity_id');
    }

    public function getEntities()
    {
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getTable('catalog/product'), array());

        foreach ($this->_requiredAttributes as $attributeCode) {
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
            $tableAlias = $attributeCode . '_table';
            $select->joinInner(
                array($tableAlias => $attribute->getBackend()->getTable()),
                "main_table.entity_id = {$tableAlias}.entity_id AND {$tableAlias}.attribute_id = {$attribute->getId()}",
                array($attributeCode => $tableAlias.'.value')
            );
        }
    }

}
