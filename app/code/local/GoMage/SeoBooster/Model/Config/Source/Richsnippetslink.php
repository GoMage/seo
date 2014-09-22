<?php

/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2014 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.1.0
 * @since        Available since Release 1.0.0
 */
class GoMage_SeoBooster_Model_Config_Source_Richsnippetslink
{

    const LINK        = 0;
    const BREADCRUMBS = 1;

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array(
                'label' => Mage::helper('gomage_seobooster')->__('Link'),
                'value' => self::LINK
            ),
            array(
                'label' => Mage::helper('gomage_seobooster')->__('Breadcrumbs'),
                'value' => self::BREADCRUMBS
            )
        );

        return $options;
    }
}