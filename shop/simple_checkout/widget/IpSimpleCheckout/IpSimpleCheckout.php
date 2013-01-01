<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\shop\simple_checkout\widget;


class IpSimpleCheckout extends \Modules\standard\content_management\Widget
{


    public function getTitle()
    {
        global $parametersMod;
        return $parametersMod->getValue('shop', 'simple_checkout', 'admin_translations', 'widget_title');
    }

    public function dataForJs($data)
    {
        global $parametersMod;
        $data['google_priority'] = $parametersMod->getValue('shop', 'simple_checkout', 'options', 'google_priority');
        $data['paypal_priority'] = $parametersMod->getValue('shop', 'simple_checkout', 'options', 'paypal_priority');
        return $data;
    }

    /**
     *
     *
     * @param $widgetId
     * @param $postData
     * @param $currentData
     * @return array data to be stored to the database
     */
    public function update($widgetId, $postData, $currentData)
    {
        global $parametersMod;

        $dataToStore = array(
            'title' => $postData['title'],
            'currency' => $postData['currency'],
            'price' => $postData['price'],
            'successUrl' => $postData['successUrl'],
            'requireLogin' => $postData['requireLogin'],
            'productId' => $postData['productId']
        );

        $parametersMod->setValue('shop', 'simple_checkout', 'options', 'paypal_active', $postData['paypalActive']);
        $parametersMod->setValue('shop', 'simple_checkout', 'options', 'paypal_priority', $postData['paypalPriority']);
        $parametersMod->setValue('shop', 'simple_checkout', 'options', 'paypal_email', $postData['paypalEmail']);

        $modelGoogle = \Modules\shop\simple_checkout\ModelGoogle::instance();
        $modelGoogle->setActive($postData['googleActive']);
        $parametersMod->setValue('shop', 'simple_checkout', 'options', 'google_priority', $postData['googlePriority']);
        $modelGoogle->setMerchantId($postData['googleMerchantId']);
        $modelGoogle->setMerchantKey($postData['googleMerchantKey']);

        return $dataToStore;
    }

    public function managementHtml($instanceId, $data, $layout)
    {
        global $parametersMod;
        global $site;

        $data['instanceId'] = $instanceId;
        $data['paypalActive'] = $parametersMod->getValue('shop', 'simple_checkout', 'options', 'paypal_active');
        $data['paypalPriority'] = $parametersMod->getValue('shop', 'simple_checkout', 'options', 'paypal_priority');
        $data['paypalEmail'] = $parametersMod->getValue('shop', 'simple_checkout', 'options', 'paypal_email');

        $modelGoogle = \Modules\shop\simple_checkout\ModelGoogle::instance();

        $data['googleActive'] = $modelGoogle->getActive();
        $data['googlePriority'] = $parametersMod->getValue('shop', 'simple_checkout', 'options', 'google_priority');
        $data['googleMerchantId'] = $modelGoogle->getMerchantId();
        $data['googleMerchantKey'] = $modelGoogle->getMerchantKey();


        return parent::managementHtml($instanceId, $data, $layout);
    }


