<?php
/**
 * @package     ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license     GNU/GPL, see ip_license.html
 */

namespace Modules\shop\simple_checkout;


require_once(BASE_DIR.MODULE_DIR.'developer/zones/manager.php');

class Install
{

    public function execute()
    {
        global $site;

        $this->createOrderTable();

        $userZone = $site->getZoneByModule('community', 'user');

        if (!$userZone) {
            $this->createUserZone('User');
        }

    }

    private function createOrderTable()
    {

        $sql = "
CREATE TABLE IF NOT EXISTS `".DB_PREF."m_shop_simple_checkout_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `price` int(11) NOT NULL COMMENT 'in cents',
  `currency` varchar(3) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `created` int(11) NOT NULL COMMENT 'unix timestamp when record has been created',
  `comment` int(11) DEFAULT NULL COMMENT 'add any comment about this order',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
        ";
        $dbh = \Ip\Db::getConnection();
        $q = $dbh->prepare($sql);
        $q->execute();

    }

    private function createUserZone($title)
    {

        global $site;
        $name = preg_replace("/[^a-zA-Z0-9\s]/", "", $title);
        $name = strtolower($name);
        if ($name == '') {
            $name = 'news';
        }

        $i = null;
        while ($site->getZone($name . $i)) {
            $i++;
        }

        $name = $name . $i;

        $sql = "
        INSERT INTO `" . DB_PREF . "zone` SET
        `row_number` = '" . (int)$this->newRowNumber() . "',
        `name` = '" . mysql_real_escape_string($name) . "',
        `template` = '" . mysql_real_escape_string($this->getTemplate()) . "',
        `translation` = '" . mysql_real_escape_string($title) . "',
        `associated_group` = 'community',
        `associated_module` = 'user'
        ";

        $rs = mysql_query($sql);
        $zoneId = mysql_insert_id();
        if ($rs) {
            $zonesModule = new \Modules\developer\zones\ZonesArea();
            $zonesModule->after_insert($zoneId);
        } else {
            trigger_error($sql . " " . mysql_error());
        }
    }

    private function newRowNumber()
    {
        $sql = "
        SELECT max(row_number) as max_row_number FROM `" . DB_PREF . "zone` WHERE 1";

        $rs = mysql_query($sql);
        if ($rs) {
            if ($lock = mysql_fetch_assoc($rs)) {
                return $lock['max_row_number'] + 1;
            } else {
                return false;
            }
        } else {
            trigger_error($sql . " " . mysql_error());
            return false;
        }


    }

    private function getTemplate() {
        global $site;
        $contentManagementZone = $site->getZoneByModule('standard', 'content_management');
        if ($contentManagementZone) {
            return $contentManagementZone->getLayout();
        }

        require_once(BASE_DIR.MODULE_DIR.'developer/zones/db.php');
        $db = new \Modules\developer\zones\Db();

        return $db->getDefaultTemplate();
        if (count($templates)) {
            return array_pop($templates);
        }

        return '';
    }

}