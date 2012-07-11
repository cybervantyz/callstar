<?php

    namespace PCS\Object;

    abstract class Registry {
        
        protected static $_index = 'abcdefghijklmnopqrstuvwxyz';
        protected static $_implodedindex;
        protected static $_length   = 16;
        protected static $_elements = array();
        protected static $_static   = array();
        
        public static function register($object) {
            if ($object->h('index') == false) $object->s('index', self::generateIndex());
            self::$_elements[$object->g('index')] = $object;
        }
        
        public static function unregister() {
            
        }
        
        public static function getElements() {
            return self::$_elements;
        }
        
        public static function getStatic() {
            return self::$_static;
        }
        
        public static function g($index) {
            if (class_exists($index) == false) {
                $filename = self::getFilename($index);
                if (file_exists($filename) == false) return null;
                require_once($filename);
                self::$_elements[$index] = new $index();
            }
            return self::$_elements[$index];
        }
        
        public static function h($index) {
            if (is_array(self::$_elements) == false) return false;
            if (array_key_exists($index, self::$_elements) == false) return false;
            return true;
        }
        
        protected static function generateIndex() {
            if (is_array(self::$_index) == false) self::$_index = str_split(self::$_index, 1);
            $size = count(self::$_index) - 1;
            $index = '';
            for ($i = 0; $i < self::$_length; $i ++) $index .= self::$_index[rand(0, $size)];
            if (array_key_exists($index, self::$_elements) == true) $index = self::generateIndex();
            return $index;
        }
        
        public static function isIndex($index) {
            if (is_string($index) == false) return false;
            if (strpos($index, '\\') !== false) return false;
            if (mb_strlen($index, 'UTF-8') != self::$_length) return false;
            if (is_array(self::$_index) == false) self::$_index = str_split(self::$_index, 1);
            if (preg_match('/[^' . implode('', self::$_index) . ']/', $index) == 1) return false;
            return true;
        }
        
        public static function getFilename($index) {
            $filename = str_split($index, 8);
            array_unshift($filename, 'data');
            $directory = $filename;
            array_pop($directory);
            $directory = implode('/', $directory);
            if (file_exists($directory) == false) {
                mkdir($directory, 0766, true);
                chmod($directory, 0755);
            }
            $filename  = implode('/', $filename);
            return './' . $filename . '.php';
        }
        
        public static function save() {
            foreach (self::$_static as $classname => $entries) {
                foreach ($entries as $name => $element) {
                    if (is_object($element) == false) continue;
                    $element->save();
                }
            }
            ob_start(); ?>

    if (defined('APPLICATION_PATH') == false) die('Fuck off!!!');

    $static = <?php print self::exportArray(self::$_static, 2, array('statics', 'delayed', 'denied')); ?>;<?php
            $data = ob_get_clean();
            $handler = fopen('data/registry.php', 'w');
            fwrite($handler, $data);
        }
        
        public static function load() {
            $filename = 'data/registry.php';
            if (file_exists($filename) == false) return;
            $handler = fopen($filename, 'r');
            $data = '';
            while (feof($handler) == false) $data .= fread($handler, 1024);
            eval($data);
            self::$_static   = $static  ;
        }
        
        public static function stat($classname, $name, $value = null) {
            if (is_null($value) == false) {
                self::$_static[$classname][$name] = $value;
                return;
            }
            if (isset(self::$_static[$classname][$name]) == false) return null;
            $result = self::$_static[$classname][$name];
            if (self::isIndex($result) == true) $result = self::g($result);
            return $result;
        }
        
        protected static function exportArray($array, $offset = 2, $ignore = array()) {
            ob_start();
            print 'array(';
            $counter = 0;
            foreach ($array as $index => $element) {
                if (in_array($index, $ignore, true) == true) continue;
                if ($counter > 0) print ',';
                print "\n";
                for ($i = 0; $i < $offset; $i ++) print '    ';
                print var_export($index); ?> => <?php
                switch (gettype($element)) {
                    case 'array' : print self::exportArray($element, $offset + 1, $ignore); break;
                    case 'object': print var_export($element->g('index')); break;
                          default: print var_export($element); break;
                }
                $counter ++;
            }
            print "\n";
            for ($i = 0; $i < $offset - 1; $i ++) print '    ';
            print ')';
            return ob_get_clean();
        }
        
    }