<?php

    namespace PCS\Language;
    
    class Code extends \PCS\Entity {
        
        protected $_iso_639_1;
        protected $_iso_639_3;
        
        protected static $_denied = array('undefined', 'any');
        
        protected function get($target, $property, $query) {
            switch ($target) {
                case 'iso-639-1': $target = 'iso_639_1'; break;
                case 'iso-639-3': $target = 'iso_639_3'; break;
            }
            $property = '_' . $target;
            return parent::get($target, $property, $query);
        }
        
    }