<?php

    namespace PCS;

    class Object {
        
        protected static $_delayed = array(
            'children'     => array('PCS', 'Entity', 'Collection'),
            'illustration' => array('PCS', 'Illustration'),
            'title'        => array('PCS', 'Entity', 'Entry', 'String'),
            'description'  => array('PCS', 'Entity', 'Entry', 'String'),
            'markup'       => 'Markup'
        );
        protected static $_denied  = array();
        
        protected $_index;
        protected $_modified = true ;
        
        protected $_master;
        protected $_parent;
        protected $_children;
        
        protected $_illustration;
        protected $_title;
        protected $_description;
        protected $_markup ;
        
        public function __construct() {
        }
        
        public function __destruct() {
        }
        
        public function g($query) {
            if (empty($query) == true) return $this;
            list($target, $property, $query) = static::preprocessGet($query);
            return $this->get($target, $property, $query);
        }
        
        public function s($query, $element) {
            list($target, $property, $query) = static::preprocessSet($query);
            return $this->set($target, $property, $query, $element);
        }
        
        public function h($query) {
            $result = $this->g($query);
            return !is_null($result);
        }
        
        public static function gS($query) {
            list($target, $property, $query) = static::preprocessGet($query);
            return static::getS($target, $property, $query);
        }
        
        public static function sS($query, $element) {
            list($target, $property, $query) = static::preprocessSet($query);
            static::setS($target, $property, $query, $element);
        }
        
        public static function hS($query) {
            return !is_null(static::gS($query));
        }
        
        public function save() {
            if ($this->g('modified') == false) return $this;
            if ($this->h('index') == false) $this->register();
            $this->s('modified', false);
            $classname = $this->g('class');
            ob_start();
            print '<?php' . "\n"; ?>

    if (defined('APPLICATION_PATH') == false) die('Fuck off!!!');

    class <?php print $this->g('index'); ?> extends <?php print $classname; ?> { <?php
            print "\n"; ?>
        public function __construct() {
            parent::__construct(); <?php
            print "\n";
            $properties = array_keys(get_object_vars($this));
            foreach ($properties as $property) {
                if (in_array($property, array('_modified'), true) == true) continue; ?>
            $this-><?php print $property; ?> = <?php
                $value = $this->$property;
                switch (gettype($value)) {
                    case 'array': ?>
array( <?php
                        print "\n";
                        $counter = 0;
                        foreach ($value as $index => $element) {
                            if (is_array($element) == true) continue;
                            if ($counter > 0) { ?>,
<?php } ?>
                <?php print var_export($index, true); ?> => <?php
                            switch (gettype($element)) {
                                case 'object':
                                    $element->save();
                                    print var_export($element->g('index'), true);
                                    break;
                                default:
                                    print var_export($element, true);
                                    break;
                            }
                            $counter ++;
                        }
                        print "\n"; ?>
            )<?php
                        break;
                    case 'object':
                        $value->save();
                        print var_export($value->g('index'), true);
                        break;
                    default:
                        print var_export($value, true);
                }
                print ";\n";
            } ?>
            $this->finalize();
        }
    } <?php
            $data = ob_get_clean();
            $filename = \PCS\Object\Registry::getFilename($this->g('index'));
            $handler = fopen($filename, 'w');
            fwrite($handler, $data);
            fclose($handler);
        }
        
        public function register() {
            \PCS\Object\Registry::register($this);
            return $this;
        }
        
        public function unregister() {
            \PCS\Object\Registry::unregister($this);
            $this->s('modified', false);
            return $this;
        }
        
        public function initialize() {
            return $this;
        }
        
        protected function finalize() {
            \PCS\Object\Registry::register($this);
            $this->s('modified', false);
        }
        
        protected function get($target, $property, $query) {
            if (strpos($target, '\\', 0) === 0) {
                if (class_exists($target) == false) return null;
                if (count($query) == 0) return null;
                $result = $target::gS($query);
                return $result;
            }
            $result = static::getS($target, $property, $query);
            if (is_null($result) == false) return $result;
            if (property_exists($this, $property)             == false) return null;
            if (in_array($property, static::getStatics())     == true ) return null;
            if (in_array($target, static::gS('denied'), true) == true ) return null;
            $result = $this->$property;
            if ($target == 'index') return $result;
            if (is_null($result) == true) {
                $delayed = static::gS('delayed');
                if (isset($delayed[$target]) == false) return null;
                $classname = $delayed[$target];
                if (is_array($classname) == true) $classname = implode('\\', $classname);
                $result = static::produce($classname);
                $result->s('master', $this);
                if (in_array('object', $result::gS('properties'), true) == true) $result->s('object', $this);
                $this->$property = $result;
                $this->s('modified', true);
            }
            $result = static::postprocessGet($target, $property, $query, $result);
            return $result;
        }
        
        protected static function getS($target, $property, $query) {
            switch ($target) {
                case 'class'  : return static::getClass  (); break;
                case 'classes': return static::getClasses(); break;
                case 'properties':
                    $generic = array_keys(get_class_vars('\\PCS\\Entity'   ));
                    $result  = array_keys(get_class_vars(static::gS('class')));
                    foreach ($result as $k => $v) {
                        if (in_array($v, $generic, true) == false) continue;
                        unset($result[$k]);
                    }
                    foreach ($result as $k => $v) $result[$k] = substr($v, 1);
                    return $result;
                    break;
            }
            if (strpos($target, '\\', 0) === 0) {
                if (class_exists($target) == false) return null;
                if (count($query) == 0) return null;
                $result = $target::gS($query);
                return $result;
            }
            if (in_array($property, static::getStatics()) == false) return null;
            $result = \PCS\Object\Registry::stat(static::gS('class'), $target);
            switch ($target) {
                case 'delayed':
                case 'denied' :
                    if (empty($result) == true) {
                        $result = array();
                        $classes = static::gS('classes');
                        if (isset($classes[1]) == true) {
                            $classname = $classes[1];
                            $result = $classname::gS($target);
                        }
                        switch ($target) {
                            case 'delayed':
                                foreach (static::$$property as $name => $classname) $result[$name] = $classname;
                                foreach (static::$_denied as $name) unset($result[$name]);
                                break;
                            case 'denied':
                                foreach (static::$$property as $i => $name) {
                                    if (in_array($name, $result) == true) continue;
                                    array_push($result, $name);
                                }
                                break;
                        }
                        \PCS\Object\Registry::stat(static::gS('class'), $target, $result);
                    }
                    break;
                default:
                    if (in_array($target, static::gS('denied'), true) == true) return null;
                    $delayed = static::gS('delayed');
                    if ((is_null($result) == true) && (isset($delayed[$target]) == true)) {
                        $classname = $delayed[$target];
                        if (is_array($classname) == true) $classname = implode('\\', $classname);
                        $result = static::produce($classname);
                        \PCS\Object\Registry::stat(static::gS('class'), $target, $result);
                    }
                    break;
            }
            $result = static::postprocessGet($target, $property, $query, $result);
            return $result;
        }
        
        protected function set($target, $property, $query, $element) {
            if (count($query) > 0) {
                if (is_null($this->g($query)) == true) {
                    print '\\PCS\\Object::set(' . $target . ')<br />';
                    print static::gS('class') . '<br />';
                    print '<pre>';
                    print_r($query);
                    var_dump($element);
                    print '</pre>';
                    die();
                }
                $this->g($query)->s($target, $element);
                return $this;
            }
            if (property_exists($this, $property) == false) return $this;
            if (in_array($property, static::getStatics()) == true) {
                \PCS\Object\Registry::stat($this->g('class'), $target, $element);
            } else {
                if (($target != 'modified') && ($this->$property !== $element)) $this->s('modified', true);
                $this->$property = $element;
            }
            return $this;
        }
        
        protected static function setS($target, $property, $query, $element) {
            if (count($query) > 0) {
                $this->getS($query)->s($target, $element);
                return $this;
            }
            $classname = static::gS('class');
            if (property_exists($classname, $property) == false) return;
            if (in_array($property, static::getStatics()) == false) return;
            \PCS\Object\Registry::stat($classname, $target, $element);
        }
        
        public static function getStatics() {
            $classname = static::gS('class');
            if (is_null(\PCS\Object\Registry::stat($classname, 'statics')) == true) {
                $reflection = new \ReflectionClass($classname);
                \PCS\Object\Registry::stat($classname, 'statics', array_keys($reflection->getStaticProperties()));
            }
            return \PCS\Object\Registry::stat($classname, 'statics');
        }
        
        protected static function getClass() {
            $classes = static::getClasses();
            return $classes[0];
        }
        
        protected static function getClasses() {
            $classname = get_called_class();
            if (\PCS\Object\Registry::isIndex($classname) == true) $classname = get_parent_class($classname);
            $classname = '\\' . $classname;
            $c = array_pop(explode('\\', $classname));
            if (in_array($c, array('Any', 'Undefined'), true) == true) $classname = '\\' . get_parent_class($classname);
            // $result = \PCS\Object\Registry::stat($classname, 'classes');
            // if (is_null($result) == false) return $result;
            $result = array();
            array_push($result, $classname);
            while ($classname = get_parent_class($classname)) array_push($result, '\\' . $classname);
            // \PCS\Object\Registry::stat($classname, 'classes', $result);
            return $result;
        }
        
        protected static function preprocessGet($query) {
            if (is_array($query) == false) $query = explode('.', $query);
            $target = array_shift($query);
            $property = '_' . $target;
            return array($target, $property, $query);
        }
        
        protected static function postprocessGet($target, $property, $query, $result) {
            if (\PCS\Object\Registry::isIndex($result) == true) $result = \PCS\Object\Registry::g($result);
            if ((is_object($result) == true) && (count($query) > 0)) $result = $result->g($query);
            return $result;
        }
        
        protected static function preprocessSet($query) {
            if (is_array($query) == false) $query = explode('.', $query);
            $target = array_pop($query);
            $property = '_' . $target;
            return array($target, $property, $query);
        }
        
        protected static function getClosestClassname() {
            $classname = implode('\\', func_get_args());
            if (class_exists($classname) == true) return $classname;
            $classes = static::gS('classes');
            foreach ($classes as $class) {
                $class = $class . '\\' . $classname;
                if (class_exists($class) == false) continue;
                return $class;
            }
            return null;
        }
        
        public static function produce() {
            $classname = call_user_func_array(array(static::gS('class'), 'getClosestClassname'), func_get_args());
            if (class_exists($classname) == false) {
                print static::gS('class') . ': Cannot produce class: ';
                print_r(func_get_args());
                return null;
            }
            $result = new $classname();
            $result->initialize();
            return $result;
        }
        
        public static function isInstanceOf() {
            $classname = '\\' . implode('\\', func_get_args());
            return in_array($classname, static::gS('classes'));
        }
        
    }