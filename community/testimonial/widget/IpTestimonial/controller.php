<?php

namespace Modules\community\testimonial\widget;

if (!defined('CMS')) exit;


class IpTestimonial extends \Modules\standard\content_management\Widget {

    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('community', 'testimonial', 'testimonial_translation', 'module_title');
    }
    
    public function managementHtml($instanceId, $data, $layout) {
        

        

       
    }

    public function previewHtml($instanceId, $data, $layout) {
        

        
    }
    
}