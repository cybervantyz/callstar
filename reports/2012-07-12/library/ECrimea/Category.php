<?php

    namespace ECrimea;
    
    class Category extends \ECrimea\Entity {
        
        protected static $_delayed = array(
            'children' => 'Collection',
            'elements' => array('PCS', 'Entity', 'Collection')
        );
        
        protected $_parent;
        protected $_children;
        protected $_elements;
        
        protected function set($target, $property, $query, $element) {
            switch ($target) {
                case 'parent':
                    if (count($query) > 0) return $this;
                    if ($this->h('parent') == true) $this->g('parent.children')->remove($this);
                    $element->g('children')->append($this);
                    break;
            }
            parent::set($target, $property, $query, $element);
            return $this;
        }
        
    }