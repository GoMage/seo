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
class GoMage_SeoBooster_Block_Adminhtml_Analyzer_Product_Grid
    extends GoMage_SeoBooster_Block_Adminhtml_Analyzer_Grid_Abstract
{
    /**
     * Init the grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('products_grid');
        $this->setDefaultSort('product_id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('gomage_seobooster/analyzer_product')
            ->getCollection()
            ->addErrors(true)
            ->prepareCollectionForReport();
        $this->setCollection($collection);
        return parent::_prepareCollection();

    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
                'header' => $this->helper('gomage_seobooster')->__('Product ID'),
                'index'  => 'product_id',
                'type'   => 'number',
                'width'  => 20,
            )
        );
        $this->addColumnAfter('product_name', array(
                'header' => $this->helper('gomage_seobooster')->__('Product Name'),
                'index'  => 'product_name',
                'type'   => 'text',
            ), 'product_id'
        );

        $this->addColumnAfter('sku', array(
                'header' => $this->helper('gomage_seobooster')->__('SKU'),
                'index'  => 'sku',
                'type'   => 'text',
            ), 'product_name'
        );

        $this->addColumnAfter('product_type', array(
                'header'  => Mage::helper('catalog')->__('Type'),
                'index'   => 'product_type',
                'type'    => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ), 'sku'
        );
        $this->addColumnAfter('action', array(
                'header'    => $this->helper('gomage_seobooster')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getProductId',
                'actions'   => array(
                    array(
                        'caption' => $this->helper('gomage_seobooster')->__('Edit'),
                        'url'     => array('base' => '*/catalog_product/edit'),
                        'field'   => 'id',
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
            ), GoMage_SeoBooster_Helper_Analyzer::META_KEYWORD_FIELD
        );

        return parent::_prepareColumns();
    }

    protected function _getDuplicateAction()
    {
        return GoMage_SeoBooster_Helper_Analyzer::PRODUCT_DUPLICATE_ACTION;
    }
}
