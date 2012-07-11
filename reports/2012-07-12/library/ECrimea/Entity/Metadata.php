<?php

    namespace ECrimea\Entity;
    
    class Metadata extends \PCS\Entity\Metadata {
        
        public function initialize() {
            parent::initialize();
            return $this;
        }
        
    }