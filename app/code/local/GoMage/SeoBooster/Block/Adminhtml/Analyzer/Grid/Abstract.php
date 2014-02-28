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
        $this->addColumn('name_chars_count', array(
            'header' => $this->helper('gomage_seobooster')->__('Name'),
            'index'  => 'name_chars_count',
            'options' => GoMage_SeoBooster_Model_Analyzer::getErrorsOptions(),
            'renderer' => 'gomage_seobooster/adminhtml_analyzer_grid_renderer_options',
            'analyze_field' => GoMage_SeoBooster_Helper_Analyzer::NAME_FIELD
        ));

        $this->addColumn('description_chars_count', array(
            'header' => $this->helper('gomage_seobooster')->__('Description'),
            'index'  => 'description_chars_count',
            'type'   => 'options',
            'options' => GoMage_SeoBooster_Model_Analyzer::getErrorsOptions()
        ));

        $this->addColumn('meta_title_chars_count', array(
            'header' => $this->helper('gomage_seobooster')->__('Title'),
            'index'  => 'meta_title_chars_count',
            'renderer' => 'gomage_seobooster/adminhtml_analyzer_grid_renderer_options',
            'options' => GoMage_SeoBooster_Model_Analyzer::getErrorsOptions(),
        ));

        $this->addColumn('meta_description_chars_count', array(
            'header' => $this->helper('gomage_seobooster')->__('Meta Description'),
            'index'  => 'meta_description_chars_count',
            'renderer' => 'gomage_seobooster/adminhtml_analyzer_grid_renderer_options',
            'options' => GoMage_SeoBooster_Model_Analyzer::getErrorsOptions()
        ));

        $this->addColumn('meta_keyword_qty', array(
            'header' => $this->helper('gomage_seobooster')->__('Meta Keywords'),
            'index'  => 'meta_keyword_qty',
            'renderer' => 'gomage_seobooster/adminhtml_analyzer_grid_renderer_options',
            'options' => GoMage_SeoBooster_Model_Analyzer::getErrorsOptions()
        ));

        return parent::_prepareColumns();
    }
}
