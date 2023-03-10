<?php

/**
 * GoMage Seo Booster Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2013-2015 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use/
 * @version      Release: 1.3.0
 * @since        Available since Release 1.0.0
 */
class GoMage_SeoBooster_Model_Sitemap_Sitemap extends Mage_Sitemap_Model_Sitemap
{
    protected $_linksCount = 0;

    protected $_filesCount = 1;

    /**
     * Generate XML file
     *
     * @return Mage_Sitemap_Model_Sitemap
     */
    public function generateXml()
    {
        if (!Mage::helper('gomage_seobooster')->isEnabled()) {
            return parent::generateXml();
        }

        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->getPath()));
        $this->_removeOldFiles();
        $this->_openXmlFile($io);

        if ($io->fileExists($this->getSitemapIndexFilename()) && !$io->isWriteable($this->getSitemapIndexFilename())) {
            Mage::throwException(Mage::helper('sitemap')->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.', $this->getSitemapIndexFilename(), $this->getPath()));
        }

        $storeId = $this->getStoreId();
        $date    = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);

        /**
         * Generate categories sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/category/changefreq', $storeId);
        $priority   = (string)Mage::getStoreConfig('sitemap/category/priority', $storeId);
        $collection = Mage::getResourceModel('gomage_seobooster/sitemap_catalog_category')->getCollection($storeId);
        foreach ($collection as $item) {
            if ($item->getExcludeFromSitemap()) {
                continue;
            }
            $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                htmlspecialchars($baseUrl . $item->getUrl()),
                $date,
                $changefreq,
                $priority
            );
            $io->streamWrite($xml);
            $this->_checkLimits($io);
        }
        unset($collection);

        /**
         * Generate products sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/product/changefreq', $storeId);
        $priority   = (string)Mage::getStoreConfig('sitemap/product/priority', $storeId);
        $collection = Mage::getResourceModel('gomage_seobooster/sitemap_catalog_product')->getCollection($storeId);
        foreach ($collection as $item) {
            if ($item->getExcludeFromSitemap()) {
                continue;
            }
            $images = $this->_getProductImages($item, $storeId);
            $xml    = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority>%s</url>',
                htmlspecialchars($baseUrl . $item->getUrl()),
                $date,
                $changefreq,
                $priority,
                $images
            );

            $io->streamWrite($xml);
            $this->_checkLimits($io);
        }
        unset($collection);

        /**
         * Generate cms pages sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/page/changefreq', $storeId);
        $priority   = (string)Mage::getStoreConfig('sitemap/page/priority', $storeId);
        $collection = Mage::getResourceModel('gomage_seobooster/sitemap_cms_page')->getCollection($storeId);
        foreach ($collection as $item) {
			if ($item->getExcludeFromSitemap()) {
                continue;
            }
            $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                htmlspecialchars($baseUrl . $item->getUrl()),
                $date,
                $changefreq,
                $priority
            );
            $io->streamWrite($xml);
            $this->_checkLimits($io);
        }
        unset($collection);

        $this->_addTags($io, $storeId);
        $this->_addAdditionalLinks($io, $storeId);

        $io->streamWrite('</urlset>');
        $io->streamClose();

        if (Mage::helper('gomage_seobooster/sitemap')->canSplitSitemap()) {
            $this->generateIndexFile();
        }
        $this->setSitemapTime(Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s'));
        $this->save();

        $this->submitSitemap();

        return $this;
    }

    public function generateIndexFile()
    {
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->getPath()));

        $filename         = $this->getData('sitemap_filename');
        $extension        = strrchr($filename, '.');
        $filenameTemplate = str_replace($extension, '', $filename) . '_%d' . $extension;

        if ($io->fileExists($filename) && !$io->isWriteable($filename)) {
            Mage::throwException(Mage::helper('sitemap')->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.', $this->getData('sitemap_filename'), $this->getPath()));
        }

        $date = Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s');
        $io->streamOpen($filename, 'w+');
        $io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        $io->streamWrite('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
        for ($index = 1; $index <= $this->_filesCount; $index++) {
            $xml = sprintf('<sitemap><loc>%s</loc><lastmod>%s</lastmod></sitemap>',
                sprintf($filenameTemplate, $index),
                $date
            );
            $io->streamWrite($xml);
        }
        $io->streamWrite('</sitemapindex>');
        $io->streamClose();
    }

    /**
     * Return product images
     *
     * @param $product
     * @param $storeId
     * @return string
     */
    protected function _getProductImages($product, $storeId)
    {
        $images      = '';
        $imagesCount = (int)Mage::helper('gomage_seobooster/sitemap')->getMaxImagesPerProduct();
        $imagesInc   = 0;

        if (!Mage::helper('gomage_seobooster/sitemap')->canIncludeProductImages()) {
            return $images;
        }

        $product = Mage::getModel('catalog/product')->load($product->getId());
        $product->setStoreId($storeId);

        $medialGallery = $product->getMediaGalleryImages();

        foreach ($medialGallery as $image) {
            if ($imagesCount != 0 && $imagesInc >= $imagesCount) {
                return $images;
            }
            $path = Mage::getSingleton('catalog/product_media_config')->getMediaUrl($image->getFile());
            $images .= '<image:image><image:loc>' . htmlspecialchars($path) . '</image:loc></image:image>';
            $imagesInc++;
        }

        return $images;
    }

    /**
     * Add tags to sitemap
     *
     * @param Varien_Io_File $io Input/Output Stream
     * @param int $storeId Store Id
     * @return $this
     */
    protected function _addTags($io, $storeId)
    {
        if (!Mage::helper('gomage_seobooster/sitemap')->canAddProductTags()) {
            return $this;
        }

        $mageUrl    = Mage::getBaseUrl();
        $baseUrl    = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        $date       = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        $changefreq = Mage::helper('gomage_seobooster/sitemap')->getProductTagsChangefreq();
        $priority   = Mage::helper('gomage_seobooster/sitemap')->getProductTagsPriority();

        $tags = Mage::getModel('tag/tag')->getPopularCollection()
            ->joinFields(Mage::app()->getStore()->getId())
            ->load();

        foreach ($tags as $tag) {
            $url = str_replace($mageUrl, $baseUrl, $tag->getTaggedProductsUrl());
            $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                htmlspecialchars($url),
                $date,
                $changefreq,
                $priority
            );
            $io->streamWrite($xml);
            $this->_checkLimits($io);
        }
        unset($collection);

        return $this;
    }

    /**
     * Add additional links to sitemap
     *
     * @param Varien_Io_File $io Input/Output Stream
     * @param int $storeId Store Id
     * @return $this
     */
    protected function _addAdditionalLinks($io, $storeId)
    {
        $mageUrl         = Mage::getBaseUrl();
        $baseUrl         = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        $additionalLinks = Mage::helper('gomage_seobooster/sitemap')->getAdditionalLinks();
        $date            = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        $changefreq      = Mage::helper('gomage_seobooster/sitemap')->getAdditionalLinksChangefreq();
        $priority        = Mage::helper('gomage_seobooster/sitemap')->getAdditionalLinksPriority();

        foreach ($additionalLinks as $link) {
            $url = str_replace($mageUrl, $baseUrl, $link['url']);
            $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                htmlspecialchars($url),
                $date,
                $changefreq,
                $priority
            );
            $io->streamWrite($xml);
            $this->_checkLimits($io);
        }

        return $this;
    }

    protected function _checkLimits($io)
    {
        if (Mage::helper('gomage_seobooster/sitemap')->canSplitSitemap()) {
            $this->_linksCount++;
            if (($this->_linksCount == Mage::helper('gomage_seobooster/sitemap')->getMaxLinksCount())
                || ($io->streamStat('size') / 1024 >= Mage::helper('gomage_seobooster/sitemap')->getMaxFileSize())
            ) {
                $this->_linksCount = 0;
                $this->_filesCount++;
                $this->_closeXmlFile($io);
                $this->_openXmlFile($io);
            }
        }
    }

    protected function _closeXmlFile($io, $append = false)
    {
        if (!$append) {
            $io->streamWrite('</urlset>');
        }
        $io->streamClose();
    }

    protected function _openXmlFile($io, $append = false)
    {
        if ($io->fileExists($this->getSitemapIndexFilename()) && !$io->isWriteable($this->getSitemapIndexFilename())) {
            Mage::throwException(Mage::helper('sitemap')->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.', $this->getSitemapIndexFilename(), $this->getPath()));
        }
        $mode = $append ? 'a+' : 'w+';

        $io->streamOpen($this->getSitemapIndexFilename(), $mode);

        if (!$append) {
            $io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
            $io->streamWrite('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n" . ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">');
        }
    }

    public function getSitemapIndexFilename()
    {
        if (Mage::helper('gomage_seobooster/sitemap')->canSplitSitemap()) {
            $filename  = $this->getData('sitemap_filename');
            $extension = strrchr($filename, '.');
            $filename  = str_replace($extension, '', $filename);
            $filename  = $filename . sprintf('_%d', $this->_filesCount) . $extension;

            return $filename;
        }

        return $this->getData('sitemap_filename');
    }

    public function submitSitemap()
    {
        if (!Mage::helper('gomage_seobooster/sitemap')->isAutosubmitEnabled() || !$this->getId()) {
            return;
        }

        $fileName   = preg_replace('/^\//', '', $this->getSitemapPath() . $this->getSitemapIndexFilename());
        $sitemapUrl = Mage::app()->getStore($this->getStoreId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $fileName;
        $engines    = Mage::helper('gomage_seobooster/sitemap')->getSearchEngines();

        foreach ($engines as $engine) {
            $engineUrl = Mage::helper('gomage_seobooster/sitemap')->getSearchEngineUrl($engine);
            $submitUrl = sprintf($engineUrl . '%s', urldecode($sitemapUrl));

            $handle = curl_init();
            curl_setopt($handle, CURLOPT_URL, $submitUrl);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
            curl_exec($handle);
            curl_close($handle);
        }
    }

    protected function _removeOldFiles()
    {
        $filename  = $this->getData('sitemap_filename');
        $extension = strrchr($filename, '.');
        $filename  = str_replace($extension, '', $filename);
        $mask      = $this->getPath() . '{' . $filename . '}_*' . $extension;
        array_map(function ($path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }, glob($mask, GLOB_BRACE)
        );
    }
}
