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
class GoMage_SeoBooster_Model_Tag_Url
{
    /**
     * Tag url suffix
     *
     * @var string
     */
    protected $_tagUrlSuffix = '.html';

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
        if ($urlRewriteId = $tag->getUrlRewriteId()) {
            $urlRewriteModel->load($urlRewriteId);
            if ($urlRewriteModel->getId()) {
                if ($tag->isDeleted()) {
                    $urlRewriteModel->delete();
                    return $this;
                }

                $urlRewriteModel->addData($rewriteData);
                $urlRewriteModel->save();
            }
        } else {
            $urlRewriteModel->setData($rewriteData)->save();
            $tag->setData('url_rewrite_id', $urlRewriteModel->getId())->save();
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
        $tags = Mage::getModel('tag/tag')->getCollection();

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
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
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
}
