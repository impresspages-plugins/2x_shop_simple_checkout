<?php

namespace Modules\community\testimonial;

if (!defined('CMS')) exit;

class System{

    function init(){
        global $site; 

            $site->addCss(BASE_URL.PLUGIN_DIR.'community/testimonial/public/ip_testimonial.css');

        }

    }