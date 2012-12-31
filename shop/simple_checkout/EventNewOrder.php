<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\shop\simple_checkout;



class EventNewOrder extends \Ip\Event{

    const EVENT_NEW_ORDER = 'simpleCheckout.newOrder';


    public function __construct($object, $buyerEmail, $price, $currency, $widgetInstanceId, $productId, $userId) {


        $eventData = array(
            'buyerEmail' => $buyerEmail,
            'price' => $price,
            'currency' => $currency,
            'widgetInstanceId' => $widgetInstanceId,
            'productId' => $productId,
            'userId' => $userId
        );

        return parent::__construct($object, self::EVENT_NEW_ORDER, $eventData);
    }

    public function getBuyerEmail()
    {
        return $this->getValue('buyerEmail');
    }

    public function getWidgetInstanceId()
    {
        return $this->getValue('widgetInstanceId');
    }

    public function getProductId()
    {
        return $this->getValue('productId');
    }

    public function getUserId()
    {
        return $this->getValue('userId');

    }

    public function getPrice()
    {
        return $this->getValue('price');

    }

    public function getCurrency()
    {
        return $this->getValue('currency');

    }

}