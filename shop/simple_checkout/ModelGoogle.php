<?php
    /**
     * @package   ImpressPages
     * @copyright Copyright (C) 2012 JSC Apro Media.
     * @license   GNU/GPL, see ip_license.html
     */

namespace Modules\shop\simple_checkout;


require_once(__DIR__.'/Lib/google/googleresponse.php');
require_once(__DIR__.'/Lib/google/googlemerchantcalculations.php');
require_once(__DIR__.'/Lib/google/googleresult.php');
require_once(__DIR__.'/Lib/google/googlerequest.php');

require_once(__DIR__.'/Lib/google/googlecart.php');
require_once(__DIR__.'/Lib/google/googleitem.php');
require_once(__DIR__.'/Lib/google/googleshipping.php');
require_once(__DIR__.'/Lib/google/googletax.php');

class ModelGoogle
{


    protected static $instance;

    protected function __construct() {}

    protected function __clone(){}

    /**
     * Get singleton instance
     * @return ModelGoogle
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new ModelGoogle();
        }

        return self::$instance;
    }



    public function processGoogleCallback()
    {
        global $log;
        $log->log('shop/simple_checkout', 'googleCallback', json_encode($_POST));


//        define('RESPONSE_HANDLER_ERROR_LOG_FILE', 'googleerror.log');
//        define('RESPONSE_HANDLER_LOG_FILE', 'googlemessage.log');

        $merchant_id = $this->getMerchantId();  // Your Merchant ID
        $merchant_key = $this->getMerchantKey();  // Your Merchant Key
        if ($this->isInSandboxMode()) {
            $server_type = "sandbox";  // change this to go live
        } else {
            $server_type = "production";  // change this to go live
        }
        $currency = 'USD';  // seGooglet to GBP if in the UK
        $certificate_path = ""; // set your SSL CA cert path

        $Gresponse = new \GoogleResponse($merchant_id, $merchant_key);

        $Grequest = new \GoogleRequest($merchant_id, $merchant_key, $server_type, $currency);
        $Grequest->SetCertificatePath($certificate_path);

        //Setup the log file
//        $Gresponse->SetLogFiles(RESPONSE_HANDLER_ERROR_LOG_FILE,
//            RESPONSE_HANDLER_LOG_FILE, L_ALL);

        // Retrieve the XML sent in the HTTP POST request to the ResponseHandler
        if (isset($_REQUEST['serial-number'])) {
            $xml_response = $this->requestHistory($_REQUEST['serial-number']);
        } else {
            $xml_response = isset($HTTP_RAW_POST_DATA)?
                $HTTP_RAW_POST_DATA:file_get_contents("php://input");
            if (get_magic_quotes_gpc()) {
                $xml_response = stripslashes($xml_response);
            }
        }
        $log->log('shop/simple_checkout', 'googleCallbackXML', $xml_response);
        list($root, $data) = $Gresponse->GetParsedXML($xml_response);
        $Gresponse->SetMerchantAuthentication($merchant_id, $merchant_key);

        /*$status = $Gresponse->HttpAuthentication();
        if(! $status) {
          die('authentication failed');
        }*/

        /* Commands to send the various order processing APIs
         * Send charge order : $Grequest->SendChargeOrder($data[$root]
         *    ['google-order-number']['VALUE'], <amount>);
         * Send process order : $Grequest->SendProcessOrder($data[$root]
         *    ['google-order-number']['VALUE']);
         * Send deliver order: $Grequest->SendDeliverOrder($data[$root]
         *    ['google-order-number']['VALUE'], <carrier>, <tracking-number>,
         *    <send_mail>);
         * Send archive order: $Grequest->SendArchiveOrder($data[$root]
         *    ['google-order-number']['VALUE']);
         *
         */

