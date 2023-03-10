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

class GoMage_SeoBooster_Model_Tag_Url
{
    /**
     * Static URL Rewrite Instance
     *
     * @var Mage_Core_Model_Url_Rewrite
     */
    protected static $_urlRewrite;

    /**
     * Static URL instance
     *
     * @var Mage_Core_Model_Url
     */
    protected static $_url;

    /**
     * Tag url suffix
     *
     * @var string
     */
    protected $_tagUrlSuffix = '.html';

    /**
     * Return resource model
     *
     * @return GoMage_SeoBooster_Model_Resource_Tag_Url
     */
    protected function _getResourceModel()
    {
        return Mage::getResourceSingleton('gomage_seobooster/tag_url');
    }

    /**
     * Retrieve URL Instance
     *
     * @return Mage_Core_Model_Url
     */
    public function getUrlInstance()
    {
        if (!self::$_url) {
            self::$_url = Mage::getModel('gomage_seobooster/url');
        }
        return self::$_url;
    }

    /**
     * Refresh tag url rewrite
     *
     * @param Mage_Tag_Model_Tag $tag Tag
     * @return $this
     */
    public function refreshTagRewrite(Mage_Tag_Model_Tag $tag)
    {
        if (!$tag || !$tag->getId()) {
            return $this;
        }

        $idPath      = $this->generatePath('id', $tag);
        $targetPath  = $this->generatePath('target', $tag);
        $requestPath = $this->generatePath('request', $tag);

        $rewriteData = array(
            'id_path'       => $idPath,
            'request_path'  => $requestPath,
            'target_path'   => $targetPath,
            'is_system'     => 0
        );

        $urlRewriteModel = Mage::getModel('core/url_rewrite');
        $urlRewriteModel->load('tag/'.$tag->getId(), 'id_path');
        if ($urlRewriteModel->getId()) {
            if ($tag->isDeleted()) {
                $urlRewriteModel->delete();
                return $this;
            }
            $urlRewriteModel->addData($rewriteData);
            $urlRewriteModel->save();
        } else {
            $urlRewriteModel->setData($rewriteData)->save();
        }
        if ($tag->dataHasChangedFor('url_key')) {
            $tag->save();
        }

        return $this;
    }

    /**
     * Refresh all tags url rewrite
     *
     * @return $this
     */
    public function refreshTagsRewrites()
    {
        $tags = $this->_getTagsCollection();
        foreach ($tags as $tag) {
            $this->refreshTagRewrite($tag);
        }
    }

    /**
     * Generate path for url rewrite
     * @param string             $type Path type
     * @param Mage_Tag_Model_Tag $tag  Tag
     * @return string
     */
    public function generatePath($type = 'target', $tag)
    {
        if (!$tag || !$tag->getId()) {
            Mage::throwException(Mage::helper('gomage_seobooster')->__('Please specify tag.'));
        }

        if ($type == 'id') {
            return 'tag' . '/' . $tag->getId();
        } elseif ($type == 'request') {
            $urlKey = $this->_formatUrlKey($tag->getName());
            if ($this->_checkUrlKeyUnique($urlKey, $tag->getId())) {
                $urlKey .= '-'. $tag->getId();
            }
            if ($urlKey != $tag->getUrlKey()) {
                $tag->setUrlKey($urlKey);
            }

            $path = $this->_getTagRewritePath() . '/' . $urlKey . $this->_tagUrlSuffix;
            return $path;
        }

        return 'tag/product/list/tagId/'. $tag->getId();
    }

    /**
     * Format Key for URL
     *
     * @param string $str
     * @return string
     */
    protected function _formatUrlKey($str)
    {
        return Mage::helper('gomage_seobooster')->formatUrlKey($str);
    }

    /**
     * Return tags url rewrite path
     *
     * @return string
     */
    protected function _getTagRewritePath()
    {
        return Mage::helper('gomage_seobooster')->getTagRewritePath();
    }

    public function getUrl(Mage_Tag_Model_Tag $tag, $params = array())
    {
        $routePath      = '';
        $routeParams    = $params;
        $storeId = null;

        $idPath = sprintf('tag/%d', $tag->getId());

        $rewrite = $this->getUrlRewrite();
        $rewrite->loadByIdPath($idPath);
        if ($rewrite->getId()) {
            $requestPath = $rewrite->getRequestPath();
        }

        if (!empty($requestPath)) {
            $routeParams['_direct'] = $requestPath;
        } else {
            $routePath = 'tag/product/list/tagId';
            $routeParams['id']  = $tag->getId();
        }

        // reset cached URL instance GET query params
        if (!isset($routeParams['_query'])) {
            $routeParams['_query'] = array();
        }

        return $this->getUrlInstance()->setStore($storeId)
            ->getUrl($routePath, $routeParams);
    }

    /**
     * Retrieve URL Rewrite Instance
     *
     * @return Mage_Core_Model_Url_Rewrite
     */
    public function getUrlRewrite()
    {
        if (!self::$_urlRewrite) {
            self::$_urlRewrite = Mage::getModel('core/url_rewrite');
        }
        return self::$_urlRewrite;
    }

    protected function _getTagsCollection()
    {
        $tags = Mage::getModel('tag/tag')->getCollection();
        $tags->getSelect()->group('main_table.tag_id');

        return $tags;
    }

    /**
     * Check tag url key unique
     *
     * @param string $urlKey Url Key
     * @param int    $tagId  Tag Id
     * @return string
     */
    protected function _checkUrlKeyUnique($urlKey, $tagId = null)
    {
        $resource = Mage::getSingleton('core/resource');
        $adapter = $resource->getConnection('core_read');
        $select = $adapter->select();

        $select->from($resource->getTableName('tag/tag'))
            ->where("url_key = ?", $urlKey);
        if (!is_null($tagId)) {
            $select->where("tag_id != ?", $tagId);
        }

        return $adapter->fetchOne($select);
    }
}
