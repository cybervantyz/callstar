<?php

    namespace PCS;
    
    class Hub extends \PCS\Object {
        
        protected static $_delayed = array();
        
        protected $_object;
        
        protected function set($target, $property, $query, $element) {
            if (count($query) == 0) {
                switch ($target) {
                    case 'object':
                        $statics = static::getStatics();
                        foreach (static::gS('delayed') as $e => $foo) {
                            $e = '_' . $e;
                            if (in_array($e, $statics, true) == true) continue;
                            if (is_object($this->$e) == false) continue;
                            print $this->$e->g('class') . '<br />';
                            $this->$e->s('object', $element);
                        }
                }
            }
            parent::set($target, $property, $query, $element);
            return $this;
        }
        
        protected function get($target, $property, $query) {
            if ((property_exists($this, $property) == true) && (in_array($property, static::getStatics(), true) == false) && (is_null($this->$property) == true)) {
                if (in_array($target, array_keys(static::gS('delayed')), true) == true) {
                    $result = parent::get($target, $property, array());
                    $result->s('object', $this->g('object'));
                }
            }
            return parent::get($target, $property, $query);
        }
        
        protected static function getClosestClassname() {
            $target = implode('\\', func_get_args());
            $classes = static::gS('classes');
            foreach ($classes as $class) {
                $classname = preg_replace('/\\\\Hub$/', '', $class) . '\\' . $target;
                if (class_exists($classname) == true) return $classname;
            }
            if (class_exists($classname) == true) return $classname;
            return parent::getClosestClassname($target);
        }
        
    }