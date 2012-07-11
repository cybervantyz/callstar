<?php

    namespace PCS\Form;

    class Thing extends \PCS\Entity {

        const TYPE_FORM             = 'form'    ;
        const TYPE_FIELDSET         = 'fieldset';
        const TYPE_ELEMENT_TEXT     = 'text'    ;
        const TYPE_ELEMENT_PASSWORD = 'password';
        const TYPE_ELEMENT_TEXTAREA = 'textarea';
        const TYPE_ELEMENT_SELECT   = 'select'  ;
        const TYPE_ELEMENT_CHECKBOX = 'checkbox';
        const TYPE_ELEMENT_RADIO    = 'radio'   ;
        const TYPE_ELEMENT_HIDDEN   = 'hidden'  ;
        const TYPE_ELEMENT_COMPLEX  = 'complex' ;
        const TYPE_ELEMENT_LINK     = 'link'    ;
        const TYPE_ELEMENT_FILE     = 'file'    ;
        const TYPE_ELEMENT_SPACE    = 'space'   ;
        const TYPE_ELEMENT_ENTRY    = 'entry'   ;
        const TYPE_CONTROL_SUBMIT   = 'submit'  ;
        const TYPE_CONTROL_BUTTON   = 'button'  ;
        
        protected static $_delayed = array(
            'errors' => array('Error', 'Collection')
        );
        
        protected $_masterform;
        protected $_errors;
        
        protected $_type;
        protected $_name;
        protected $_value;
        
        protected $_decorated = false;
        protected $_validated = false;
        
        protected function get($target, $property, $query) {
            switch ($target) {
                case 'fullname':
                    if ($this->h('masterform') == true) return $this->g('masterform.fullname') . '[' . $this->g('name') . ']';
                    return parent::get('name', '_name', array());
                    break;
                case 'id':
                    return str_replace(array('[', ']'), array('_', ''), $this->g('fullname'));
            }
            return parent::get($target, $property, $query);
        }
        
        public function validate() {
            $this->s('validated', true);
            return $this;
        }
        
    }