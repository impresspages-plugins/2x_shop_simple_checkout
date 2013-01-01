<?php
    /**
     * @package ImpressPages
     * @copyright   Copyright (C) 2011 ImpressPages LTD.
     * @license see ip_license.html
     */
namespace Modules\shop\simple_checkout;



class ModelOrder {
    protected static $instance;

    protected function __construct() {}

    protected function __clone(){}

    /**
     * Get singleton instance
     * @return ModelOrder
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new ModelOrder();
        }

        return self::$instance;
    }

    public function insetOrder($price, $currency, $userId, $email, $created, $comment)
    {
        if (is_string($price)) {
            $price = (float) $price;
            $price = round($price*100);
        }
        $currency = strtoupper($currency);
        $dbh = \Ip\Db::getConnection();
        $sql = '
                INSERT INTO
                    `'.DB_PREF.'m_shop_simple_checkout_order`
                SET
                    `price` = :price,
                    `currency` = :currency,
                    `userId` = :userId,
                    `email` = :email,
                    `created` = :created,
                    `comment` = :comment
            ';

        $params = array (
            ':price' => $price,
            ':currency' => $currency,
            ':userId' => $userId,
            ':email' => $email,
            ':created' => $created,
            ':comment' => $comment
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
    }

}