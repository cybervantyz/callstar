<?php

    namespace PCS\Form\Element;

    class Textarea extends \PCS\Form\Element {
        
        protected $_type = self::TYPE_ELEMENT_TEXTAREA;
        protected $_decorated = true;
        
        public function render() {
            ob_start(); ?>
            <textarea
                  id="<?php print $this->g('id'      ); ?>"
                name="<?php print $this->g('fullname'); ?>"
                autocomplete="off"
            ><?php print $this->g('value'); ?></textarea><?php
            return parent::render(ob_get_clean());
        }
        
        public function __toString() {
            return $this->render();
        }
        
    }