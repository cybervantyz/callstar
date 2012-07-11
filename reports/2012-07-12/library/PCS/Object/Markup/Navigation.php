<?php

    namespace PCS\Object\Markup;
    
    class Navigation extends \PCS\Object\Markup {
        
        public function render() {
            return '<a class="markup section navigation" href="http://' . $this->g('object.address') . '">' . parent::render() . '</a>';
        }
        
    }