<?php

    namespace PCS\Form\Element\Password\Labelless;
    
    class Markup extends \PCS\Form\Element\Password\Markup {
        
        public function initialize() {
            parent::initialize();
            $this->s('columns.count.maximum', 1)
                 ->g('elements')->remove('label');
            return $this;
        }
        
    }