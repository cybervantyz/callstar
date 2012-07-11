<?php

    namespace PCS\Form\Control;

    class Submit extends \PCS\Form\Control {
        
        protected $_type = self::TYPE_CONTROL_SUBMIT;
        
        public function initialize() {
            parent::initialize();
            $this->s('name', 'submit')
                 ->s('title.instances.first.e.value', 'Отправить');
            return $this;
        }
        
        public function render() {
            ob_start(); ?>
            <input
                   id="<?php print $this->g('id'   ); ?>"
                 type="<?php print $this->g('type' ); ?>"
                 name="<?php print $this->g('fullname'); ?>"
                value="<?php print $this->g('title'); ?>"
            /><?php
            return parent::render(ob_get_clean());
        }
        
    }