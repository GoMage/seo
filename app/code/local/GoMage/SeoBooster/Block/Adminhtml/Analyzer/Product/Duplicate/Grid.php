<?php 
/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2015 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.3.0
 * @since        Available since Release 1.0.0
 */

class GoMage_SeoBooster_Block_Adminhtml_Analyzer_Product_Duplicate_Grid
    extends GoMage_SeoBooster_Block_Adminhtml_Analyzer_Duplicate_Grid_Abstract
{
    /**
     * Init the grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('products_duplicate_grid');
        $this->setDefaultSort('entity_id');
    }

    protected function _prepareCollection()
    {
        $entityId = $this->getParam('duplicate_entity_id');
        $collection = Mage::getModel('gomage_seobooster/analyzer_product')
            ->getCollection()->getDuplicateCollection($entityId);
        $this->setCollection($collection);
        return parent::_prepareCollection();

    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => $this->helper('gomage_seobooster')->__('Product ID'),
            'index'  => 'entity_id',
            'type'   => 'number',
            'width'  => 20,
        ));
        $this->addColumnAfter('name', array(
            'header' => $this->helper('gomage_seobooster')->__('Product Name'),
            'index'  => 'name',
            'type'   => 'text',
        ), 'entity_id');

        $this->addColumnAfter('sku', array(
            'header' => $this->helper('gomage_seobooster')->__('SKU'),
            'index'  => 'sku',
            'type'   => 'text',
        ), 'name');

        $this->addColumnAfter('type_id', array(
            'header'=> Mage::helper('catalog')->__('Type'),
            'index' => 'type_id',
            'type'  => 'options',
            'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ), 'sku');

        $actionColumnParams = array(
            'header'  => $this->helper('gomage_seobooster')->__('Action'),
            'width'   => '100px',
            'type'    => 'action',
            'getter'  => 'getEntityId',
            'actions' => array(
                array(
                    'caption' => $this->helper('gomage_seobooster')->__('Edit'),
                    'url'     => array('base' => '*/catalog_product/edit'),
                    'field'   => 'id',
                ),
            ),
            'filter'    => false,
            'sortable'  => false,
            'is_system' => true,
        );

        if ($this->_getDuplicateField() != GoMage_SeoBooster_Helper_Analyzer::NAME_FIELD) {
            call_user_func(array($this, 'addColumnAfter'), 'action', $actionColumnParams, $this->_getDuplicateField());
        } else {
            call_user_func(array($this, 'addColumn'), 'action', $actionColumnParams);
        }

        return parent::_prepareColumns();
    }
}
