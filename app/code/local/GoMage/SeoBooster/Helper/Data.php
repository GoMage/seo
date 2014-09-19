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
class GoMage_SeoBooster_Helper_Data extends Mage_Core_Helper_Data
{
    const CANONICAL_URL_DISABLED            = 0;
    const CANONICAL_URL_PRODUCTS            = 1;
    const CANONICAL_URL_CATEGORIES          = 2;
    const CANONICAL_URL_PRODUCTS_CATEGORIES = 3;

    const CANONICAL_URL_DEFAULT_DOMAIN_VALUE = 0;

    /**
     * Return module status
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfig('gomage_seobooster/general/enabled') &&
        (in_array(Mage::app()->getStore()->getWebsiteId(), $this->getAvailableWebsites()) ||
            Mage::app()->getStore()->getWebsiteId() == 0);
    }

    public function getIsAnymoreVersion($major, $minor, $revision = 0)
    {
        $version_info = Mage::getVersionInfo();

        if ($version_info['major'] > $major) {
            return true;
        } elseif ($version_info['major'] == $major) {
            if ($version_info['minor'] > $minor) {
                return true;
            } elseif ($version_info['minor'] == $minor) {
                if ($version_info['revision'] >= $revision) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return url by route
     *
     * @param string $route Route
     * @param array $params Route params
     * @param int|null $storeId Store Id
     * @return string
     */
    public function getUrl($route, $params = array(), $storeId = null)
    {
        if (!is_null($storeId)) {
            $store = Mage::app()->getStore($storeId);
        }

        if ($this->isEnabled()) {
            $urlModel = Mage::getModel('gomage_seobooster/url');
            if (isset($store)) {
                $urlModel->setStore($store);
            }

            return $urlModel->getUrl($route, $params);
        }
        if (isset($store)) {
            return $store->getUrl($route, $params);
        }

        return $this->_getUrl($route, $params);
    }

    public function canAddTrailingSlash()
    {
        return $this->isEnabled() && Mage::getStoreConfig('gomage_seobooster/general/add_trailing_slash');
    }

    public function addTrailingSlash($routePath)
    {
        if ($this->canAddTrailingSlash()) {
            if ((preg_match('/\.[a-z]{2,4}$/', $routePath) === 0)
                && (substr($routePath, -1, 1) !== '/')
            ) {
                return $routePath . '/';
            }
        }

        return $routePath;
    }

    /**
     * Can use url rewrite for product reviews
     *
     * @return bool
     */
    public function canUseProductReviewsUrlRewrite()
    {
        return $this->isEnabled() && Mage::getStoreConfig('gomage_seobooster/url_rewrite/enable_product_review_url_rewrite');
    }

    /**
     * Return product reviews url rewrite path
     *
     * @return string
     */
    public function getProductReviewsUrlRewritePath()
    {
        return Mage::getStoreConfig('gomage_seobooster/url_rewrite/product_review_rewrite_path');
    }

    /**
     * Can use url rewrite for tags
     *
     * @return bool
     */
    public function canUseTagUrlRewrite()
    {
        return $this->isEnabled() && Mage::getStoreConfig('gomage_seobooster/url_rewrite/enable_tag_url_rewrite');
    }

    /**
     * Return tags url rewrite path
     *
     * @return string
     */
    public function getTagRewritePath()
    {
        return Mage::getStoreConfig('gomage_seobooster/url_rewrite/tag_url_rewrite_path');
    }

    /**
     * Can use rss url rewrite
     *
     * @return bool
     */
    public function canUseRssUrlRewrite()
    {
        return $this->isEnabled() && Mage::getStoreConfig('gomage_seobooster/url_rewrite/enable_rss_url_rewrite');
    }

    /**
     * Return rss url rewrite path
     *
     * @return string
     */
    public function getRssUrlRewritePath()
    {
        return Mage::getStoreConfig('gomage_seobooster/url_rewrite/rss_url_rewrite_path');
    }

    /**
     * Is rich snippet enable
     *
     * @return mixed
     */
    public function isRichSnippetEnabled()
    {
        return $this->isEnabled() && Mage::getStoreConfig('gomage_seobooster/general/enable_rich_snippets');
    }

