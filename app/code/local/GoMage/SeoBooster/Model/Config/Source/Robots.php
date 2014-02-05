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
 * @category   GoMage
 * @package    GoMage_SeoBooster
 * @subpackage Model
 * @author     Roman Bublik <rb@gomage.com>
 */
class GoMage_SeoBooster_Model_Config_Source_Robots extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array('value' => 0, 'label' => Mage::helper('gomage_seobooster')->__('Default')),
                array('value' => 'INDEX,FOLLOW', 'label' => 'INDEX, FOLLOW'),
                array('value' => 'NOINDEX,FOLLOW', 'label' => 'NOINDEX, FOLLOW'),
                array('value' => 'INDEX,NOFOLLOW', 'label' => 'INDEX, NOFOLLOW'),
                array('value' => 'NOINDEX,NOFOLLOW', 'label' => 'NOINDEX, NOFOLLOW'),
                array('value' => 'INDEX, FOLLOW, NOARCHIVE', 'label' => 'INDEX, FOLLOW, NOARCHIVE'),
                array('value' => 'INDEX, NOFOLLOW, NOARCHIVE', 'label' => 'INDEX, NOFOLLOW, NOARCHIVE'),
                array('value' => 'NOINDEX, NOFOLLOW, NOARCHIVE', 'label' => 'NOINDEX, NOFOLLOW, NOARCHIVE')
            );
        }

        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
}
