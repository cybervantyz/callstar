<?php

    namespace PCS;

    class Form extends \PCS\Form\Thing {
        
        const METHOD_GET  = 'get' ;
        const METHOD_POST = 'post';
        
        protected static $_delayed = array(
            'elements' => array('Element', 'Collection'),
            'markup'   => 'Markup'
        );
        
        protected $_type = \PCS\Form\Thing::TYPE_FORM;
        protected $_action;
        protected $_method;
        
        protected $_elements;
        protected $_markup;
        
        protected $_object;
        
        public function initialize() {
            parent::initialize();
            $this->s('action', '')
                 ->s('method', static::METHOD_POST)
                 ->s('value' , array())
                 ->g('markup.elements')->append('object.title'      , 'title'      )
                                       ->append('object.description', 'description')
                                       ->append('object.errors'     , 'errors'     )
                                       ->append(static::produce('Markup'), 'elements')
                                       ->append(static::produce('Markup'), 'controls')
                                       ->s('controls.e.columns.count.maximum', null);
            $submit = static::produce('Control', 'Submit');
            $this->g('elements')->append($submit, 'submit');
            $this->g('markup.elements.controls.e.elements')->append($submit, 'submit');
            return $this;
        }
        
        public function render() {
            $this->validate();
            $contents = $this->g('markup')->render();
            $classname = '';
            foreach (static::gS('classes') as $class) {
                if ($class == '\\PCS\\Form\\Thing') break;
                $classname .= ' ' . preg_replace(array('/^_pcs_/', '/^_/'), '', str_replace('\\', '_', strtolower($class)));
            }
            ob_start(); ?>
            <div class="form <?php print $classname; ?>" id="<?php print $this->g('id'); ?>"><?php print $contents; ?></div><?php
            $contents = ob_get_clean();
            if ($this->h('masterform') == false) {
                ob_start(); ?>
                <form action="<?php print $this->g('action'); ?>" method="<?php print $this->g('method'); ?>" enctype="multipart/form-data" target="_self"><?php
                    print $contents;
                    foreach ($this->g('elements') as $element) {
                        $element = $element->g('e');
                        if (is_object($element) == false) continue;
                        if ($element->g('type') != \PCS\Form\Thing::TYPE_ELEMENT_HIDDEN) continue;
                        print $element->render();
                    } ?>
                </form><?php
                $contents = ob_get_clean();
            }
            return $contents;
        }
        
        public function __toString() {
            return $this->render();
        }
        
        protected function get($target, $property, $query) {
            switch ($target) {
                case 'value':
                    if (is_null($this->$property) == false) return $this->$property;
                    $files = array();
                    if ($this->h('form') == true) {
                        $data = $this->g('form.value');
                        $files = array();
                        if (isset($data['files']) == true) $files = $data['files'];
                    } else {
                        $data = array();
                        switch ($this->g('method')) {
                            case static::METHOD_GET : $data = $_GET ; break;
                            case static::METHOD_POST: $data = $_POST; break;
                        }
                        $files = $_FILES;
                    }
                    if (isset($files[$this->g('name')]) == true) {
                        $files = $files[$this->g('name')];
                        $sections = array('name', 'type', 'tmp_name', 'error', 'size');
                        foreach ($sections as $section) {
                            foreach ($files[$section] as $key => $value) {
                                if (isset($files[$key]) == false) foreach ($sections as $s) $files[$key][$s] = array();
                                if (is_array($value) == false) {
                                    $files[$key][$section] = $value;
                                    continue;
                                }
                                foreach ($value as $k => $v) $files[$key][$section][$k] = $v;
                            }
                            unset($files[$section]);
                        }
                    }
                    if (isset($data[$this->g('name')]) == true) {
                        $data = $data[$this->g('name')];
                    } else {
                        $data = array();
                    }
                    $data['files'] = $files;
                    $this->_value = $data;
                    $this->validate();
                    break;
                case 'markup':
                case 'elements':
                    if (is_null($this->$property) == true) $result = parent::get($target, $property, array())->s('object', $this);
                    break;
            }
            return parent::get($target, $property, $query);
        }

        public function validate($data = null) {
            if ($this->g('validated') == true) return $this;
            if (is_null($data) == true) {
                $data = array();
                switch ($this->g('method')) {
                    case self::METHOD_GET : if (isset($_GET [$this->g('name')]) == true) $data = $_GET [$this->g('name')]; break;
                    case self::METHOD_POST:
                        if (isset($_POST[$this->g('name')]) == true) $data = $_POST[$this->g('name')];
                        $files = array();
                        foreach ($this->g('elements') as $element) {
                            if ($element->g('e.type') != \PCS\Form\Thing::TYPE_ELEMENT_FILE) continue;
                            if (isset($_FILES[$this->g('name')]['name'][$element->g('e.name')]) == false) continue;
                            $files[$element->g('e.name')] = array(
                                'name'     => $_FILES[$this->g('name')]['name'    ][$element->g('e.name')],
                                'type'     => $_FILES[$this->g('name')]['type'    ][$element->g('e.name')],
                                'tmp_name' => $_FILES[$this->g('name')]['tmp_name'][$element->g('e.name')],
                                'error'    => $_FILES[$this->g('name')]['error'   ][$element->g('e.name')],
                                'size'     => $_FILES[$this->g('name')]['size'    ][$element->g('e.name')]
                            );
                        }
                        $data['files'] = $files;
                        break;
                }
                $this->s('value', $data);
            }
            $data = $this->g('value');
            $files = array();
            if (isset($data['files']) == true) $files = $data['files'];
            unset($data['files']);
            if ((empty($data) == true) && (empty($files) == true)) return $this;
            foreach ($this->g('elements') as $element) {
                $element = $element->g('e');
                switch ($element->g('type')) {
                    case \PCS\Form\Thing::TYPE_ELEMENT_FILE:
                        if (isset($files[$element->g('name')]) == true) {
                            $element->s('value', $files[$element->g('name')]);
                        }
                    default:
                        if (isset($data[$element->g('name')]) == false) break;
                        $value = $data[$element->g('name')];
                        $ts = array(
                            \PCS\Form\Thing::TYPE_ELEMENT_SELECT,
                            \PCS\Form\Thing::TYPE_ELEMENT_RADIO
                        );
                        if (in_array($element->g('type'), $ts, true) == true) {
                            if (is_null($element->g('options')->search($value)) == true) {
                                print $element->g('fullname') . '<br />';
                                print 'Option were not found:<br />';
                                var_dump($value);
                                die();
                            }
                            $value = $element->g('options')->search($value)->g('value');
                        }
                        $element->s('value', $value);
                }
                $element->validate();
            }
            $this->s('validated', true);
            if ($this->isValid() == true) $this->process();
            return $this;
        }

        public function isValid() {
            $this->validate();
            $data = $this->g('value');
            $files = array();
            if (isset($data['files']) == true) {
                $files = $data['files'];
                unset($data['files']);
            }
            if ((empty($data) == true) && (empty($files) == true)) return false;
            foreach ($data  as $name => $value) if ($this->h('elements.' . $name) == false) return false;
            foreach ($files as $name => $value) if ($this->h('elements.' . $name) == false) return false;
            return ($this->g('errors.count.current') == 0);
        }
        
        public function process() {
            return $this;
        }
        
    }