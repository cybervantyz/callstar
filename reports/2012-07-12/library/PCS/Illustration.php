<?php

    namespace PCS;
    
    class Illustration extends \PCS\Object {
        
        const TYPE_IMG_PNG = 'image/png';
        const TYPE_IMG_GIF = 'image/gif';
        const TYPE_IMG_JPG = 'image/jpg';
        const TYPE_OBJECT_FLASH = 'application/x-shockwave-flash';
        
        protected static $_delayed = array(
            'instances' => array('Instance', 'Collection')
        );
        
        protected $_instances;
        
        protected function set($target, $property, $query, $element) {
            switch ($target) {
                case 'filename':
                    foreach ($this->g('instances') as $instance) $instance->s('e.value', $element);
                    return $this;
                    break;
            }
            parent::set($target, $property, $query, $element);
        }
        
    }