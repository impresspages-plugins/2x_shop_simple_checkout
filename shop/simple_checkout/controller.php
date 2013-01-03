<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 JSC Apro Media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Modules\shop\simple_checkout;




class Controller extends \Ip\Controller
{



    /**
     * Handle notifications from Google Checkout
     */
    public function googleCallback()
    {
        global $site;
        $modelGoogle = \Modules\shop\simple_checkout\ModelGoogle::instance();
        $modelGoogle->processGoogleCallback();

        $site->setOutput('');
    }

    /**
     *
     * Handle notifications from PayPal
     */
    public function paypalCallback () {
        global $site;
        global $log;
        $modelPayPal = \Modules\shop\simple_checkout\ModelPayPal::instance();
        $modelPayPal->processPayPalCallback();
        $site->setOutput('');
    }

    public function requireLogin()
    {
        global $site;
        $userZone = $site->getZoneByModule('community', 'user');
        if (!$userZone) {
            trigger_error("community/user zone doesn't exist. Please create one in Developer -> Zones");
        }

        if (!isset($_REQUEST['method'])) {
            $this->redirect(BASE_URL); //die silently
        }
        $method = $_REQUEST['method'];

        if (!isset($_REQUEST['widgetInstanceId'])) {
            $this->redirect(BASE_URL); //die silently
        }
        $instanceId = $_REQUEST['widgetInstanceId'];

        $params = array(
            'g' => 'shop',
            'm' => 'simple_checkout',
            'a' => 'proceedAfterLogin',
            'method' => $method,
            'widgetInstanceId' => $instanceId
        );
        $proceedUrl = $site->generateUrl(null, null, null, $params);
        $_SESSION['modules']['community']['user']['page_after_login'] = str_replace('&amp;', '&', $proceedUrl);

        $this->redirect($userZone->getLinkLogin());
    }

    public function proceedAfterLogin()
    {
        global $site;
        if (!isset($_REQUEST['method'])) {
            $this->redirect(BASE_URL); //die silently
        }
        $method = $_REQUEST['method'];

        if (!isset($_REQUEST['widgetInstanceId'])) {
            $this->redirect(BASE_URL); //die silently
        }
        $widgetInstanceId = $_REQUEST['widgetInstanceId'];


        $widgetRecord = \Modules\standard\content_management\Model::getWidgetFullRecord($widgetInstanceId);
        if(!$widgetRecord) {
            $this->redirect(BASE_URL); //die silently
        }
        $data = $widgetRecord['data'];

        $widgetObject = \Modules\standard\content_management\Model::getWidgetObject('IpSimpleCheckout');


        if ($method == 'google') {
            $modelGoogle = \Modules\shop\simple_checkout\ModelGoogle::instance();
            $checkoutButton = $widgetObject->getGoogleCheckoutButton($widgetInstanceId, $data);
            $autoCheckoutPage = \Ip\View::create('view/proceedCheckoutLayout.php', array('checkoutButton' => $checkoutButton))->render();

            $site->setOutput($autoCheckoutPage);
        }
    }

//    public function test()
//    {
//        global $dispatcher;
//
//
//        $modelOrder = ModelOrder::instance();
//        $duplicate = $modelOrder->paymentExists(ModelOrder::METHOD_GOOGLE, 1);
//
//
//        $completedOrderEvent = new  EventNewOrder($this, $buyerEmail = 'test@test.lt', '129.29', 'usd', '1004', '125', '10', 'google', 1, true);
//        $dispatcher->notify($completedOrderEvent);
//
//    }

}