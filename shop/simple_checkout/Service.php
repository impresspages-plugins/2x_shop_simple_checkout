<?php
    /**
     * @package ImpressPages
     * @copyright   Copyright (C) 2011 ImpressPages LTD.
     * @license see ip_license.html
     */
namespace Modules\shop\simple_checkout;



class Service {
    protected static $instance;

    protected function __construct() {}

    protected function __clone(){}

    /**
     * Get singleton instance
     * @return Service
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new Service();
        }

        return self::$instance;
    }


    public function insertOrder($price, $currency, $userId, $email, $test, $paymentMethod, $paymentId, $comment = null)
    {
        $modelOrder = ModelOrder::instance();
        $created = time();
        $modelOrder->insetOrder($price, $currency, $userId, $email, $test, $created, $paymentMethod, $paymentId, $comment);
    }

}
