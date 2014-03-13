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
class GoMage_SeoBooster_Block_Adminhtml_Config_Form_Renderer_Notice
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Enter description here...
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<td colspan="4"><span style="color: #FBDB0C; padding-left: 5px">'.
            $this->__('After enabling or changing any Rewrite Path you need to rebuild required indexes in <a href="%s">Index Management</a>', $this->_getIndexUrl()) .'</span></td>';

        return $this->_decorateRowHtml($element, $html);
    }

    protected function _getIndexUrl()
    {
        return Mage::helper('adminhtml')->getUrl('*/process/list');
    }
}
