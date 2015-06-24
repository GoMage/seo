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

class GoMage_SeoBooster_Block_Adminhtml_Analyzer_Category_Grid
    extends GoMage_SeoBooster_Block_Adminhtml_Analyzer_Grid_Abstract
{
    /**
     * Init the grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('categories_grid');
        $this->setDefaultSort('category_id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('gomage_seobooster/analyzer_category')
            ->getCollection()
            ->addErrors(true)
            ->prepareCollectionForReport();
        $this->setCollection($collection);
        return parent::_prepareCollection();

    }

    protected function _prepareColumns()
    {
        $this->addColumn('category_id', array(
            'header' => $this->helper('gomage_seobooster')->__('Category ID'),
            'index'  => 'category_id',
            'type'   => 'number',
            'width'  => 20,
        ));
        $this->addColumnAfter('category_name', array(
            'header' => $this->helper('gomage_seobooster')->__('Category Name'),
            'index'  => 'category_name',
            'type'   => 'text',
        ), 'category_id');

        $this->addColumnAfter('action', array(
            'header'  => $this->helper('gomage_seobooster')->__('Action'),
            'width'   => '100px',
            'type'    => 'action',
            'getter'  => 'getCategoryId',
            'actions' => array(
                array(
                    'caption' => $this->helper('gomage_seobooster')->__('Edit'),
                    'url'     => array('base' => '*/catalog_category/edit'),
                    'field'   => 'id',
                ),
            ),
            'filter'    => false,
            'sortable'  => false,
            'is_system' => true,
        ), GoMage_SeoBooster_Helper_Analyzer::META_KEYWORD_FIELD);

        return parent::_prepareColumns();
    }

    protected function _getDuplicateAction()
    {
        return GoMage_SeoBooster_Helper_Analyzer::CATEGORY_DUPLICATE_ACTION;
    }
}
