<?php

    namespace PCS\Form\Element;
    
    class Collection extends \PCS\Collection {
        
        protected $_object;
        
        protected function add($element = null, $key = null) {
            if (is_object($element) == true) {
                if ($element->isInstanceOf('PCS', 'Section') == true) $element = $element->g('markup.navigation');
                if ($element->isInstanceOf('PCS', 'Form', 'Thing') == true) {
                    $key = $element->g('name');
                    $element->s('masterform', $this->g('object'));
                }
            }
            return parent::add($element, $key);
        }
        
        public static function getClosestClassname() {
            $target = implode('\\', func_get_args());
            if ($target == 'Element') {
                foreach (static::gS('classes') as $classname) {
                    $classname .= '\\Element';
                    if (class_exists($classname) == true) return $classname;
                }
            }
            return parent::getClosestClassname($target);
        }
        
    }