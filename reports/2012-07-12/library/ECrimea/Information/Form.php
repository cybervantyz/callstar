<?php

    namespace ECrimea\Information;
    
    class Form extends \ECrimea\Entity\Form {
        
        public function initialize() {
            parent::initialize();
            $this->s('name', 'information')
                 ->s('title.instances.first.e.value', 'Новая информационная страница');
            return $this;
        }
        
    }