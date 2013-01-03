<?php
    /**
     * @package ImpressPages
     * @copyright   Copyright (C) 2011 ImpressPages LTD.
     * @license see ip_license.html
     */
namespace Modules\shop\simple_checkout;



class ModelOrder {

    const METHOD_GOOGLE = 'google';
    const METHOD_PAYPAL = 'paypal';

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

    public function insetOrder($price, $currency, $userId, $email, $test, $created, $paymentMethod, $paymentId, $comment)
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
                    `test` = :test,
                    `payment_method` = :paymentMethod,
                    `payment_id` = :paymentId,
                    `created` = :created,
                    `comment` = :comment
            ';

        $params = array (
            ':price' => $price,
            ':currency' => $currency,
            ':userId' => $userId,
            ':email' => $email,
            ':test' => $test,
            ':paymentMethod' => $paymentMethod,
            ':paymentId' => $paymentId,
            ':created' => $created,
            ':comment' => $comment
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
    }

    public function paymentExists($paymentMethod, $paymentId)
    {
        $dbh = \Ip\Db::getConnection();
        $sql = '
            SELECT
                *
            FROM
                `'.DB_PREF.'m_shop_simple_checkout_order`
            WHERE
                `payment_method` = :paymentMethod AND
                `payment_id` = :paymentId
        ';

        $params = array (
            ':paymentMethod' => $paymentMethod,
            ':paymentId' => $paymentId
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
        if ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            return true;
        } else {
            return false;
        }
    }

}