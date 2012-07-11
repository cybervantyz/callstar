<?php

    namespace PCS\Form\Element;

    class Text extends \PCS\Form\Element {
        
        protected $_type = self::TYPE_ELEMENT_TEXT;
        protected $_decorated = true;
        
        public function render() {
            ob_start(); ?>
            <input
                   id="<?php print $this->g('id'      ); ?>"
                 type="<?php print $this->g('type'    ); ?>"
                 name="<?php print $this->g('fullname'); ?>"
                value="<?php print $this->g('value'   ); ?>"
                autocomplete="off"
            /><?php
            return parent::render(ob_get_clean());
        }
        
        public function __toString() {
            return $this->render();
        }
        
    }