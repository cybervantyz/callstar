<?php

    namespace PCS\Form\Thing\Error;

    class Collection extends \PCS\Entity\Collection {
        
        public function render() {
            return '&nbsp;';
        }
        
        public function __toString() {
            return $this->render();
        }
        
    }