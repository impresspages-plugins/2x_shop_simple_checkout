<?php

namespace Modules\community\testimonial;                
                
if (!defined('BACKEND')) exit;             
                
require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/std_mod.php');        
                
class ItemsArea extends \Modules\developer\std_mod\Area {           
                
  function __construct(){                 
    global $parametersMod;             
                
    parent::__construct(          
      array(                
        'dbTable' => 'm_community_testimonial',             
        'title' => $parametersMod->getValue('community', 'testimonial', 'testimonial_translation', 'module_title'),                
        'dbPrimaryKey' => 'id',              
        'searchable' => true,              
        'orderBy' => 'row_number',             
        'sortable' => true,               
        'sortField' => 'row_number'              
      )                    
    );                
    
    //Author               
    $element = new \Modules\developer\std_mod\ElementText( //Text field                
      array(                     
        'title' => $parametersMod->getValue('community', 'testimonial', 'testimonial_translation', 'author_field_name'),              
        'showOnList' => true,                 
        'dbField' => 'author',               
        'searchable' => true, 
        'required' => true              
      )                
    );                
    $this->addElement($element);                    
                
    //Comment                 
    $element = new \Modules\developer\std_mod\ElementTextarea( //TextArea field                 
      array(                
        'title' => $parametersMod->getValue('community', 'testimonial', 'testimonial_translation', 'comment_field_name'),               
        'showOnList' => true,                
        'dbField' => 'comment',
        'required' => true           
      )                
    );                
    $this->addElement($element); 

    //Extra Details                 
    $element = new \Modules\developer\std_mod\ElementText( //Text field                 
      array(                
        'title' => $parametersMod->getValue('community', 'testimonial', 'testimonial_translation', 'extras_field_name'),               
        'showOnList' => true,                
        'dbField' => 'extra_details',
        'required' => false           
      )                
    );                
    $this->addElement($element);                        
          
    //Visibility
    $element = new \Modules\developer\std_mod\ElementBool(  //text field
      array(
        'title' => $parametersMod->getValue('community', 'testimonial', 'testimonial_translation', 'visibility_field_name'),
        'showOnList' => true,
        'dbField' => 'visibility',
        'required' => false,
        'searchable' => false,
        'defaultValue' => 1
      )
    );
    $this->addElement($element);                
  
  }                             
}