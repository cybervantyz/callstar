<?php

    namespace PCS\Entity;
    
    class Entry extends \PCS\Object {
        
        protected static $_delayed = array(
            'instances' => array('Instance', 'Collection')
        );
        protected static $_denied = array('undefined', 'any');
        
        protected $_instances;
        protected $_value;
        
        protected function get($target, $property, $query) {
            switch ($target) {
                case 'value':
                    if (is_null($this->$property) == true) $this->s($target, $this->g('instances.actual.value'));
                    break;
            }
            return parent::get($target, $property, $query);
        }
        
        protected function finalize() {
            parent::finalize();
            $this->s('value', null);
            return $this;
        }
        
        public function render() {
            return '' . $this->g('value');
        }
        
        public function __toString() {
            return $this->render();
        }
        
        public function save() {
            $this->s('value', null);
            parent::save();
            return $this;
        }
        
    }