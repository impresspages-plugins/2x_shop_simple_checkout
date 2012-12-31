<?php

namespace Modules\community\testimonial\widget;

if (!defined('CMS')) exit;


class IpTestimonial extends \Modules\standard\content_management\Widget {

    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('community', 'testimonial', 'testimonial_translation', 'module_title');
    }
    
    public function managementHtml($instanceId, $data, $layout) {
        require_once(BASE_DIR.PLUGIN_DIR.'community/testimonial/model.php');
        $randomTestimony = \Modules\community\testimonial\Model::getRandomTestimony();

        $data = array (
        	'author' => $randomTestimony['author'],
            'comment' => $randomTestimony['comment'],
            'extras' => $randomTestimony['extra_details']
        );
        $response = \Ip\View::create(BASE_DIR.PLUGIN_DIR.$this->moduleGroup.'/'.$this->moduleName.'/widget/'.$this->name.'/'.self::MANAGEMENT_DIR.'/default.php', $data)->render();

        return $response; 
    }

    public function previewHtml($instanceId, $data, $layout) {
    	require_once(BASE_DIR.PLUGIN_DIR.'community/testimonial/model.php');
        $randomTestimony = \Modules\community\testimonial\Model::getRandomTestimony();

        $data = array (
        	'author' => $randomTestimony['author'],
            'comment' => $randomTestimony['comment'],
            'extras' => $randomTestimony['extra_details']
        );
        $response = \Ip\View::create(BASE_DIR.PLUGIN_DIR.$this->moduleGroup.'/'.$this->moduleName.'/widget/'.$this->name.'/'.self::PREVIEW_DIR.'/'.$layout.'.php', $data)->render();

        return $response;
    }
    
}