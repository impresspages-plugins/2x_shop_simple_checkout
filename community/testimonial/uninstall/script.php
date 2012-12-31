<?php             
             
namespace Modules\community\testimonial;             
             
if (!defined('CMS')) exit; //this file can't bee accessed directly             
             
class Uninstall{             
             
  public function execute(){             
             
    $sql = "DROP TABLE `".DB_PREF."m_community_testimonial` ";             
                 
    $rs = mysql_query($sql);             
                 
    if(!$rs){              
      trigger_error($sql." ".mysql_error());             
    }             
                 
  }             
}     