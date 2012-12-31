<?php

namespace Modules\shop\simple_checkout;

if (!defined('BACKEND')) exit;  //this file can't be accessed directly

global $site;

require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/std_mod.php'); //include standard module to manage data records


class AreaItem extends \Modules\developer\std_mod\Area{  //extending standard data management module area

    function __construct(){
        global $parametersMod;  //global object to get parameters

        parent::__construct(
        array(
      'dbTable' => 'm_custom_payment',  //table of data we need to manage
      'title' => 'Subscriptions',
      'dbPrimaryKey' => 'userId',  //Primary key of that table
      'searchable' => true,  //User will have search button or not
      'orderBy' => 'paidUntil',  //Database field, by which the records should be ordered by default
      'orderDirection' => 'desc',
      'sortable' => false,  //Does user have a right to change the order of records
      'allowInsert' => true,
      'allowUpdate' => true,
      'allowDelete' => true,
      'searchable' => true,
        )
        );









        $element = new \Modules\developer\std_mod\ElementNumber(  //text field
        array(
    'title' => 'UserId', //Field name
    'dbField' => 'userId',
    'showOnList' => true,  //Show field value in list of all records
    'searchable' => true,  //Allow to search by this field
    'disabledOnUpdate' => true
        )
        );
        $this->addElement($element);




        $element = new \Modules\developer\std_mod\ElementDateTime(  //text field
        array(
    'title' => 'Paid Until', //Field name
    'dbField' => 'paidUntil',
    'showOnList' => true,  //Show field value in list of all records
    'searchable' => true,  //Allow to search by this field
    'type' => 'unix'
    )
    );
    $this->addElement($element);






    }
}


