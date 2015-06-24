<?php
/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2015 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.2.0
 * @since        Available since Release 1.0.0
 */

class GoMage_SeoBooster_Model_Config_Source_Layered_Separator
{
    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array(
                'label' => "=",
                'value' => "="
            ),
            array(
                'label' => "/",
                'value' => "/"
            ),
            array(
                'label' => "-",
                'value' => "-"
            )
        );

        return $options;
    }
}
