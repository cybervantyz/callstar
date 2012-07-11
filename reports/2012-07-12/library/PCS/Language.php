<?php

    namespace PCS;
    
    class Language extends \PCS\Entity {
        
        protected static $_delayed = array(
            'code' => 'Code'
        );
        protected static $_current;
        
        protected $_code;
        
        protected static function getS($target, $property, $query) {
            switch ($target) {
                case 'current':
                    $result = static::current();
                    if (count($query) > 0) $result = $result->g($query);
                    return $result;
                    break;
            }
            return parent::getS($target, $property, $query);
        }
        
        protected static function current() {
            if (is_null(self::$_current) == false) return self::$_current;
            $languages = static::gS('registry');
            if (false && isset($_SESSION['language']) == true) {
                self::$_current = \PCS\Object\Registry::g($_SESSION['language']);
                if (is_null(self::$_current) == false) return self::$_current;
            }
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) == true) {
                $accept = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                foreach ($accept as $key => $part) {
                    $code    = $part;
                    $quality = 1;
                    if ($code == '*') {
                        $keys = array_keys($languages->g('elements'));
                        foreach ($keys as $key) {
                            $language = $languages->g($key . '.e');
                            if ($language === $language->g('undefined')) continue;
                            if ($language === $language->g('any'      )) continue;
                            self::$_current = $language;
                            $_SESSION['language'] = $language->g('index');
                            return $language;
                        }
                    }
                    if (strpos($part, ';') > 0) {
                        list($code, $quality) = explode(';', $part);
                        $quality = floatval(str_replace('q=', '', $quality));
                    }
                    $accept[$key] = array(
                        'code'    => $code   ,
                        'quality' => $quality
                    );
                }
                foreach ($accept as $variant) {
                    $keys = $languages->g('keys');
                    foreach ($keys as $key) {
                        $language = $languages->g($key . '.e');
                        if (strtolower($language->g('code.iso-639-1')) != strtolower($variant['code'])) continue;
                        self::$_current = $language;
                        $_SESSION['language'] = $language->g('index');
                        return $language;
                    }
                }
            }
            $keys = $languages->g('keys');
            foreach ($keys as $key) {
                $language = $languages->g($key . '.e');
                if ($language === $language->g('undefined')) continue;
                if ($language === $language->g('any'      )) continue;
                self::$_current = $language;
                $_SESSION['language'] = $language->g('index');
                return $language;
            }
            return $languages->g('first.e');
        }
        
    }