<?php

    namespace PCS\Form\Element;

    class Password extends \PCS\Form\Element {
        
        protected $_type = self::TYPE_ELEMENT_PASSWORD;
        protected $_decorated = true;
        
        public function render() {
            ob_start(); ?>
            <input
                   id="<?php print $this->g('id'   ); ?>"
                 type="<?php print $this->g('type' ); ?>"
                 name="<?php print $this->g('name' ); ?>"
                value="<?php print $this->g('value'); ?>"
                autocomplete="off"
            /><?php
            return parent::render(ob_get_clean());
        }
        
        public function __toString() {
            return $this->render();
        }
        
    }