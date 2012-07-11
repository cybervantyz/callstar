<?php

    namespace PCS\Entity\Entry;

    class Instance extends \PCS\Entity {
        
        protected $_language;
        protected $_value;
        
        protected $_actuality;
        
        protected function get($target, $property, $query) {
            switch ($target) {
                case 'actuality':
                    if (is_null($this->_actuality) == false) return $this->_actuality;
                    $actuality = 0;
                    if ($this->g('language.index') == \PCS\Language::gS(    'any.index')) $actuality += 1;
                    if ($this->g('language.index') == \PCS\Language::gS('current.index')) $actuality += 2;
                    $this->_actuality = $actuality;
                    return $this->_actuality;
                    break;
            }
            return parent::get($target, $property, $query);
        }
        
        protected function finalize() {
            parent::finalize();
            $this->s('actuality', null);
            return $this;
        }
        
    }