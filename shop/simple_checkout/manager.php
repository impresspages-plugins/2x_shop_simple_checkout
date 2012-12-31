<?php
namespace Modules\shop\simple_checkout;




class Manager {
    var $standardModule;

    public function __construct() {
        global $parametersMod;

        $areaItem = new AreaItem();



        $this->standardModule = new \Modules\developer\std_mod\StandardModule($areaItem); //create management tool
    }

    public function manage() {
        return $this->standardModule->manage();  //return management tools
    }


}