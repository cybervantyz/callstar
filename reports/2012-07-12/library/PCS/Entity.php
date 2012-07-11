<?php

    namespace PCS;
    
    class Entity extends \PCS\Object {
        
        protected static $_delayed = array(
            'registry'  => 'Registry',
            'metadata'  => 'Metadata',
            'schema'    => 'Schema',
            'undefined' => 'Undefined',
            'any'       => 'Any'
        );
        
        protected static $_registry;
        protected static $_metadata;
        protected static $_schema;
        
        protected static $_undefined;
        protected static $_any;
        
        protected static function getS($target, $property, $query) {
            switch ($target) {
                case 'registry':
                    $result = \PCS\Object\Registry::stat(static::gS('class'), $target);
                    if (is_null($result) == true) $result = parent::getS($target, $property, array())->s('classname', static::gS('class'));
                    break;
                case 'schema':
                    $result = \PCS\Object\Registry::stat(static::gS('class'), $target);
                    if (is_null($result) == true) {
                        $result = parent::getS($target, $property, array());
                        if ($result->g('fields.count.current') == 0) {
                            $classnames = static::gS('classes');
                            $classname = $classnames[1];
                            $schema = $classname::gS('schema');
                            if (is_null($schema) == true) break;
                            foreach ($schema->g('fields') as $f) {
                                $field = $result::produce('Field');
                                $field->s('name' , $f->g('e.name' ))
                                      ->s('query', $f->g('e.query'));
                                $result->g('fields')->append($field);
                            }
                        }
                    }
                    break;
            }
            return parent::getS($target, $property, $query);
        }
        
        protected function get($target, $property, $query) {
            switch ($target) {
                case 'section':
                    $result = parent::get($target, $property, array());
                    $result->s('object', $this->g('markup.full'));
                    break;
            }
            return parent::get($target, $property, $query);
        }
        
        public function register() {
            parent::register();
            $classes = static::gS('classes');
            foreach ($classes as $classname) {
                if (in_array($classname, array('\\PCS\\Entity', '\\PCS\\Object'), true) == true) continue;
                $classname::gS('registry')->append($this)->s('modified', true);
            }
            return $this;
        }
        
        public function save($touchRegistries = false) {
            parent::save();
            if ($touchRegistries == true) {
                $classes = static::gS('classes');
                foreach ($classes as $classname) {
                    if (in_array($classname, array('\\PCS\\Entity', '\\PCS\\Object'), true) == true) continue;
                    $classname::gS('registry')->save();
                    if ($classname == '\\PCS\\Section') {
                        foreach ($classname::gS('registry') as $element) {
                            print $element->g('e.title.instances.first.e.value') . '<br />';
                        }
                        print $this->g('title.instances.first.e.value');
                        die();
                    }
                }
            }
            return $this;
        }
        
    }