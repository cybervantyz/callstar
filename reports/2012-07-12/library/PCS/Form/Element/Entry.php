<?php

    namespace PCS\Form\Element;

    class Entry extends \PCS\Form\Element {
        
        protected $_type = self::TYPE_ELEMENT_ENTRY;
        protected $_decorated = false;
        
        public function render() {
            return parent::render($this->g('value'));
        }
        
    }