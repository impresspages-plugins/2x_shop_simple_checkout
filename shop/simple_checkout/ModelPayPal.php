<?php
    /**
     * @package   ImpressPages
     * @copyright Copyright (C) 2012 JSC Apro Media.
     * @license   GNU/GPL, see ip_license.html
     */

namespace Modules\shop\simple_checkout;

class ModelPayPal
{
    const PAYPAL_POST_URL_TEST = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    const PAYPAL_POST_URL = 'https://www.paypal.com/cgi-bin/webscr';


    protected static $instance;

    protected function __construct() {}

    protected function __clone(){}

    /**
     * Get singleton instance
     * @return ModelPayPal
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new ModelPayPal();
        }

        return self::$instance;
    }

    public function processPayPalCallback()
    {
        global $log;
        global $site;

        $notificationData = $_POST;

        if(!array_key_exists("txn_id", $notificationData)) {
            $site->setOutput('');
            return;
        }

        $postUrl = $this->getPayPalUrl();

        $response = $this->httpPost($postUrl, $notificationData);

        if(!$response["status"]) {
            $log->log('shop/simple_checkout', 'Can\'t connect to PayPal. '.$response['error_msg'].' '.$response['error_no']  , $this->checkEncoding(json_encode($notificationData)));
            return;
        }

        $customData = json_decode($_POST['custom'], true);

        $userId = isset($customData['userId']) ? $customData['userId'] : null;
        $productId = isset($customData['productId']) ? $customData['productId'] : null;
        $widgetInstanceId = isset($customData['widgetInstanceId']) ? $customData['widgetInstanceId'] : null;
        $currency = isset($notificationData['mc_currency']) ? $notificationData['mc_currency'] : null;
        $price = isset($notificationData['mc_gross']) ? $notificationData['mc_gross'] : null;
        $buyerEmail = isset($notificationData['payer_email']) ? $notificationData['payer_email'] : null;

        //check notification values
        $widgetObject = \Modules\standard\content_management\Model::getWidgetObject('IpSimpleCheckout');
        $validOrder = $widgetObject->checkOrder($widgetInstanceId, $currency, $price, $productId);
        if (!$validOrder) {
            $log->log('shop/simple_checkout', 'paypal', 'Order price currency or other details doesn\'t match');
            return false;
        }
        if ($notificationData['payment_status'] != 'Completed') {
            $log->log('shop/simple_checkout', 'paypal', 'Incorrect payment status');
            return false;
        }




        if ($response["httpResponse"] == 'VERIFIED') {
            global $dispatcher;
            $log->log('shop/simple_checkout', 'Successful PayPal notification', json_encode($this->checkEncoding($_POST)));
            $completedOrderEvent = new  EventNewOrder($this, $buyerEmail, $price, $currency, $widgetInstanceId, $productId, $userId);
            $dispatcher->notify($completedOrderEvent);
        } else {
            $log->log('shop/simple_checkout', 'PayPal doesn\'t recognize the payment', json_encode($this->checkEncoding($_POST)));
            $log->log('shop/simple_checkout', 'PayPal doesn\'t recognize the payment2', json_encode($this->checkEncoding($response)));
        }

    }




    /**
     *
     * Enter description here ...
     * @param string $url
     * @param array $values
     * @return array
     */
    private function httpPost($url, $values) {
        $tmpAr = array_merge($values, array("cmd" => "_notify-validate"));
        $postFieldsAr = array();
        foreach ($tmpAr as $name => $value) {
            $postFieldsAr[] = "$name=".urlencode($value);
        }
        $postFields_ = implode("&", $postFieldsAr);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        //turning off the server and peer verification(TrustManager Concept).
        if (DEVELOPMENT_ENVIRONMENT) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, TRUE);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);

        //setting the nvpreq as POST FIELD to curl
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postFields_);

        //getting response from server
        $httpResponse = curl_exec($ch);

        if(!$httpResponse) {
            return array("status" => false, "error_msg" => curl_error($ch), "error_no" => curl_errno($ch));
        }


        return array("status" => true, "httpResponse" => $httpResponse);

    }

    public function getPaypalForm($widgetInstanceId, $productId, $userId, $itemTitle, $itemPrice, $currency, $returnUrl){
        global $parametersMod;
        global $session;
        global $site;



        $postUrl = $this->getPayPalUrl();
        $form = new \Modules\developer\form\Form();
        $form->setAction($postUrl);

        $form->addClass('ipwSubscribe');
        $form->removeClass('ipModuleForm');

        $privateData = array (
            'widgetInstanceId' => $widgetInstanceId,
            'productId' => $productId,
            'userId' => $userId ? $userId : ''
        );


        $values = array (
            'business' => $this->getEmail(),
            'cmd' => '_xclick',
            'item_name' => $itemTitle,
            'currency_code' => $currency,
            'amount' => $itemPrice,
            'custom' => json_encode($privateData),
            'return' => $returnUrl,
            'notify_url' => str_replace('&amp;', '&', $site->generateUrl(null, null, array(), array('g' => 'shop', 'm'=>'simple_checkout', 'a'=>'paypalCallback')))
        );

        foreach ($values as $valueKey => $value) {
            $field = new \Modules\developer\form\Field\Hidden(
                array(
                    'name' => $valueKey,
                    'defaultValue' => $value
                ));
            $form->addField($field);
        }


        //Submit button

        if ($parametersMod->getValue('shop', 'simple_checkout', 'options', 'paypal_image_url')) {
            $imageSrc = $parametersMod->getValue('shop', 'simple_checkout', 'options', 'paypal_image_url');
        } else {
            $imageSrc = BASE_URL.PLUGIN_DIR."shop/simple_checkout/public/payment_methods/paypal.gif";
        }


        $field = new \Modules\shop\simple_checkout\Lib\FieldSubmitImage(
            array(
                'name' => 'submit',
                'imageSrc' => $imageSrc
            ));
        $form->addField($field);



        return $form->render();
    }

    /**
     *
     *  Returns $data encoded in UTF8. Very useful before json_encode as it fails if some strings are not utf8 encoded
     * @param mixed $dat array or string
     * @return array
     */
    private function checkEncoding($dat)
    {
        if (is_string($dat)) {
            if (mb_check_encoding($dat, 'UTF-8')) {
                return $dat;
            } else {
                return utf8_encode($dat);
            }
        }
        if (is_array($dat)) {
            $answer = array();
            foreach ($dat as $i => $d) {
                $answer[$i] = $this->checkEncoding($d);
            }
            return $answer;
        }
        return $dat;
    }


    public function getActive()
    {
        global $parametersMod;
        return $parametersMod->getValue('shop', 'simple_checkout', 'options', 'paypal_active');
    }

    public function getEmail()
    {
        global $parametersMod;
        return $parametersMod->getValue('shop', 'simple_checkout', 'options', 'paypal_email');
    }


    public function getPayPalUrl()
    {
        if (DEVELOPMENT_ENVIRONMENT) {
            return self::PAYPAL_POST_URL_TEST;
        } else {
            return self::PAYPAL_POST_URL;
        }
    }

    public function correctConfiguration()
    {
        if ($this->getActive() && $this->getEmail()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}