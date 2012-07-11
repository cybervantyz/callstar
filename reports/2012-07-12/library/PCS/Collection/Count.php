<?php

    namespace PCS\Collection;

    class Count extends \PCS\Object {
        
        protected $_current;
        protected $_minimum;
        protected $_maximum;
        
        public function initialize() {
            parent::initialize();
            $this->s('current', 0);
            return $this;
        }
        
        public function set($target, $property, $query, $element) {
            if ((is_null($element) == false) && (in_array($target, array('current', 'minimum', 'maximum'), true) == true)) $element = abs(intval($element));
            parent::set($target, $property, $query, $element);
            return $this;
        }
        
    }