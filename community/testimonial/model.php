<?php
/**
 * @package ImpressPages
 */
namespace Modules\community\testimonial;

if (!defined('CMS')) exit;



class Model{
    public static function getRandomTestimony() {
        $sql = "
            SELECT * FROM `".DB_PREF."m_community_testimonial` 
            WHERE `visibility` = 1
            ORDER BY rand() LIMIT 1
        ";
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t select data '.$sql.' '.mysql_error());
        }

        if ($testimony = mysql_fetch_assoc($rs)) {
            return $testimony;
        }

        return false;

    }
    
}