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
 * Long description of the class (if any...)
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Block
 * @author     Roman Bublik <rb@gomage.com>
 */
abstract class GoMage_SeoBooster_Block_Adminhtml_Analyzer_Grid_Abstract extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init the grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('things_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
    }

    /**
     * Prepare grid collection
     *
     * @return Oggetto_Things_Block_Adminhtml_Thing_Grid
     */
    protected function _prepareCollection()
    {
        return parent::_prepareCollection();
    }

    /**
     * Define grid columns
     *
     * @return Oggetto_Things_Block_Adminhtml_Thing_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => $this->helper('gomage_seobooster')->__('ID'),
            'index'  => 'entity_id',
            'type'   => 'number',
            'width'  => 20,
        ));

        $this->addColumn('name_analyze', array(
            'header' => $this->helper('gomage_seobooster')->__('Name'),
            'index'  => 'name_analyze',
            'type'   => 'options',
            'options' => array()
        ));

        $this->addColumn('description', array(
            'header' => $this->helper('gomage_seobooster')->__('Description'),
            'index'  => 'description',
            'type'   => 'options',
            'options' => array()
        ));

        $this->addColumn('meta_title', array(
            'header' => $this->helper('gomage_seobooster')->__('Title'),
            'index'  => 'meta_title',
            'type'   => 'options',
            'options' => array()
        ));

        $this->addColumn('meta_description', array(
            'header' => $this->helper('gomage_seobooster')->__('Meta Description'),
            'index'  => 'meta_description',
            'type'   => 'options',
            'options' => array()
        ));

        $this->addColumn('meta_keywords', array(
            'header' => $this->helper('gomage_seobooster')->__('Meta Keywords'),
            'index'  => 'meta_keywords',
            'type'   => 'options',
            'options' => array()
        ));

        $this->addColumn('action',
            array(
                'header'  => $this->helper('gomage_seobooster')->__('Action'),
                'width'   => '100px',
                'type'    => 'action',
                'getter'  => 'getId',
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
            ));

        return parent::_prepareColumns();
    }
}
