<?php

    namespace PCS;
    
    abstract class Loader extends \Zend_Loader_Autoloader {
        
        protected static $_path;
        
        public static function autoload($classname) {
            $filename = self::getPath() . str_replace('\\', '/', $classname) . '.php';
            if (file_exists($filename) == true) require_once($filename);
        }
        
        protected static function getPath() {
            if (is_null(self::$_path) == true) self::$_path = preg_replace('/application$/', 'library', APPLICATION_PATH) . '/';
            return self::$_path;
        }
        
    }
    
    \Zend_Loader_Autoloader::getInstance()->pushAutoloader(array('\\PCS\\Loader', 'autoload'));