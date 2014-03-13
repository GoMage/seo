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
class GoMage_SeoBooster_Block_Adminhtml_System_Config_Fieldset_Help
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    /**
     * Return header html for fieldset
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderHtml($element)
    {
        if (method_exists(parent, '_getHeaderTitleHtml')) {
            return static::_getHeaderHtml($element);
        }

        $default = !$this->getRequest()->getParam('website') && !$this->getRequest()->getParam('store');

        $html = '<div  class="entry-edit-head collapseable" >
        <button style="margin-right:25px; float: right;" onclick="window.open(\''. $this->_getHelpUrl() .'\', \'_blank\')" class="scalable go" type="button" id="glc_help"><span>'.$this->__('GoMage SEO Booster Wiki').'</span></button>
        <a id="'.$element->getHtmlId().'-head" href="#" onclick="Fieldset.toggleCollapse(\''.$element->getHtmlId().'\', \''.$this->getUrl('*/*/state').'\'); return false;">'.$element->getLegend().'</a></div>';
        $html.= '<input id="'.$element->getHtmlId().'-state" name="config_state['.$element->getId().']" type="hidden" value="'.(int)$this->_getCollapseState($element).'" />';
        $html.= '<fieldset class="'.$this->_getFieldsetCss().'" id="'.$element->getHtmlId().'">';
        $html.= '<legend>'.$element->getLegend().'</legend>';

        if ($element->getComment()) {
            $html .= '<div class="comment">'.$element->getComment().'</div>';
        }
        // field label column
        $html.= '<table cellspacing="0" class="form-list"><colgroup class="label" /><colgroup class="value" />';
        if (!$default) {
            $html.= '<colgroup class="use-default" />';
        }
        $html.= '<colgroup class="scope-label" /><colgroup class="" /><tbody>';

        return $html;
    }

    /**
     * Return header title part of html for fieldset
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderTitleHtml($element)
    {
        return '<div class="entry-edit-head collapseable" >
        <button style="margin-right:25px; float: right;" onclick="window.open(\''. $this->_getHelpUrl() .'\', \'_blank\')" class="scalable go" type="button" id="glc_help"><span>'.$this->__('GoMage SEO Booster Wiki').'</span></button>
        <a id="' . $element->getHtmlId()
        . '-head" href="#" onclick="Fieldset.toggleCollapse(\'' . $element->getHtmlId() . '\', \''
        . $this->getUrl('*/*/state') . '\'); return false;">' . $element->getLegend() . '</a></div>';
    }

    protected function _getHelpUrl()
    {
        return 'http://wiki.gomage.com/display/seobooster/Home';
    }
}
