<?php

    namespace PCS\Markup;

    class Element extends \PCS\Collection\Element {
        
        protected $_decorated = false;
        protected $_colspan = 1;
        protected $_rowspan = 1;
        protected $_width ;
        protected $_height;
        
        public function render() {
            return $this->g('e');
        }
        
        public function __toString() {
            return $this->render();
        }
        
    }