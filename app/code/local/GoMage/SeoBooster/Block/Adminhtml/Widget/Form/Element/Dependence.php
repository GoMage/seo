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
 * Form element dependencies mapper
 * Assumes that one element may depend on other element values.
 * Will toggle as "enabled" only if all elements it depends from toggle as true.
 *
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Block
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Block_Adminhtml_Widget_Form_Element_Dependence extends Mage_Adminhtml_Block_Widget_Form_Element_Dependence
{
    const MULTIPLE_VALUE_PREFIX = 'multiple-';

    /**
     * Field dependences JSON map generator
     *
     * @return string
     */
    protected function _getDependsJson()
    {
        $result = array();
        foreach ($this->_depends as $to => $row) {
            foreach ($row as $from => $value) {
                if (strpos($value, self::MULTIPLE_VALUE_PREFIX) !== false) {
                    $value = str_replace(self::MULTIPLE_VALUE_PREFIX, '', $value);
                    $value = explode('-', $value);
                }
                $result[$this->_fields[$to]][$this->_fields[$from]] = $value;
            }
        }
        return Mage::helper('core')->jsonEncode($result);
    }
}
