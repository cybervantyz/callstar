<?php

    namespace PCS\Entity\Entry\Instance;
    
    class Collection extends \PCS\Collection {
        
        protected $_actual;
        protected $_cachedindex;
        
        public function initialize() {
            parent::initialize();
            $instance = static::produce('Instance');
            $instance->s('language', \PCS\Language::gS('any'));
            $this->append($instance);
            return $this;
        }
        
        protected function get($target, $property, $query) {
            switch ($target) {
                case 'actual':
                    if (is_null($this->$property) == false) return parent::get($target, $property, $query);
                    $index   = $this->g('cachedindex');
                    $actual  = \PCS\Language::gS(    'any.index');
                    $current = \PCS\Language::gS('current.index');
                    if (isset($index[$current]) == false) $actual = $current;
                    $this->$property = $index[$actual];
                    break;
                case 'cachedindex':
                    if (empty($this->$property) == false) return $this->$property;
                    if ($this->g('count.current') == 0) return $this->$property;
                    $index = array();
                    $keys = $this->g('keys');
                    foreach ($keys as $key) {
                        if ($element->h('e.language.index') == false) $element->g('e.language')->register();
                        $index[$element->g('e.language.index')] = $element->g('e.index');
                    }
                    $this->s($target, $index);
                    return $index;
                    break;
            }
            return parent::get($target, $property, $query);
        }
        
        protected function add($element, $key = null) {
            if ($element->h('language.index') == false) $element->g('language')->register();
            $element = parent::add($element, $key);
            $index[$element->g('e.language.index')] = $element->g('index');
            $this->s('cachedindex', $index);
            return $element;
        }
        
        protected function finalize() {
            parent::finalize();
            $this->s('actual'     , null);
            $this->s('cachedindex', null);
            return $this;
        }
        
    }