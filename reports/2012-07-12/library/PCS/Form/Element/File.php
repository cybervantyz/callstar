<?php

    namespace PCS\Form\Element;

    class File extends \PCS\Form\Element {
        
        protected $_type = self::TYPE_ELEMENT_FILE;
        protected $_decorated = false;
        
        public function render() {
            ob_start(); ?>
            <input
                   id="<?php print $this->g('id'      ); ?>"
                 type="<?php print $this->g('type'    ); ?>"
                 name="<?php print $this->g('fullname'); ?>"
                value=""
                autocomplete="off"
            /><?php
            return parent::render(ob_get_clean());
        }
        
        public function __toString() {
            return $this->render();
        }
        
    }