<?php

    namespace PCS\Form;

    class Control extends \PCS\Form\Thing {
        
        protected static $_delayed = array(
            'markup' => 'Markup'
        );
        
        protected $_markup;
        
        public function initialize() {
            parent::initialize();
            $this->s('decorated', true);
            return $this;
        }
        
        protected function get($target, $property, $query) {
            switch ($target) {
                case 'markup':
                    if (is_null($this->$property) == true) parent::get($target, $property, array())->s('object', $this);
                    break;
            }
            return parent::get($target, $property, $query);
        }
        
        public function validate() {
            return $this;
        }
        
        public function render($content = null) {
            if ($this->g('decorated') == true) $content = \PCS\Decoration::render($content);
            ob_start(); ?>
            <div class="formcontrol type-<?php print $this->g('type'); ?> <?php print $this->g('name'); ?>"><?php print $content; ?></div><?php
            return ob_get_clean();
        }
        
    }