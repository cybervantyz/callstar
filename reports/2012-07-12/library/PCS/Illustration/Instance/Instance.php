<?php

    namespace PCS\Illustration\Instance;
    
    class Instance extends \PCS\Object {
        
        protected $_value;
        protected $_width;
        protected $_height;
        
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
                    $element = './' . $element;
                    if ((file_exists($element) == false) || (is_file($element) == false)) return $this;
                    $sourceimage = imagecreatefrompng($element);
                    $sourceimagewidth  = imagesx($sourceimage);
                    $sourceimageheight = imagesy($sourceimage);
                    $sW = $sourceimagewidth ;
                    $sH = $sourceimageheight;
                    $w  = $this->g('width' );
                    $h  = $this->g('height');
                    if ((is_null($w) == true) && (is_null($h) == true)) {
                        $w = $sW;
                        $h = $sH;
                    }
                    if (is_null($w) == true) $w = ($sW / $sH) * $h;
                    if (is_null($h) == true) $h = ($sH / $sW) * $w;
                    $kW = $sW / $w;
                    $kH = $sH / $h;
                    if ($kW > $kH) $sW = $kH * $w;
                    if ($kW < $kH) $sH = $kW * $h;
                    $image = imagecreatetruecolor($w, $h);
                    imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
                    imagecopyresampled($image, $sourceimage, 0, 0, round(($sourceimagewidth - $sW) / 2), round(($sourceimageheight - $sH) / 2), $w, $h, $sW, $sH);
                    imagesavealpha($image, true);
                    imagepng($image, $filename, 0, PNG_ALL_FILTERS);
                    chmod($filename, 0766);
                    parent::set('value', '_value', array(), $filename);
                    $this->save();
                    return $this;
                    break;
            }
            parent::set($target, $property, $query, $element);
            return $this;
        }
        
        public function render() {
            ob_start(); ?>
            <img src="<?php print preg_replace('/^.\//', '/', $this->g('value')); ?>" /><?php
            return ob_get_clean();
        }
        
        public function __toString() {
            return $this->render();
        }
        
    }