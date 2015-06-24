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
class GoMage_SeoBooster_Model_Observer
{
    /**
     * Refresh tag url rewrite if tag is new or name updated
     *
     * @param Varien_Event_Observer $observer Observer
     * @return $this
     */
    public function refreshTagUrlRewrite(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $tag   = $event->getDataObject();

        if (!Mage::helper('gomage_seobooster')->canUseTagUrlRewrite()) {
            return $this;
        }

        if (!$tag || !$tag->getId()) {
            return $this;
        }

        if ($tag->isObjectNew() || ($tag->getOrigData('name') != $tag->getName())) {
            Mage::getModel('gomage_seobooster/tag_url')->refreshTagRewrite($tag);
        }
    }

    /**
     * Remove tag url rewrite if tag deleted
     *
     * @param Varien_Event_Observer $observer Observer
     * @return $this
     */
    public function removeTagUrlRewrite(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $tag   = $event->getDataObject();

        if (!Mage::helper('gomage_seobooster')->canUseTagUrlRewrite()) {
            return $this;
        }

        $tag->isDeleted(true);
        Mage::getModel('gomage_seobooster/tag_url')->refreshTagRewrite($tag);
    }

    public function refreshReviewUrlRewrite(Varien_Event_Observer $observer)
    {
        $event  = $observer->getEvent();
        $review = $event->getDataObject();

        if (!Mage::helper('gomage_seobooster')->canUseProductReviewsUrlRewrite()) {
            return $this;
        }

        if ($review->isObjectNew()) {
            Mage::getModel('gomage_seobooster/review_url')->refreshReviewRewrite($review);
        }
    }

    public function removeReviewUrlRewrite(Varien_Event_Observer $observer)
    {
        $event  = $observer->getEvent();
        $review = $event->getDataObject();

        if (!Mage::helper('gomage_seobooster')->canUseProductReviewsUrlRewrite()) {
            return $this;
        }

        $review->isDeleted(true);
        Mage::getModel('gomage_seobooster/review_url')->refreshReviewRewrite($review);
    }

    /**
     * Add fields to meta tab of cms page
     *
     * @param Varien_Event_Observer $observer
     */
    public function addCmsPageMetaFields(Varien_Event_Observer $observer)
    {
        $event    = $observer->getEvent();
        $form     = $event->getForm();
        $fieldset = $form->getElement('meta_fieldset');

        $fieldset->addField('canonical_url_store', 'select', array(
                'label'    => Mage::helper('gomage_seobooster')->__('Cross-Domain Canonical URL'),
                'title'    => Mage::helper('gomage_seobooster')->__('Cross-Domain Canonical URL'),
                'name'     => 'canonical_url_store',
                'required' => true,
                'options'  => Mage::getModel('gomage_seobooster/config_source_canonical_catalog_storeurl')->getOptionArray(),
            )
        );
        $fieldset->addField('robots', 'select', array(
                'label'    => Mage::helper('gomage_seobooster')->__('Robots'),
                'title'    => Mage::helper('gomage_seobooster')->__('Robots'),
                'name'     => 'robots',
                'required' => true,
                'options'  => Mage::getModel('gomage_seobooster/config_source_robots')->getOptionArray(),
            )
        );

        $fieldset->addField('exclude_from_sitemap', 'select', array(
                'label'    => Mage::helper('gomage_seobooster')->__('Exclude from XML Sitemap'),
                'title'    => Mage::helper('gomage_seobooster')->__('Exclude from XML Sitemap'),
                'name'     => 'exclude_from_sitemap',
                'required' => true,
                'options'  => Mage::getModel('eav/entity_attribute_source_boolean')->getOptionArray(),
            )
        );
    }

    static public function checkK(Varien_Event_Observer $event)
    {
        $key = Mage::getStoreConfig('gomage_activation/seobooster/key');
        Mage::helper('gomage_seobooster')->a($key);
    }

    public function controllerFrontInitBefore(Varien_Event_Observer $observer)
    {
        if (Mage::helper('gomage_seobooster')->canAddTrailingSlash()) {
            $app        = Mage::app();
            $reflection = new ReflectionClass($app);
            $property   = $reflection->getProperty('_request');
            $property->setAccessible(true);
            $request = new GoMage_SeoBooster_Controller_Request_Http();
            if ($request->has('q')) {
                $request->setParam('q', trim($request->get('q'), '/'));
            }
            $property->setValue($app, $request);
        }
    }

}
