<?php
/**
 * @package     ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license     GNU/GPL, see ip_license.html
 */

namespace Modules\shop\simple_checkout;



class Uninstall
{

    public function execute()
    {
        $this->dropOrderTable();
    }

    private function dropOrderTable()
    {

        $sql = "
DROP TABLE IF EXISTS `".DB_PREF."m_shop_simple_checkout_order`
        ";
        $dbh = \Ip\Db::getConnection();
        $q = $dbh->prepare($sql);
        $q->execute();

    }

}