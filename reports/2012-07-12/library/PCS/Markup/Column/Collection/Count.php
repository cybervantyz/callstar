<?php

    namespace PCS\Markup\Column\Collection;

    class Count extends \PCS\Collection\Count {
        
        public function initialize() {
            parent::initialize();
            $this->s('minimum', 1)
                 ->s('maximum', 1);
            return $this;
        }
        
    }