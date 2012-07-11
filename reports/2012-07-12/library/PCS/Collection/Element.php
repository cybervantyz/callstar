<?php

    namespace PCS\Collection;
    
    class Element extends \PCS\Object {
        
        const UP   = 'up'  ;
        const DOWN = 'down';
        
        protected $_collection;
        protected $_next;
        protected $_previous;
        protected $_i;
        protected $_e;
        
        public function move($direction, $count = 1) {
            if (in_array($direction, array(self::UP, self::DOWN), true) == false) return $this;
            $elements = array(-2 => null, -1 => null, 1 => null, 2 => null);
            if ($this->h('previous' . '.previous') == true) $elements[-2] = $this->g('previous' . '.previous');
            if ($this->h('next'     . '.next'    ) == true) $elements[ 2] = $this->g('next'     . '.next'    );
            if ($this->h('previous') == true) $elements[-1] = $this->g('previous');
            if ($this->h('next'    ) == true) $elements[ 1] = $this->g('next'    );
            switch ($direction) {
                case self::UP:
                    if (is_null($elements[-1]) == true ) return $this;
                    if (is_null($elements[-2]) == false) $elements[-2]->s('next', $this);
                    $elements[-1]->s('previous', $this);
                    if (isset($elements[1]) == true) {
                        $elements[-1]->s('next'    , $elements[ 1]);
                        $elements[ 1]->s('previous', $elements[-1]);
                    }
                    $this->s('previous', $elements[-2]);
                    $this->s('next'    , $elements[-1]);
                    break;
                case self::DOWN:
                    if (is_null($elements[1]) == true) return $this;
                    if (is_null($elements[2]) == false) $elements[2]->s('previous', $this);
                    $elements[ 1]->s('next'    , $this);
                    if (isset($elements[-1]) == true) {
                        $elements[ 1]->s('previous', $elements[-1]);
                        $elements[-1]->s('next'    , $elements[ 1]);
                    }
                    $this->s('next'    , $elements[2]);
                    $this->s('previous', $elements[1]);
                    break;
            }
            return $this;
        }
        
        public function render() {
            $e = $this->g('e');
            if (  is_null($e) == true) return '&nbsp;';
            if (is_object($e) == true) return $e->render();
            return $e;
        }
        
        public function __toString() {
            return $this->render();
        }
        
    }