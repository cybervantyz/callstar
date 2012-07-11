<?php

    namespace PCS\Form\Element;

    class Space extends \PCS\Form\Element {
        
        protected $_type = self::TYPE_ELEMENT_SPACE;
        protected $_decorated = true;
        
        public function render() {
            return '&nbsp;';
        }
        
        public function __toString() {
            return $this->render();
        }
        
    }