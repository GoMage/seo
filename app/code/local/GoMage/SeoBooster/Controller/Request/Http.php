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
class GoMage_SeoBooster_Controller_Request_Http extends Mage_Core_Controller_Request_Http
{
    public function setQuery($spec, $value = null)
    {
        if ((null === $value) && !is_array($spec)) {
            #require_once 'Zend/Controller/Exception.php';
            throw new Zend_Controller_Exception('Invalid value passed to setQuery(); must be either array of values or key/value pair');
        }
        if ((null === $value) && is_array($spec)) {
            foreach ($spec as $key => $value) {
                $this->setQuery($key, $value);
            }
            return $this;
        }
        $_GET[(string)$spec] = rtrim($value, '/');
        return $this;
    }

}
