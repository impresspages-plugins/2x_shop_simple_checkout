<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\shop\simple_checkout\Lib;


class FieldSubmitImage extends \Modules\developer\form\Field\Field{

    public function __construct($options)
    {
        if (!empty($options['imageSrc'])) {
            $this->setImageSrc($options['imageSrc']);
        }

        parent::__construct($options);
    }

    public function render($doctype) {
        return '<input type="image" src="'.$this->getImageSrc().'" '.$this->getAttributesStr($doctype).' class="ipmControlSubmit '.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'" '.$this->getValidationAttributesStr($doctype).' value="'.htmlspecialchars($this->getDefaultValue()).'" />';
    }

    public function getLayout() {
        return self::LAYOUT_DEFAULT;
    }


    public function getType() {
        return self::TYPE_SYSTEM;
    }

    /**
     * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
     */
    public function getTypeClass() {
        return 'submit';
    }

    public function setImageSrc($imageSrc)
    {
        $this->imageSrc = $imageSrc;
    }

    public function getImageSrc()
    {
        return $this->imageSrc;
    }


}