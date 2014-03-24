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

class GoMage_SeoBooster_Block_Page_Html_Head_Opengraph extends Mage_Core_Block_Template
{
    protected $_items = array();

    /**
     * Add Item
     *
     * @param string $property Property
     * @param string $content  Content
     */
    public function addItem($property, $content)
    {
        if (!isset($this->_items[$property])) {
            $this->_items[$property] = array();
        }
        $this->_items[$property][$content] = $content;
    }

    /**
     * Return items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }
}
