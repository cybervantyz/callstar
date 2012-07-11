<?php

    namespace PCS\Markup\Row\Collection;

    class Count extends \PCS\Collection\Count {
        
        public function initialize() {
            parent::initialize();
            $this->s('minimum', 1   )
                 ->s('maximum', null);
            return $this;
        }
        
    }