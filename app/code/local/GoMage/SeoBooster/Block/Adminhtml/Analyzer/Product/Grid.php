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
    }

    protected function _prepareCollection()
    {
        $collection = new Varien_Data_Collection();
        $this->setCollection($collection);
        return parent::_prepareCollection();

    }

    protected function _prepareColumns()
    {
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

        return parent::_prepareColumns();
    }
}