        switch ($root) {
            case "request-received": {
                break;
            }
            case "error": {
                break;
            }
            case "diagnosis": {
                break;
            }
            case "checkout-redirect": {
                break;
            }
            case "merchant-calculation-callback": {
                break;
            }
            case "new-order-notification": {
                $Gresponse->SendAck();
                break;
            }
            case "order-state-change-notification": {
                $Gresponse->SendAck(null, FALSE);
                $new_financial_state = $data[$root]['new-financial-order-state']['VALUE'];
                $new_fulfillment_order = $data[$root]['new-fulfillment-order-state']['VALUE'];
                switch($new_financial_state) {
                    case 'REVIEWING': {
                        break;
                    }
                    case 'CHARGEABLE': {
                        break;
                    }
                    case 'CHARGING': {
                        break;
                    }
                    case 'CHARGED': {
                        break;
                    }
                    case 'PAYMENT_DECLINED': {
                        break;
                    }
                    case 'CANCELLED': {
                        break;
                    }
                    case 'CANCELLED_BY_GOOGLE': {
                        //$Grequest->SendBuyerMessage($data[$root]['google-order-number']['VALUE'],
                        //    "Sorry, your order is cancelled by Google", true);
                        break;
                    }
                    default:
                        break;
                }

                switch($new_fulfillment_order) {
                    case 'NEW': {
                        break;
                    }
                    case 'PROCESSING': {
                        break;
                    }
                    case 'DELIVERED': {
                        break;
                    }
                    case 'WILL_NOT_DELIVER': {
                        break;
                    }
                    default:
                        break;
                }
                break;
            }
            case "charge-amount-notification": {
                global $dispatcher;



                $orderXML = $this->requestOrder($data[$root]['google-order-number']['VALUE']);
                list($orderRoot, $order) = $Gresponse->GetParsedXML($orderXML);


                if (isset($order[$orderRoot]['notifications']['new-order-notification'])) {
                    $orderData = $order[$orderRoot]['notifications']['new-order-notification'];
                }


                if (isset($orderData['shopping-cart'])) {
                    $cartData = $orderData['shopping-cart'];
                }

                if (isset($cartData['merchant-private-data'])) {
                    $merchantData = $cartData['merchant-private-data'];
                }

                $productId = isset($merchantData['productId']['VALUE']) ? $merchantData['productId']['VALUE'] : null;
                $userId = isset($merchantData['userId']['VALUE']) ? $merchantData['userId']['VALUE'] : null;
                $widgetInstanceId = isset($merchantData['widgetInstanceId']['VALUE']) ? $merchantData['widgetInstanceId']['VALUE'] : null;
                $currency = isset($orderData['order-total']['currency']) ? $orderData['order-total']['currency'] : null;
                $price = isset($orderData['order-total']['VALUE']) ? $orderData['order-total']['VALUE'] : null;
                $buyerEmail = isset($orderData['buyer-billing-address']['email']['VALUE']) ? $orderData['buyer-billing-address']['email']['VALUE'] : null;

                $widgetObject = \Modules\standard\content_management\Model::getWidgetObject('IpSimpleCheckout');
                $validOrder = $widgetObject->checkOrder($widgetInstanceId, $currency, $price, $productId);

                if ($validOrder || true) {
                    $completedOrderEvent = new  EventNewOrder($this, $buyerEmail, $price, $currency, $widgetInstanceId, $productId, $userId);
                    $dispatcher->notify($completedOrderEvent);
                } else {
                    //something went wrong. Notification values doesn't match widget values. Possible hack. Ignore.
                }

                //$Grequest->SendDeliverOrder($data[$root]['google-order-number']['VALUE'],
                //    <carrier>, <tracking-number>, <send-email>);
                //$Grequest->SendArchiveOrder($data[$root]['google-order-number']['VALUE'] );
                $Gresponse->SendAck();
                break;
            }
            case "chargeback-amount-notification": {
                $Gresponse->SendAck();
                break;
            }
            case "refund-amount-notification": {
                $Gresponse->SendAck();
                break;
            }
            case "risk-information-notification": {
                $Gresponse->SendAck();
                break;
            }
            default:
                $Gresponse->SendBadRequestStatus("Invalid or not supported Message");
                break;
        }

    }

    /* GET */

    public function getActive()
    {
        global $parametersMod;
        return $parametersMod->getValue('shop', 'simple_checkout', 'options', 'google_active');
    }

    public function getMerchantId()
    {
        global $parametersMod;
        return $parametersMod->getValue('shop', 'simple_checkout', 'options', 'google_merchant_id');
    }

    public function getMerchantKey()
    {
        global $parametersMod;
        return $parametersMod->getValue('shop', 'simple_checkout', 'options', 'google_merchant_key');
    }

    public function isInSandboxMode()
    {
        return DEVELOPMENT_ENVIRONMENT;
    }

    /* SET */

    public function setActive($active)
    {
        global $parametersMod;
        return $parametersMod->setValue('shop', 'simple_checkout', 'options', 'google_active', $active);
    }

    public function setMerchantId($merchantId)
    {
        global $parametersMod;
        $merchantId = trim($merchantId);
        $parametersMod->setValue('shop', 'simple_checkout', 'options', 'google_merchant_id', $merchantId);
    }

    public function setMerchantKey($merchantKey)
    {
        global $parametersMod;
        $merchantKey = trim($merchantKey);
        return $parametersMod->setValue('shop', 'simple_checkout', 'options', 'google_merchant_key', $merchantKey);
    }


    /* PRIVATE MODEL METHODS */


    /* In case the XML API contains multiple open tags
       with the same value, then invoke this function and
       perform a foreach on the resultant array.
       This takes care of cases when there is only one unique tag
       or multiple tags.
       Examples of this are "anonymous-address", "merchant-code-string"
       from the merchant-calculations-callback API
    */
    private function getArrResult($child_node) {
        $result = array();
        if(isset($child_node)) {
            if($this->isAssociativeArray($child_node)) {
                $result[] = $child_node;
            }
            else {
                foreach($child_node as $curr_node){
                    $result[] = $curr_node;
                }
            }
        }
        return $result;
    }

    /* Returns true if a given variable represents an associative array */
    private function isAssociativeArray( $var ) {
        return is_array( $var ) && !is_numeric( implode( '', array_keys( $var ) ) );
    }

    private function requestHistory($serialNumber)
    {


        $xml = '<notification-history-request xmlns="http://checkout.google.com/schema/2">
  <serial-number>'.$serialNumber.'</serial-number>
</notification-history-request>';

        return $this->sendRequest($xml);
    }


    private function requestOrder($orderNumber) {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<notification-history-request xmlns="http://checkout.google.com/schema/2">
    <order-numbers>
        <google-order-number>'.$orderNumber.'</google-order-number>
    </order-numbers>
    <notification-types>
        <notification-type>new-order</notification-type>
    </notification-types>
</notification-history-request>';
        return $this->sendRequest($xml);
    }

    private function sendRequest($xml)
    {
        if ($this->isInSandboxMode()) {
            $requestUrl = 'https://sandbox.google.com/checkout/api/checkout/v2/reports/Merchant/'.$this->getMerchantId();
        } else {
            $requestUrl = 'https://checkout.google.com/api/checkout/v2/reports/Merchant/'.$this->getMerchantId();
        }



        // Get the curl session object
        $session = curl_init($requestUrl);
        // Set the POST options.
        curl_setopt($session, CURLOPT_USERPWD, $this->getMerchantId() . ":" . $this->getMerchantKey());
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($session, CURLOPT_HEADER, true);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);


        // Do the POST and then close the session
        $response = curl_exec($session);
        if (curl_errno($session)) {
            trigger_error(curl_error($session));
        } else {
            curl_close($session);
        }

        $bodyXML = $this->getBodyX($response);
        return $bodyXML;
    }

    /**
     * @access private
     */
    private function getBodyX($heads){
        $fp = explode(DOUBLE_ENTER, $heads, 2);
        return $fp[1];
    }




    public function getGoogleCheckoutButton($widgetInstanceId, $productId, $userId, $itemTitle, $itemPrice, $currency, $returnUrl)
    {
        global $site;
        global $parametersMod;


        if ($this->isInSandboxMode()) {
            $serverType = "sandbox";
        } else {
            $serverType = "production";
        }



        $cart = new \GoogleCart($this->getMerchantId(), $this->getMerchantKey(), $serverType, $currency);
        $totalCount = 1;

        $item_1 = new \GoogleItem($itemTitle,      // Item name
            $itemTitle, // Item      description
            $totalCount, // Quantity
            $itemPrice); // Unit price
        $cart->AddItem($item_1);

        //    // Add shipping options
        //    if($total_count < 3){
        //        $ship_1 = new GoogleFlatRateShipping("USPS Priority Mail", 4.55);
        //    }else{
        //        $ship_1 = new GoogleFlatRateShipping("USPS Priority Mail", 6.2);
        //    }
        //    $Gfilter = new GoogleShippingFilters();
        //    $Gfilter->SetAllowedCountryArea('CONTINENTAL_48');
        //
        //    $ship_1->AddShippingRestrictions($Gfilter);
        //
        //    $cart->AddShipping($ship_1);

        //    // Add tax rules
        //    $tax_rule = new GoogleDefaultTaxRule(0.05);
        //    $tax_rule->SetStateAreas(array("MA"));
        //    $cart->AddDefaultTaxRules($tax_rule);

        //    // Specify <edit-cart-url>
        //    $cart->SetEditCartUrl("https://www.example.com/cart/");

        // Specify "Return to xyz" link
        $cart->SetContinueShoppingUrl($returnUrl);

        // Request buyer's phone number
        $cart->SetRequestBuyerPhone(true);

        $privateData = new \MerchantPrivateData(array (
            'widgetInstanceId' => $widgetInstanceId,
            'productId' => $productId,
            'userId' => $userId ? $userId : ''
        ));

        $cart->SetMerchantPrivateData($privateData);

        // Display Google Checkout button
        if ($parametersMod->getValue('shop', 'simple_checkout', 'options', 'google_image_url')) {
            $buttonSrc = $parametersMod->getValue('shop', 'simple_checkout', 'options', 'google_image_url');
        } else {
            $buttonSrc = BASE_URL.PLUGIN_DIR."shop/simple_checkout/public/payment_methods/google.gif";
        }

        $button = "<div align=\"center\"><form method=\"post\" action=\"".
            $cart->checkout_url . "\"" . ($cart->googleAnalytics_id?
            " onsubmit=\"setUrchinInputCode();\"":"") . ">
                <input type=\"hidden\" name=\"cart\" value=\"".
            base64_encode($cart->GetXML()) ."\">
                <input type=\"hidden\" name=\"signature\" value=\"".
            base64_encode($cart->CalcHmacSha1($cart->GetXML())). "\">
                <input type=\"image\" name=\"Checkout\" alt=\"Checkout\"
                src=\"".$buttonSrc."\" />
                </form></div>";


        return $button;

    }

    public function correctConfiguration()
    {
        if ($this->getActive() && $this->getMerchantId() != '' && $this->getMerchantKey() != '') {
            return TRUE;
        } else {
            return FALSE;
        }
    }



}