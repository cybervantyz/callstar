<?php

    namespace PCS\Object\Markup\Navigation;

    class Child extends \PCS\Object\Markup {
        
        protected $_variation = 'navigation.child';
        
        public function render() {
            return '<a class="markup section navigation" href="http://' . $this->g('object.address') . '">' . parent::render() . '</a>';
        }
        
    }