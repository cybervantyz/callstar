<?php

    namespace PCS\Form\Element\Text\Labelless;
    
    class Markup extends \PCS\Form\Element\Text\Markup {
        
        public function initialize() {
            parent::initialize();
            $this->s('columns.count.maximum', 1)
                 ->g('elements')->remove('label');
            return $this;
        }
        
    }