    public function previewHtml($instanceId, $data, $layout)
    {
        global $parametersMod;
        global $session;
        global $site;

        $modelGoogle = \Modules\shop\simple_checkout\ModelGoogle::instance();

        if ($modelGoogle->correctConfiguration()) {


            if (empty($data['requireLogin']) || $session->loggedIn()) {
                $data['googleButton'] = $this->getGoogleCheckoutButton($instanceId, $data);
            } else {
                $params = array(
                    'g' => 'shop',
                    'm' => 'simple_checkout',
                    'a' => 'requireLogin',
                    'method' => 'google',
                    'widgetInstanceId' => $instanceId
                );
                $loginUrl = $site->generateUrl(null, null, null, $params);
                $loginButton = \Ip\View::create('helperView/googleLoginRequired.php', array('loginUrl' => $loginUrl))->render();
                $data['googleButton'] = $loginButton;
            }
        }

        $modelPayPal = \Modules\shop\simple_checkout\ModelPayPal::instance();
        if ($modelPayPal->correctConfiguration()) {
            if (empty($data['requireLogin']) || $session->loggedIn()) {
                $data['paypalButton'] = $this->getPayPalButton($instanceId, $data);
            } else {
                $params = array(
                    'g' => 'shop',
                    'm' => 'simple_checkout',
                    'a' => 'requireLogin',
                    'method' => 'paypal',
                    'widgetInstanceId' => $instanceId
                );
                $loginUrl = $site->generateUrl(null, null, null, $params);
                $loginButton = \Ip\View::create('helperView/googleLoginRequired.php', array('loginUrl' => $loginUrl))->render();
                $data['googleButton'] = $loginButton;
            }


        }

        $answer = parent::previewHtml($instanceId, $data, $layout);

        if ($site->managementState()) {
            $incorrectFields = array();
            if (abs((float)$data['price']) < 0.0001) {
                $incorrectFields[] = $parametersMod->getValue('shop', 'simple_checkout', 'admin_translations', 'price');
            }
            if (mb_strlen($data['currency']) != 3) {
                $incorrectFields[] = $parametersMod->getValue('shop', 'simple_checkout', 'admin_translations', 'currency');
            }

            if ($incorrectFields) {
                $answer = \Ip\View::create('helperView/configurationError.php', array('incorrectFields' => $incorrectFields));
            }
        }


        return $answer;
    }

    public function getPayPalButton($instanceId, $data)
    {

        global $session;
        $modelPayPal= \Modules\shop\simple_checkout\ModelPayPal::instance();

        $returnUrl = isset($data['successUrl']) ? $data['successUrl'] : '';
        $itemTitle = isset($data['title']) ? $data['title'] : '';
        $itemPrice = isset($data['price']) ? $data['price'] : '';
        $currency = isset($data['currency']) ? $data['currency'] : '';
        $userId = $session->userId();

        $form = $modelPayPal->getPaypalForm($instanceId, $data['productId'], $userId, $itemTitle, $itemPrice, $currency, $returnUrl);
        return $form;
    }

    public function getGoogleCheckoutButton($instanceId, $data)
    {
        global $session;
        $modelGoogle = \Modules\shop\simple_checkout\ModelGoogle::instance();

        $returnUrl = isset($data['successUrl']) ? $data['successUrl'] : '';
        $itemTitle = isset($data['title']) ? $data['title'] : '';
        $itemPrice = isset($data['price']) ? $data['price'] : '';
        $currency = isset($data['currency']) ? $data['currency'] : '';
        $userId = $session->userId();

        $checkoutButton = $modelGoogle->getGoogleCheckoutButton($instanceId, $data['productId'], $userId, $itemTitle, $itemPrice, $currency, $returnUrl);
        return $checkoutButton;
    }


    /**
     * Check if passed currency, price and product id matches $instanceId.
     * Used to check payment notifications if user has paid the same amount and currency
     * @param int $instanceId
     * @param string $currency
     * @param string $price
     * @param string $productId
     * @return boolean true if data matches
     */
    public function checkOrder($instanceId, $currency, $price, $productId)
    {
        global $log;
        $widgetRecord = \Modules\standard\content_management\Model::getWidgetFullRecord($instanceId);
        if (empty($widgetRecord['data'])) {
            $log->log('shop/simple_checkout', 'widget has no data');
            return FALSE;
        }
        $widgetData = $widgetRecord['data'];


        if (empty($widgetData['price']) || empty($widgetData['currency']) || empty($widgetData['productId'])) {
            $log->log('shop/simple_checkout', 'missing widget data.');
            return FALSE;
        }


        if ($widgetData['price'] != $price || $widgetData['currency'] != $currency || $widgetData['productId'] != $productId) {
            $log->log('shop/simple_checkout', 'Widget data doesn\'t match.');
            return FALSE;
        }

        return TRUE;
    }


}