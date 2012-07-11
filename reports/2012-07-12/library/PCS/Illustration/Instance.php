<?php

    namespace PCS\Illustration;
    
    class Instance extends \PCS\Entity\Entry\Instance {
        
        protected static $_delayed = array(
            'instances' => array('Instance', 'Collection')
        );
        
        protected $_instances;
        
        protected function get($target, $property, $query) {
            if (strpos($target, 'Æ') === false) return parent::get($target, $property, $query);
            list($width, $height) = explode('Æ', $target);
            if (($width  == 'null') || (empty($width ) == true)) $width  = null;
            if (($height == 'null') || (empty($height) == true)) $height = null;
            foreach ($this->g('instances') as $instance) {
                $instance = $instance->g('e');
                if ($instance->g('width' ) != $width ) continue;
                if ($instance->g('height') != $height) continue;
                return $instance->g($query);
            }
            $instance = static::produce('Instance');
            $instance->s('width' , $width )
                     ->s('height', $height)
                     ->s('value' , $this->g('value'))
                     ->save();
            $this->g('instances')->append($instance);
            $this->save();
            return $instance->g($query);
        }
        
        protected function set($target, $property, $query, $element) {
            switch ($target) {
                case 'value':
                    if ($this->h('index') == false) $this->register();
                    $filename = \PCS\Object\Registry::getFilename($this->g('index'));
                    $filename = preg_replace('/.php$/', '.png', $filename);
                    $directory = explode('/', $filename );
                    array_pop($directory);
                    $directory = implode('/', $directory);
                    if (file_exists($directory) == false) mkdir($directory, 0766, true);
                    // rename($element, $filename);
                    // copy($element, $filename);
                    $information = getimagesize($element);
                    $image = null;
                    switch ($information['mime']) {
                        case 'image/png' : $image = imagecreatefrompng ($element); break;
                        case 'image/gif' : $image = imagecreatefromgif ($element); break;
                        case 'image/jpeg':
                        case 'image/jpg' : $image = imagecreatefromjpeg($element); break;
                    }
                    if (is_null($image) == true) return $this;
                    imagesavealpha($image, true);
                    imagepng($image, $filename, 0, PNG_ALL_FILTERS);
                    parent::set('value', '_value', array(), $filename);
                    if (is_uploaded_file($element) == true) unlink($element);
                    foreach ($this->g('instances') as $instance) $instance->s('e.value', $this->g('value'));
                    return $this;
                    break;
            }
            parent::set($target, $property, $query, $element);
            return $this;
        }
        
    }