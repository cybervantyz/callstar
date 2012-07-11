<?php

    namespace PCS\Form;
    
    class Element extends \PCS\Form\Thing {
        
        protected static $_delayed = array(
            'markup' => 'Markup'
        );
        
        protected $_markup;
        
        protected function get($target, $property, $query) {
            switch ($target) {
                case 'markup':
                    if (is_null($this->$property) == true) parent::get($target, $property, array())->s('object', $this);
                    break;
                case 'reguired':
                    $value  = $this->g('value' );
                    $errors = $this->g('errors.elements');
                    $this->s('value', '')->validate();
                    $result = $this->h('errors');
                    $this->s('value', $value)
                         ->s('errors.elements', $errors);
                    return $result;
            }
            return parent::get($target, $property, $query);
        }
        
        public function render($content = null) {
            if ($this->g('decorated') == true) $content = \PCS\Decoration::render($content);
            $this->s('markup.elements.element.e', $content);
            ob_start(); ?>
            <div class="formelement type-<?php print $this->g('type'); ?> <?php print $this->g('name'); ?>"><?php
                print $this->g('markup')->render(); ?>
            </div><?php
            return ob_get_clean();
        }
        
    }