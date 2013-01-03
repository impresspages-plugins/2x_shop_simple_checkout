<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\shop\simple_checkout;



class System{

    function init(){
        global $site;
        global $dispatcher;

        $site->addCss(BASE_URL.PLUGIN_DIR.'shop/simple_checkout/public/simpleCheckout.css');

        $dispatcher->bind(\Modules\shop\simple_checkout\EventNewOrder::EVENT_NEW_ORDER, array($this, 'processOrder'));


    }

    public function processOrder(EventNewOrder $event)
    {   global $log;

        $data = $event->getValues();
        $log->log('shop/simple_checkout', 'New Order', json_encode($data));

        $service = Service::instance();
        $service->insertOrder($event->getPrice(), $event->getCurrency(), $event->getUserId(), $event->getBuyerEmail(), $event->getTest(), $event->getPaymentMethod(), $event->getPaymentId(), null);
    }


}


