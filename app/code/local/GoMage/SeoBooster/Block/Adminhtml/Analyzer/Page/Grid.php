<?php 
/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2014 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.0.0
 * @since        Available since Release 1.0.0
 */

class GoMage_SeoBooster_Block_Adminhtml_Analyzer_Page_Grid
    extends GoMage_SeoBooster_Block_Adminhtml_Analyzer_Grid_Abstract
{
    /**
     * Init the grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('pages_grid');
        $this->setDefaultSort('page_id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('gomage_seobooster/analyzer_page')
            ->getCollection()
            ->addErrors(true)
            ->prepareCollectionForReport();
        $this->setCollection($collection);
        return parent::_prepareCollection();

    }

    protected function _prepareColumns()
    {
        $this->addColumn('page_id', array(
            'header' => $this->helper('gomage_seobooster')->__('Page ID'),
            'index'  => 'page_id',
            'type'   => 'number',
            'width'  => 20,
        ));
        $this->addColumnAfter('page_name', array(
            'header' => $this->helper('gomage_seobooster')->__('Page Title'),
            'index'  => 'page_name',
            'type'   => 'text',
        ), 'page_id');

        $this->addColumnAfter('action', array(
            'header'  => $this->helper('gomage_seobooster')->__('Action'),
            'width'   => '100px',
            'type'    => 'action',
            'getter'  => 'getPageId',
            'actions' => array(
                array(
                    'caption' => $this->helper('gomage_seobooster')->__('Edit'),
                    'url'     => array('base' => '*/cms_page/edit'),
                    'field'   => 'page_id',
                ),
            ),
            'filter'    => false,
            'sortable'  => false,
            'is_system' => true,
        ), GoMage_SeoBooster_Helper_Analyzer::META_KEYWORD_FIELD);

        parent::_prepareColumns();

        $this->removeColumn(GoMage_SeoBooster_Helper_Analyzer::DESCRIPTION_FIELD);
        $this->removeColumn(GoMage_SeoBooster_Helper_Analyzer::META_TITLE_FIELD);

        return $this;
    }

    protected function _getDuplicateAction()
    {
        return GoMage_SeoBooster_Helper_Analyzer::PAGE_DUPLICATE_ACTION;
    }
}
