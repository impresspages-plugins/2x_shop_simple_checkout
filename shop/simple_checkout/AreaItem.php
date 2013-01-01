<?php

namespace Modules\shop\simple_checkout;

if (!defined('BACKEND')) exit; //this file can't be accessed directly

global $site;

require_once(BASE_DIR . MODULE_DIR . 'developer/std_mod/std_mod.php'); //include standard module to manage data records


class AreaItem extends \Modules\developer\std_mod\Area
{ //extending standard data management module area

    function __construct()
    {
        global $parametersMod; //global object to get parameters

        parent::__construct(
            array(
                'dbTable' => 'm_shop_simple_checkout_order', //table of data we need to manage
                'title' => 'Orders',
                'dbPrimaryKey' => 'id', //Primary key of that table
                'searchable' => true, //User will have search button or not
                'orderBy' => 'id', //Database field, by which the records should be ordered by default
                'orderDirection' => 'desc',
                'sortable' => false, //Does user have a right to change the order of records
                'allowInsert' => true,
                'allowUpdate' => true,
                'allowDelete' => true,
                'searchable' => true,
            )
        );


        $element = new \Modules\developer\std_mod\ElementNumber( //text field
            array(
                'title' => 'Price (in cents)', //Field name
                'dbField' => 'price',
                'showOnList' => true, //Show field value in list of all records
                'searchable' => true, //Allow to search by this field
            )
        );
        $this->addElement($element);


        $element = new \Modules\developer\std_mod\ElementText( //text field
            array(
                'title' => 'Currency', //Field name
                'dbField' => 'currency',
                'showOnList' => true, //Show field value in list of all records
                'searchable' => true, //Allow to search by this field
            )
        );
        $this->addElement($element);

        $element = new \Modules\developer\std_mod\ElementNumber( //text field
            array(
                'title' => 'User Id', //Field name
                'dbField' => 'userId',
                'showOnList' => true, //Show field value in list of all records
                'searchable' => true, //Allow to search by this field
            )
        );
        $this->addElement($element);



        $element = new \Modules\developer\std_mod\ElementText( //text field
            array(
                'title' => 'Email', //Field name
                'dbField' => 'email',
                'showOnList' => true, //Show field value in list of all records
                'searchable' => true, //Allow to search by this field
            )
        );
        $this->addElement($element);


        $element = new \Modules\developer\std_mod\ElementDateTime( //text field
            array(
                'title' => 'Created', //Field name
                'dbField' => 'created',
                'showOnList' => true, //Show field value in list of all records
                'searchable' => true, //Allow to search by this field
                'type' => 'unix'
            )
        );
        $this->addElement($element);


        $element = new \Modules\developer\std_mod\ElementTextarea( //text field
            array(
                'title' => 'Comment', //Field name
                'dbField' => 'comment',
                'showOnList' => true, //Show field value in list of all records
                'searchable' => true, //Allow to search by this field
            )
        );
        $this->addElement($element);


    }
}


