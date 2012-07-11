<?php

    namespace PCS;
    
    class Account extends \PCS\Entity {
        
        protected $_username;
        protected $_password;
        
        public function set($target, $property, $query, $element) {
            switch ($target) {
                case 'password':
                    $element = md5($element);
                    break;
            }
            return parent::set($target, $property, $query, $element);
        }
        
    }