<?php

    namespace PCS;

    class Collection extends \PCS\Object implements \Iterator {
        
        const BEFORE = 'before';
        const AFTER  = 'after' ;
        
        protected $_elements = array();
        protected $_indexes  = array();
        protected $_count;
        
        protected $_first;
        protected $_last;
        protected $_current;
        
        protected static $_denied  = array('elements', 'illustration', 'title', 'description', 'children');
        protected static $_delayed = array('count' => 'Count');
        
        public function getElements() {
            return $this->_elements;
        }
        
        protected function get($target, $property, $query) {
            switch ($target) {
                case 'keys':
                    return array_keys($this->_elements);
                    break;
            }
            $result = parent::get($target, $property, $query);
            if (is_null($result) == false) return $result;
            if (isset($this->_elements[$target]) == false) return null;
            $result = $this->_elements[$target];
            if (\PCS\Object\Registry::isIndex($result) == true) $result = \PCS\Object\Registry::g($result);
            if ((count($query) > 0) && (is_object($result) == true)) $result = $result->g($query);
            return $result;
        }
        
        protected function set($target, $property, $query, $element) {
            if (count($query) > 0) {
                if (is_object($this->g($query)) == false) {
                    print '\\PCS\\Collection::set(' . $target . ')<br />';
                    print static::gS('class') . '<br />';
                    print_r($query);
                    var_dump($this->g($query));
                    die();
                }
                $this->g($query)->s($target, $element);
                return $this;
            }
            if ((property_exists($this, $property) == false) || (in_array($target, static::gS('denied'), true) == true)) {
                if (isset($this->_elements[$target]) == true) return $this;
                $this->append($element, $target);
                return $this;
            }
            $this->$property = $element;
            return $this;
        }
        
        public function append($element = null, $key = null) {
            $element = $this->add($element, $key);
            if (is_null($element) == true) return $this;
            if ($this->h('last') == true) {
                $this->s('last.next', $element);
                $element->s('previous', $this->g('last'));
            }
            if ($this->g('count.current') == 1) $this->s('first', $element)->s('current', $element);
            $this->s('last', $element);
            return $this;
        }
        
        public function prepend($element = null, $key = null) {
            $element = $this->add($element, $key);
            if (is_null($element) == true) return $this;
            if ($this->h('first') == true) {
                $this->s('first.previous', $element);
                $element->s('next', $this->g('first'));
            }
            if ($this->g('count.current') == 1) $this->s('last', $element)->s('current', $element);
            $this->s('first', $element);
            return $this;
        }
        
        public function insert($element = null, $key = null, $mode = self::AFTER, $target = null) {
            if ($this->g('count.current') == 0) {
                $this->append($element, $key);
                return $this;
            }
            if (in_array($mode, array(self::BEFORE, self::AFTER), true) == false) return $this;
            if (is_null($target) == true) $target = $this->g('last.i');
            if (isset($this->_elements[$target]) == false) return $this;
            $target = $this->_elements[$target];
            $element = $this->add($element, $key);
            switch ($mode) {
                case self::BEFORE:
                    if ($target->h('previous') == true) {
                        $target->s('previous.next', $element);
                        $element->s('previous', $target->g('previous'));
                    }
                    $target->s('previous', $element);
                    $element->s('next', $target);
                    break;
                case self::AFTER:
                    if ($target->h('next') == true) {
                        $target->s('next.previous', $element);
                        $element->s('next', $target->g('next'));
                    }
                    $target->s('next', $element);
                    $element->s('previous', $target);
                    break;
            }
            return $this;
        }
        
        protected function add($element = null, $key = null) {
            if (($this->h('count.maximum') == true) && ($this->g('count.current') >= $this->g('count.maximum'))) return null;
            if (empty($key) == true) {
                $key = 0;
                while (isset($this->_elements[$key]) == true) $key += 1;
                $key = '' . $key;
            }
            if (isset($this->_elements[$key]) == true) return null;
            if ((is_object($element) == true) && ($element->h('index') == true)) {
                if (array_key_exists($element->g('index'), $this->_indexes) == true) return null;
                $this->_indexes[$element->g('index')] = $key;
            }
            $e = static::produce('Element')->s('collection', $this);
            if (is_null($element) == true) $element = '&nbsp;';
            $e->s('i', $key    )
              ->s('e', $element);
            $this->_elements[$key] = $e;
            $this->s('count.current', count($this->_elements))
                 ->s('modified', true);
            return $e;
        }
        
        public function remove($key) {
            if ((is_object($key) == true) && (empty($this->_indexes) == false) && ($key->h('index') == true) && (isset($this->_indexes[$key->g('index')]) == true)) $key = $this->_indexes[$key->g('index')];
            if (is_object($key) == true) {
                foreach ($this->_elements as $k => $element) {
                    if (is_object($element) == false) continue;
                    if ($element->g('index') != $key->g('index')) continue;
                    $key = $k;
                    break;
                }
            }
            if (is_object($key) == true) return $this;
            if (isset($this->_elements[$key]) == false) return $this;
            $element = $this->_elements[$key];
            if (is_object($element) == true) {
                if ($this->g('first') === $element) $this->s('first', $element->g('next'    ));
                if ($this->g('last' ) === $element) $this->s('last' , $element->g('previous'));
                if ($element->h('previous') == true) $element->s('previous.next', $element->g('next'    ));
                if ($element->h('next'    ) == true) $element->s('next.previous', $element->g('previous'));
                if ($element->h('index') == true) unset($this->_indexes[$element->g('index')]);
            }
            unset($this->_elements[$key]);
            $this->s('count.current', count($this->_elements));
            return $this;
        }
        
        public function current() {
            return $this->g('current');
        }
        
        public function next() {
            $this->s('current', $this->g('current.next'));
            return $this;
        }
        
        public function key() {
            return $this->g('current.i');
        }
        
        public function valid() {
            return $this->h('current');
        }
        
        public function rewind() {
            $this->s('current', $this->g('first'));
            return $this;
        }
        
        protected static function getClosestClassname() {
            $target = implode('\\', func_get_args());
            if (in_array($target, array('Element', 'Count'), true) == true) {
                if ($target == 'Element') {
                    $classes = static::gS('classes');
                    foreach ($classes as $class) {
                        if (preg_match('/\\\\Element\\\\Collection$/', $class) == 0) continue;
                        $classname = preg_replace('/\\\\Element\\\\Collection$/', '', $class) . '\\Element';
                        if (class_exists($classname) == true) return $classname;
                    }
                }
                return parent::getClosestClassname($target);
            }
            $classname = parent::getClosestClassname($target);
            if (is_null($classname) == false) return $classname;
            $classname = preg_replace('/\\\\Collection$/', '', static::gS('class'));
            if (class_exists($classname) == true) return $classname;
        }
        
    }