    /**
     * Format Key for URL
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }

    /**
     * Return robots for product, category or cms page
     *
     * @param Mage_Catalog_Model_Product|Mage_Catalog_Model_Category|Mage_Cms_Model_Page $entity Entity
     * @return string
     */
    public function getRobots($entity)
    {
        if (($robots = $entity->getRobots()) && $this->isEnabled()) {
            return $robots;
        }

        return Mage::getStoreConfig('design/head/default_robots');
    }

    public function getProductMaxPrice(Mage_Catalog_Model_Product $product)
    {
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            return $this->_getConfigurableProductMaxPrice($product);
        }

        return $product->getFinalPrice();
    }

    protected function _getConfigurableProductMaxPrice(Mage_Catalog_Model_Product $product)
    {
        $productPrice    = $product->getFinalPrice();
        $attributes      = $product->getTypeInstance()->getConfigurableAttributes($product);
        $attributesPrice = 0.0;
        foreach ($attributes as $attribute) {
            $attributeMaxPrice = 0.0;
            if ($prices = $attribute->getPrices()) {
                foreach ($prices as $price) {
                    if ($price['is_percent']) {
                        $priceValue = $productPrice * $price['pricing_value'] / 100;
                    } else {
                        $priceValue = $price['pricing_value'];
                    }
                    $attributeMaxPrice = max($attributeMaxPrice, $priceValue);
                }
            }
            $attributesPrice += $attributeMaxPrice;
        }

        $productPrice += $attributesPrice;
        return $productPrice;
    }

    public function getGroupedProductPrices(Mage_Catalog_Model_Product $product)
    {

        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
            $maxPrice           = 0.0;
            $minPrice           = $product->getMinimalPrice();
            $associatedProducts = $product->getTypeInstance()->getAssociatedProducts($product);

            foreach ($associatedProducts as $_product) {
                $maxPrice = max($maxPrice, $_product->getFinalPrice());
                $minPrice = min($minPrice, $_product->getFinalPrice());
            }

            return new Varien_Object(array('min_price' => $minPrice, 'max_price' => $maxPrice));
        }

        return new Varien_Object();
    }

    public function getAllStoreDomains()
    {
        $domains = array();
        foreach (Mage::app()->getWebsites() as $website) {
            $url = $website->getConfig('web/unsecure/base_url');
            if ($domain = trim(preg_replace('/^.*?\\/\\/(.*)?\\//', '$1', $url))) {
                $domains[] = $domain;
            }
            $url = $website->getConfig('web/secure/base_url');

            if ($domain = trim(preg_replace('/^.*?\\/\\/(.*)?\\//', '$1', $url))) {
                $domains[] = $domain;
            }
        }

        return array_unique($domains);
    }

    public function ga()
    {
        return Zend_Json::decode(base64_decode(Mage::helper('core')->decrypt(Mage::getStoreConfig('gomage_activation/seobooster/ar'))));
    }

    public function getAvailableWebsites()
    {
        return $this->_w();
    }

    protected function _w()
    {
        if (!Mage::getStoreConfig('gomage_activation/seobooster/installed') ||
            (intval(Mage::getStoreConfig('gomage_activation/seobooster/count')) > 10)
        ) {
            return array();
        }

        $time_to_update = 60 * 60 * 24 * 15;

        $r = Mage::getStoreConfig('gomage_activation/seobooster/ar');
        $t = Mage::getStoreConfig('gomage_activation/seobooster/time');
        $s = Mage::getStoreConfig('gomage_activation/seobooster/websites');

        $last_check = str_replace($r, '', Mage::helper('core')->decrypt($t));

        $allsites = explode(',', str_replace($r, '', Mage::helper('core')->decrypt($s)));
        $allsites = array_diff($allsites, array(""));

        if (($last_check + $time_to_update) < time()) {
            $this->a(Mage::getStoreConfig('gomage_activation/seobooster/key'),
                intval(Mage::getStoreConfig('gomage_activation/seobooster/count')),
                implode(',', $allsites)
            );
        }

        return $allsites;

    }

    public function a($k, $c = 0, $s = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, sprintf('https://www.gomage.com/index.php/gomage_downloadable/key/check'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'key=' . urlencode($k) . '&sku=seo-booster&domains=' . urlencode(implode(',', $this->getAllStoreDomains())) . '&ver=' . urlencode('1.1'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $content = curl_exec($ch);

        $r = Zend_Json::decode($content);
        $e = Mage::helper('core');
        if (empty($r)) {

            $value1 = Mage::getStoreConfig('gomage_activation/seobooster/ar');

            $groups = array(
                'seobooster' => array(
                    'fields' => array(
                        'ar'       => array(
                            'value' => $value1
                        ),
                        'websites' => array(
                            'value' => (string)Mage::getStoreConfig('gomage_activation/seobooster/websites')
                        ),
                        'time'     => array(
                            'value' => (string)$e->encrypt($value1 . (time() - (60 * 60 * 24 * 15 - 1800)) . $value1)
                        ),
                        'count'    => array(
                            'value' => $c + 1)
                    )
                )
            );

            Mage::getModel('adminhtml/config_data')
                ->setSection('gomage_activation')
                ->setGroups($groups)
                ->save();

            Mage::getConfig()->reinit();
            Mage::app()->reinitStores();

            return;
        }

        $value1 = '';
        $value2 = '';

        if (isset($r['d']) && isset($r['c'])) {
            $value1 = $e->encrypt(base64_encode(Zend_Json::encode($r)));


            if (!$s) {
                $s = Mage::getStoreConfig('gomage_activation/seobooster/websites');
            }

            $s = array_slice(explode(',', $s), 0, $r['c']);

            $value2 = $e->encrypt($value1 . implode(',', $s) . $value1);

        }
        $groups = array(
            'seobooster' => array(
                'fields' => array(
                    'ar'        => array(
                        'value' => $value1
                    ),
                    'websites'  => array(
                        'value' => (string)$value2
                    ),
                    'time'      => array(
                        'value' => (string)$e->encrypt($value1 . time() . $value1)
                    ),
                    'installed' => array(
                        'value' => 1
                    ),
                    'count'     => array(
                        'value' => 0)

                )
            )
        );

        Mage::getModel('adminhtml/config_data')
            ->setSection('gomage_activation')
            ->setGroups($groups)
            ->save();

        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();

    }

    public function notify()
    {
        $frequency = intval(Mage::app()->loadCache('gomage_notifications_frequency'));
        if (!$frequency) {
            $frequency = 24;
        }
        $last_update = intval(Mage::app()->loadCache('gomage_notifications_last_update'));

        if (($frequency * 60 * 60 + $last_update) > time()) {
            return false;
        }

        $timestamp = $last_update;
        if (!$timestamp) {
            $timestamp = time();
        }

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, sprintf('https://www.gomage.com/index.php/gomage_notification/index/data'));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'sku=seo-booster&timestamp=' . $timestamp . '&ver=' . urlencode('1.1'));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            $content = curl_exec($ch);

            $result = Zend_Json::decode($content);

            if ($result && isset($result['frequency']) && ($result['frequency'] != $frequency)) {
                Mage::app()->saveCache($result['frequency'], 'gomage_notifications_frequency');
            }

            if ($result && isset($result['data'])) {
                if (!empty($result['data'])) {
                    Mage::getModel('adminnotification/inbox')->parse($result['data']);
                }
            }
        } catch (Exception $e) {
        }

        Mage::app()->saveCache(time(), 'gomage_notifications_last_update');

    }

    public function formatUrlValue($value, $default = '')
    {
        $oldLocale  = setlocale(LC_COLLATE, "0");
        $localeCode = Mage::app()->getLocale()->getLocaleCode();
        setlocale(LC_COLLATE, $localeCode . '.UTF8', 'C.UTF-8', 'en_US.utf8');
        $value = iconv(mb_detect_encoding($value), 'ASCII//TRANSLIT', $value);
        setlocale(LC_COLLATE, $oldLocale);

        $value = strtolower($value);
        $value = preg_replace('#[^0-9a-z]+#i', '_', Mage::helper('catalog/product_url')->format($value));
        $value = trim($value, '_');

        return $value ? $value : $default;
    }

    public function isGoMageCheckoutEnabled()
    {
        $modules = (array)Mage::getConfig()->getNode('modules')->children();
        return isset($modules['GoMage_Checkout']) && $modules['GoMage_Checkout']->is('active');
    }

}
