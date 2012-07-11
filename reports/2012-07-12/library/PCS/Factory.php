<?php

    namespace PCS;

    abstract class Factory {
        
        public static function produce($classname) {
            $object = new $classname;
            $object->register();
            return $object;
        }
        
    }